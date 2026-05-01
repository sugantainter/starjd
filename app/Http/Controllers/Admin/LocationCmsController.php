<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoPage;
use App\Models\Area;
use App\Models\Hospital;
use App\Models\Market;
use App\Models\Metro;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App\Models\AIUsage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LocationCmsController extends Controller
{
    /**
     * Generate AI-powered content for a specific location page.
     */
    public function generateAiContent(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'city' => 'nullable|string',
            'type' => 'required|string',
            'topic' => 'nullable|string|max:500',
        ]);

        $name = $request->name;
        $city = $request->city ?: 'Unknown City';
        $type = $request->type;
        $topic = $request->topic;

        $topicContext = $topic ? "Focus the content specifically on this topic: '{$topic}'." : "";

        $prompt = "Act as an SEO expert for StarJD, a professional talent and services marketplace. 
        Generate comprehensive landing page content for '{$name}' which is a '{$type}' in '{$city}'.
        {$topicContext}
        
        Requirements:
        1. Intro Text: A professional 80-word introduction.
        2. Guide Sections: Exactly 3 detailed sections. Each with a 'title' and 'content'.
        3. FAQs: Exactly 3 frequently asked questions with answers.
        
        Return ONLY a JSON object with this structure:
        {
          \"intro_text\": \"...\",
          \"guide_content\": [{\"title\": \"...\", \"content\": \"...\"}, ...],
          \"faqs\": [{\"q\": \"...\", \"a\": \"...\"}, ...]
        }
        Do not include markdown tags or intro text. Just the JSON.";

        $content = $this->callAi($prompt, 'seo_generation');

        if (!$content) {
            return response()->json(['error' => 'AI generation failed or limit reached.'], 429);
        }

        $json = json_decode($this->cleanJsonResponse($content), true);
        
        if (!$json) {
            return response()->json(['error' => 'Invalid AI response format.'], 500);
        }

        return response()->json($json);
    }

    private function callAi(string $prompt, string $type): ?string
    {
        $openaiKey = config('services.openai.api_key');
        $geminiKey = config('services.gemini.api_key');
        $user = Auth::user();

        // Check limit (shared with other AI features)
        $limit = config('services.ai_daily_limit', 15);
        $usageCount = AIUsage::where('user_id', $user->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();
            
        if ($usageCount >= $limit) return null;

        if ($openaiKey) {
            try {
                $response = Http::withToken($openaiKey)->timeout(40)->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [['role' => 'user', 'content' => $prompt]],
                    'temperature' => 0.7
                ]);
                
                if ($response->successful()) {
                    $json = $response->json();
                    $content = $json['choices'][0]['message']['content'] ?? null;
                    if ($content) {
                        AIUsage::create([
                            'user_id' => $user->id,
                            'provider' => 'openai',
                            'model' => 'gpt-4o-mini',
                            'type' => $type,
                            'total_tokens' => $json['usage']['total_tokens'] ?? 0,
                        ]);
                    }
                    return $content;
                }
            } catch (\Exception $e) {}
        }

        if ($geminiKey) {
            try {
                $response = Http::timeout(40)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$geminiKey}", [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);
                
                if ($response->successful()) {
                    $json = $response->json();
                    $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($content) {
                        AIUsage::create([
                            'user_id' => $user->id,
                            'provider' => 'gemini',
                            'model' => 'gemini-2.5-flash',
                            'type' => $type,
                        ]);
                    }
                    return $content;
                }
            } catch (\Exception $e) {}
        }

        return null;
    }

    private function cleanJsonResponse(string $text): string
    {
        $text = preg_replace('/^```(?:json)?\n?/', '', $text);
        $text = preg_replace('/\n?```$/', '', $text);
        return trim($text);
    }
    public function index(Request $request)
    {
        $query = SeoPage::with('entity');
        $query = $this->applyFilters($query, $request);

        $perPage = $request->per_page ? (int) $request->per_page : 20;

        $perPage = $request->per_page ? (int) $request->per_page : 20;
        // if perPage is -1, return all
        if ($perPage === -1) {
            // we can simulate pagination format
            $all = $query->latest()->get();
            return response()->json([
                'data' => $all,
                'total' => $all->count(),
                'per_page' => $all->count() > 0 ? $all->count() : 1,
                'last_page' => 1,
                'from' => 1,
                'to' => $all->count()
            ]);
        }

        return response()->json($query->latest()->paginate($perPage));
    }

    public function show($id)
    {
        return response()->json(SeoPage::with('entity')->findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $page = SeoPage::findOrFail($id);
        $page->update($request->all());
        return response()->json($page);
    }

    public function destroy($id)
    {
        SeoPage::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Bulk Import raw data into SEO Pages.
     */
    public function bulkImport(Request $request)
    {
        set_time_limit(0); // Allow long running import
        ini_set('memory_limit', '512M');

        $request->validate([
            'type' => 'required|in:area,hospital,market,metro,school',
            'ids' => 'nullable|array',
            'state_id' => 'nullable|integer',
            'city_id' => 'nullable|integer',
            'slug_pattern' => 'nullable|string',
            'title_pattern' => 'nullable|string',
        ]);

        $type = $request->type;
        $slugPattern = $request->slug_pattern ?: '{name}-in-{city}';
        $titlePattern = $request->title_pattern ?: '{name} in {city}';

        $modelClass = $this->getModelClass($type);
        
        $query = $modelClass::query();
        if ($request->ids) {
            $query->whereIn('id', $request->ids);
        }
        
        if ($request->state_id) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->city_id) {
            $query->where('city_id', $request->city_id);
        }

        // Avoid duplication
        $query->whereDoesntHave('seoPage');

        $count = 0;
        $query->chunk(100, function ($entities) use ($type, $slugPattern, $titlePattern, &$count) {
            foreach ($entities as $entity) {
                $city = $entity->city ?? 'Unknown City';
                $name = $entity->name;

                $replacements = [
                    '{name}' => $name,
                    '{city}' => $city,
                    '{type}' => ucfirst($type),
                ];

                $finalTitle = str_replace(array_keys($replacements), array_values($replacements), $titlePattern);
                $slugBase = str_replace(array_keys($replacements), array_values($replacements), $slugPattern);
                $cleanSlug = Str::slug($slugBase);
                
                if (SeoPage::where('slug', $cleanSlug)->exists()) {
                    $cleanSlug = $cleanSlug . '-' . Str::random(4);
                }

                SeoPage::create([
                    'entity_type' => get_class($entity),
                    'entity_id' => $entity->id,
                    'type' => $type,
                    'slug' => $cleanSlug,
                    'title' => $finalTitle,
                    'meta_title' => "$finalTitle | StarJD",
                    'meta_description' => "Discover the top $type in $city. View details, ratings, and more about $name on StarJD.",
                    'status' => 'published',
                ]);
                $count++;
            }
        });

        return response()->json([
            'message' => "Successfully imported $count $type pages.",
            'count' => $count
        ]);
    }

    /**
     * Bulk update status or other fields.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'nullable|array',
            'all_matching' => 'nullable|boolean',
            'filters' => 'nullable|array',
            'action' => 'required|string',
            'value' => 'nullable',
            'template_data' => 'nullable|array' // For applying templates
        ]);

        $query = SeoPage::query();

        if ($request->all_matching && $request->filters) {
            $query = $this->applyFilters($query, new Request($request->filters));
        } else {
            $query->whereIn('id', $request->ids);
        }

        switch ($request->action) {
            case 'status':
                $query->update(['status' => $request->value]);
                break;
            case 'delete':
                $query->delete();
                break;
            case 'template':
                $data = $request->template_data;
                $pages = $query->with('entity')->get();
                
                foreach ($pages as $page) {
                    $entity = $page->entity;
                    if (!$entity) continue;

                    $replacements = [
                        '{name}' => $entity->name,
                        '{city}' => $entity->city ?? '',
                        '{type}' => ucfirst($page->type),
                    ];

                    // Helper function to replace in strings or arrays
                    $replace = function ($target) use ($replacements) {
                        if (is_string($target)) {
                            return str_replace(array_keys($replacements), array_values($replacements), $target);
                        }
                        return $target;
                    };

                    $update = [];
                    if (isset($data['intro_text'])) {
                        $update['intro_text'] = $replace($data['intro_text']);
                    }
                    if (isset($data['guide_content'])) {
                        $guide = $data['guide_content'];
                        foreach ($guide as &$section) {
                            $section['title'] = $replace($section['title']);
                            $section['content'] = $replace($section['content']);
                        }
                        $update['guide_content'] = $guide;
                    }
                    if (isset($data['faqs'])) {
                        $faqs = $data['faqs'];
                        foreach ($faqs as &$faq) {
                            $faq['q'] = $replace($faq['q']);
                            $faq['a'] = $replace($faq['a']);
                        }
                        $update['faqs'] = $faqs;
                    }

                    if (isset($data['meta_title'])) {
                        $update['meta_title'] = $replace($data['meta_title']);
                    }
                    if (isset($data['meta_description'])) {
                        $update['meta_description'] = $replace($data['meta_description']);
                    }
                    if (isset($data['meta_keywords'])) {
                        $update['meta_keywords'] = $replace($data['meta_keywords']);
                    }

                    $page->update($update);
                }
                break;
        }

        return response()->json(['success' => true]);
    }

    private function getModelClass($type)
    {
        return match ($type) {
            'area' => Area::class,
            'hospital' => Hospital::class,
            'market' => Market::class,
            'metro' => Metro::class,
            'school' => School::class,
        };
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%");
            });
        }

        if ($request->slug) {
            $query->where('slug', 'like', "%{$request->slug}%");
        }

        if ($request->state_id || $request->city_id) {
            $models = [Area::class, Hospital::class, Market::class, Metro::class, School::class];
            $query->whereHasMorph('entity', $models, function ($q) use ($request) {
                if ($request->state_id) {
                    $q->where('state_id', (int) $request->state_id);
                }
                if ($request->city_id) {
                    $q->where('city_id', (int) $request->city_id);
                }
            });
        }

        return $query;
    }
}
