<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SectionsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['name' => 'Fashion', 'slug' => 'fashion', 'image' => 'https://picsum.photos/seed/fashion/400/500', 'count_display' => '1.2k', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Beauty', 'slug' => 'beauty', 'image' => 'https://picsum.photos/seed/beauty/400/500', 'count_display' => '890', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Tech', 'slug' => 'tech', 'image' => 'https://picsum.photos/seed/tech/400/500', 'count_display' => '650', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'Travel', 'slug' => 'travel', 'image' => 'https://picsum.photos/seed/travel/400/500', 'count_display' => '720', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('testimonials') && DB::table('testimonials')->count() === 0) {
            DB::table('testimonials')->insert([
                ['quote' => 'Super easy to find creators that fit our brand. Saved us so much time.', 'name' => 'Demo User', 'role' => 'Brand Manager', 'avatar' => 'https://picsum.photos/seed/t1/80/80', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['quote' => 'The platform is intuitive and the creators are professional. Highly recommend.', 'name' => 'Demo Client', 'role' => 'Marketing Lead', 'avatar' => 'https://picsum.photos/seed/t2/80/80', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['quote' => 'We scaled our influencer campaigns 3x in six months. Game changer.', 'name' => 'Demo Founder', 'role' => 'Founder', 'avatar' => 'https://picsum.photos/seed/t3/80/80', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('faqs') && DB::table('faqs')->count() === 0) {
            DB::table('faqs')->insert([
                ['question' => 'What is influencer marketing?', 'answer' => 'Influencer marketing is a form of marketing where brands collaborate with social media influencers and content creators to promote products or services.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['question' => 'How does StarJD work?', 'answer' => 'StarJD connects brands with vetted creators. Brands search by platform, category, and budget; message creators; and book packages. Payment is held securely until content is delivered.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['question' => 'How much does it cost?', 'answer' => 'Costs vary by creator reach and platform. Many creators offer packages starting under $250. Use our search filters to find creators within your budget.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['question' => 'Which platforms are supported?', 'answer' => 'We support Instagram, TikTok, YouTube, Twitter, and UGC (user-generated content) creators.', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('steps') && DB::table('steps')->count() === 0) {
            DB::table('steps')->insert([
                ['title' => 'Search Creators', 'desc' => 'Browse thousands of vetted creators. Filter by niche, location, and budget.', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Chat & Book', 'desc' => 'Message creators directly and book packages. Payment is held securely until delivery.', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Get Content', 'desc' => 'Receive high-quality content on time. Approve and download from your dashboard.', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Schema::hasTable('videos') && DB::table('videos')->count() === 0) {
            DB::table('videos')->insert([
                ['video_id' => 'CaZxAzb7PPM', 'title' => 'Influencer Marketing Guide', 'desc' => 'Learn how to run effective influencer campaigns.', 'embed_url' => 'https://www.youtube.com/embed/CaZxAzb7PPM', 'watch_url' => 'https://www.youtube.com/watch?v=CaZxAzb7PPM', 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['video_id' => 'K1ImD48UqNQ', 'title' => 'Creator Success Stories', 'desc' => 'Hear from brands and creators who found success.', 'embed_url' => 'https://www.youtube.com/embed/K1ImD48UqNQ', 'watch_url' => 'https://www.youtube.com/watch?v=K1ImD48UqNQ', 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
                ['video_id' => 'c64h-K74bFI', 'title' => 'UGC & Brand Collabs', 'desc' => 'How to create authentic UGC content with brands.', 'embed_url' => 'https://www.youtube.com/embed/c64h-K74bFI', 'watch_url' => 'https://www.youtube.com/watch?v=c64h-K74bFI', 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
                ['video_id' => '5qap5aO4i9A', 'title' => 'How to Find the Right Creators', 'desc' => 'Tips for discovering creators that fit your brand.', 'embed_url' => 'https://www.youtube.com/embed/5qap5aO4i9A', 'watch_url' => 'https://www.youtube.com/watch?v=5qap5aO4i9A', 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
                ['video_id' => 'jNQXAC9IVRw', 'title' => 'Pricing & Negotiation Tips', 'desc' => 'Setting rates and working with budgets.', 'embed_url' => 'https://www.youtube.com/embed/jNQXAC9IVRw', 'watch_url' => 'https://www.youtube.com/watch?v=jNQXAC9IVRw', 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
                ['video_id' => '9bZkp7q19f0', 'title' => 'Content Brief Best Practices', 'desc' => 'Writing briefs that get great content.', 'embed_url' => 'https://www.youtube.com/embed/9bZkp7q19f0', 'watch_url' => 'https://www.youtube.com/watch?v=9bZkp7q19f0', 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
