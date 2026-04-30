<?php

namespace App\Http\Controllers\Professional;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\AIUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AISuggestionController extends Controller
{
    public function suggest(Request $request)
    {
        $type = $request->input('type');
        $context = $request->input('context', []);
        
        $prompt = "";
        
        switch ($type) {
            case 'creator_tagline':
                $name = $context['name'] ?? 'a professional';
                $category = $context['category'] ?? 'creative';
                $prompt = "Generate a short, catchy professional tagline (max 8 words) for a creator named '{$name}' in the category '{$category}'. Return only the tagline.";
                break;
            case 'creator_bio':
                $name = $context['name'] ?? 'a professional';
                $category = $context['category'] ?? 'creative';
                $tagline = $context['tagline'] ?? '';
                $prompt = "Write a professional, direct, and engaging bio (max 60 words) for a creator named '{$name}' in the category '{$category}'. " . ($tagline ? "Their tagline is '{$tagline}'." : "") . " Keep it professional and suitable for a talent marketplace. Avoid flowery language. Return only the bio.";
                break;
            case 'brand_bio':
                $name = $context['company_name'] ?? 'our brand';
                $prompt = "Write a professional and authoritative brand bio (max 80 words) for a company named '{$name}' that collaborates with creators and influencers. Keep it direct and business-like. Return only the bio.";
                break;
            case 'campaign_description':
                $brand = $context['company_name'] ?? 'Our Brand';
                $campaignType = $context['campaign_type'] ?? 'marketing';
                if (!empty($context['current_description'])) {
                    $current = $context['current_description'];
                    $prompt = "Improve and professionalize the following campaign description for a '{$brand}' campaign on '{$campaignType}'. Make it clean, engaging, and direct. Limit to 150 words. Return only the improved description without any conversational intro. Current description: \"{$current}\"";
                } else {
                    $prompt = "Write a clean and direct campaign description for a '{$brand}' campaign on '{$campaignType}'. Describe the goals and what we need from creators. Limit to 150 words. Return only the description without any conversational intro.";
                }
                break;
            case 'studio_description':
                $name = $context['name'] ?? 'Our Studio';
                $category = $context['category'] ?? 'Photography';
                $prompt = "Write a professional studio description for '{$name}', a '{$category}' studio. Focus on equipment and atmosphere. Max 80 words. Return only the description.";
                break;
            case 'package_description':
                $name = $context['name'] ?? 'Service Package';
                $category = $context['category'] ?? 'Creative Service';
                $prompt = "Write a clean, professional, and concise description for a service package called '{$name}' for '{$category}'. Focus on deliverables and value. Max 60 words. Avoid fluff. Return only the description.";
                break;
            default:
                return response()->json(['error' => 'Invalid suggestion type'], 400);
        }

        $suggestion = $this->generateWithAI($prompt, $type);
        
        if (!$suggestion) {
            return response()->json(['error' => 'Daily AI limit reached or AI service unavailable.'], 429);
        }

        return response()->json(['suggestion' => $suggestion]);
    }

    private function checkUsageLimit(): bool
    {
        $user = Auth::user();
        if (!$user) return true; // System usage or guest (allow for now)

        $limit = config('services.ai_daily_limit', 15);
        
        $usageCount = AIUsage::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();
            
        return $usageCount < $limit;
    }

    private function generateWithAI(string $prompt, string $type = 'general'): ?string
    {
        if (!$this->checkUsageLimit()) {
            Log::warning('AI Usage Limit Reached for user: ' . (Auth::user()->id ?? 'Guest'));
            return null;
        }

        $openaiKey = config('services.openai.api_key');
        $geminiKey = config('services.gemini.api_key');
        $user = Auth::user();

        if ($openaiKey) {
            try {
                $response = Http::withToken($openaiKey)->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.7
                ]);
                
                if ($response->successful()) {
                    $json = $response->json();
                    $content = $json['choices'][0]['message']['content'] ?? null;
                    
                    if ($content) {
                        AIUsage::create([
                            'user_id' => $user ? $user->id : null,
                            'provider' => 'openai',
                            'model' => 'gpt-4o-mini',
                            'type' => $type,
                            'prompt_tokens' => $json['usage']['prompt_tokens'] ?? 0,
                            'completion_tokens' => $json['usage']['completion_tokens'] ?? 0,
                            'total_tokens' => $json['usage']['total_tokens'] ?? 0,
                        ]);
                    }
                    return $content;
                } else {
                    Log::error('OpenAI Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('OpenAI Exception: ' . $e->getMessage());
            }
        }

        if ($geminiKey) {
            try {
                $response = Http::timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);
                
                if ($response->successful()) {
                    $json = $response->json();
                    $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    
                    if ($content) {
                        $usage = $json['usageMetadata'] ?? [];
                        AIUsage::create([
                            'user_id' => $user ? $user->id : null,
                            'provider' => 'gemini',
                            'model' => 'gemini-2.5-flash',
                            'type' => $type,
                            'prompt_tokens' => $usage['promptTokenCount'] ?? 0,
                            'completion_tokens' => $usage['candidatesTokenCount'] ?? 0,
                            'total_tokens' => $usage['totalTokenCount'] ?? 0,
                        ]);
                    }
                    return $content;
                } else {
                    Log::error('Gemini Error: ' . $response->body());
                }
            } catch (\Exception $e) {
                Log::error('Gemini Exception: ' . $e->getMessage());
            }
        }

        Log::warning('AI Suggestion: No API keys configured or calls failed.');
        return null;
    }

    private function cleanJsonResponse(string $text): string
    {
        // Remove markdown code blocks if present
        $text = preg_replace('/^```(?:json)?\n?/', '', $text);
        $text = preg_replace('/\n?```$/', '', $text);
        return trim($text);
    }

    private $categories = [
        'graphic-video-editors' => [
            'titles' => [
                'I will edit professional cinematic videos for your YouTube or Brand',
                'I will design 5 high-converting social media graphics and banners',
                'I will provide professional color grading and video post-production',
                'I will create custom motion graphics and intro animations'
            ],
            'descriptions' => "Are you looking for a video that truly stands out? I provide professional video editing services focused on storytelling and cinematic quality.\n\nWhat I offer:\n- High-quality 4K editing\n- Professional color grading\n- Sound design & royalty-free music\n- Seamless transitions and motion graphics\n- Unlimited revisions to ensure satisfaction\n\nPerfect for YouTubers, Real Estate agents, and Corporate brands who want to elevate their visual content.",
            'tags' => ['Video Editing', 'Color Grading', 'Motion Graphics', 'Post Production', 'Cinematic'],
            'pricing' => [
                'Basic' => ['desc' => 'Short video edit (up to 2 mins) with basic color correction.', 'features' => ['1080p Export' => true, 'Color Grading' => true]],
                'Standard' => ['desc' => 'Full video edit (up to 10 mins) with motion graphics and sound design.', 'features' => ['4K Export' => true, 'Color Grading' => true, 'Motion Graphics' => true, 'Sound Design' => true]],
                'Premium' => ['desc' => 'High-end cinematic production with advanced VFX and unlimited revisions.', 'features' => ['4K Export' => true, 'Advanced VFX' => true, 'Sound Design' => true, 'Unlimited Revisions' => true]]
            ],
            'faqs' => [
                ['question' => 'What software do you use?', 'answer' => 'I primarily use Adobe Premiere Pro, After Effects, and DaVinci Resolve for professional results.'],
                ['question' => 'How do I send my footage?', 'answer' => 'You can share your footage via Google Drive, Dropbox, or WeTransfer.']
            ]
        ],
        'social-media-managers' => [
            'titles' => [
                'I will manage your Instagram and Facebook growth for 30 days',
                'I will create a strategic social media content calendar and strategy',
                'I will grow your social media presence with organic engagement',
                'I will be your professional social media manager and content creator'
            ],
            'descriptions' => "Managing social media shouldn't be a full-time job for you. I will handle your digital presence with a data-driven strategy designed to grow your audience and engagement.\n\nMy services include:\n- Strategic Content Planning\n- Daily high-quality posts & stories\n- Community engagement & comment management\n- Hashtag research and SEO optimization\n- Detailed monthly performance reports\n\nLet's turn your social media followers into loyal customers.",
            'tags' => ['Social Media Manager', 'Instagram Growth', 'Content Strategy', 'Marketing', 'Automation'],
            'pricing' => [
                'Basic' => ['desc' => 'Management of 1 platform for 7 days (3 posts/week).', 'features' => ['Basic Engagement' => true, 'Hashtag Research' => true]],
                'Standard' => ['desc' => 'Management of 2 platforms for 15 days (5 posts/week).', 'features' => ['Advanced Engagement' => true, 'Content Planning' => true, '2 Platforms' => true]],
                'Premium' => ['desc' => 'Full management of 3 platforms for 30 days with daily posts.', 'features' => ['Daily Posting' => true, 'Monthly Reports' => true, 'Content Creation' => true]]
            ],
            'faqs' => [
                ['question' => 'Do you create the content?', 'answer' => 'Yes, I create all graphics, captions, and Reels as part of the management service.'],
                ['question' => 'Do I need to provide access?', 'answer' => 'Yes, you will need to grant me editor access to your social media accounts.']
            ]
        ],
        'content-writers' => [
            'titles' => [
                'I will write 5 SEO-optimized blog posts for your niche',
                'I will write compelling sales copy for your landing page',
                'I will create professional script for your next viral video',
                'I will provide high-quality website content and about us pages'
            ],
            'descriptions' => "Words have the power to sell. I provide professional copywriting and content writing services that focus on clarity, engagement, and SEO.\n\nWhat I can do for you:\n- SEO-optimized articles and blog posts\n- High-converting sales copy\n- Engaging video and podcast scripts\n- Technical and creative writing\n- Professional proofreading and editing\n\nAll content is 100% original, AI-detection free, and tailored to your brand voice.",
            'tags' => ['Copywriting', 'SEO Content', 'Blog Writing', 'Creative Writing', 'Sales Copy'],
            'pricing' => [
                'Basic' => ['desc' => 'One 500-word SEO-optimized article.', 'features' => ['Topic Research' => true, 'SEO Keywords' => true]],
                'Standard' => ['desc' => 'Three 1000-word high-quality blog posts.', 'features' => ['SEO Keywords' => true, 'Reference Links' => true, '3 Articles' => true]],
                'Premium' => ['desc' => 'Full monthly content strategy + 8 long-form articles.', 'features' => ['Content Strategy' => true, 'Unlimited Revisions' => true, 'Meta Descriptions' => true]]
            ],
            'faqs' => [
                ['question' => 'Is the content AI-generated?', 'answer' => 'No, all content is written by me from scratch to ensure a unique brand voice and pass all AI detectors.'],
                ['question' => 'Can you write in different tones?', 'answer' => 'Absolutely. I can adapt my voice to be professional, witty, authoritative, or friendly based on your brand requirements.']
            ]
        ]
    ];

    public function suggestTitle(Request $request): JsonResponse
    {
        $request->validate(['service_id' => 'required']);
        $service = Service::find($request->service_id);
        $slug = $service ? $service->slug : 'default';

        $aiResult = $this->generateWithAI("Generate 3 professional Fiverr-style 'I will' titles for the category: {$service->name}. Return as a comma separated list. No intro.", 'title');
        if ($aiResult) {
            return response()->json(['suggestions' => array_map('trim', explode(',', $aiResult))]);
        }

        $suggestions = $this->categories[$slug]['titles'] ?? [
            'I will provide professional ' . ($service ? $service->name : 'services') . ' for your project',
            'I will be your expert ' . ($service ? $service->name : 'specialist') . ' and deliver results'
        ];

        return response()->json(['suggestions' => $suggestions]);
    }

    public function suggestDescription(Request $request): JsonResponse
    {
        $request->validate([
            'service_id' => 'required',
            'title' => 'nullable|string|max:255'
        ]);
        $service = Service::find($request->service_id);
        $slug = $service ? $service->slug : 'default';
        $prompt = "Write a professional service description for a '{$service->name}' service gig titled '{$request->title}'. Use bullet points for features. Tone: Professional and direct. Limit to 150 words.";

        $aiResult = $this->generateWithAI($prompt, 'description');
        if ($aiResult) {
            return response()->json(['description' => $aiResult]);
        }

        $description = $this->categories[$slug]['descriptions'] ?? "Default professional description...";
        return response()->json(['description' => $description]);
    }

    public function suggestTags(Request $request): JsonResponse
    {
        $request->validate(['service_id' => 'required']);
        $service = Service::find($request->service_id);
        $slug = $service ? $service->slug : 'default';

        $aiResult = $this->generateWithAI("List 5 high-traffic search tags for a service in the category '{$service->name}'. Comma separated. No intro.", 'tags');
        if ($aiResult) {
            return response()->json(['tags' => array_map('trim', explode(',', $aiResult))]);
        }

        return response()->json([
            'tags' => $this->categories[$slug]['tags'] ?? ['Service', 'Professional', 'Quality']
        ]);
    }

    public function suggestPricing(Request $request): JsonResponse
    {
        $request->validate(['service_id' => 'required']);
        $service = Service::find($request->service_id);
        $slug = $service ? $service->slug : 'default';

        $aiResult = $this->generateWithAI("Generate JSON pricing for 3 tiers (Basic, Standard, Premium) for '{$service->name}'. 
            Format: {\"Basic\": {\"desc\": \"...\", \"features\": {\"Key\": true}}, \"Standard\": {...}, \"Premium\": {...}}. 
            Only JSON. No markdown tags.", 'pricing');
        if ($aiResult) {
            $cleaned = $this->cleanJsonResponse($aiResult);
            $json = json_decode($cleaned, true);
            if ($json) return response()->json(['pricing' => $json]);
        }

        return response()->json([
            'pricing' => $this->categories[$slug]['pricing'] ?? []
        ]);
    }

    public function suggestFAQs(Request $request): JsonResponse
    {
        $request->validate(['service_id' => 'required']);
        $service = Service::find($request->service_id);
        $slug = $service ? $service->slug : 'default';

        $aiResult = $this->generateWithAI("Generate 3 professional FAQs in JSON format for '{$service->name}'. 
            Format: [{\"question\": \"...\", \"answer\": \"...\"}]. 
            Only JSON. No markdown tags.", 'faqs');
        if ($aiResult) {
            $cleaned = $this->cleanJsonResponse($aiResult);
            $json = json_decode($cleaned, true);
            if ($json) return response()->json(['faqs' => $json]);
        }

        return response()->json([
            'faqs' => $this->categories[$slug]['faqs'] ?? []
        ]);
    }
}
