<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\ProfessionalProfile;
use App\Models\ServiceListing;
use App\Models\Service;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Professional User
        $user = User::create([
            'name' => 'Alex Professional',
            'email' => 'pro@starjd.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        $proRole = Role::where('slug', 'professional')->first();
        if ($proRole) {
            $user->roles()->attach($proRole->id, ['is_primary' => true]);
        }

        // 2. Create Professional Profile
        ProfessionalProfile::create([
            'user_id' => $user->id,
            'tagline' => 'Full-Stack Developer & Digital Marketing Specialist',
            'bio' => 'Professional developer with over 8 years of experience in building scalable web applications and managing multi-channel digital marketing campaigns. I specialize in React, Laravel, and Meta Ads Management.',
            'languages' => [
                ['name' => 'English', 'level' => 'Native'],
                ['name' => 'Hindi', 'level' => 'Fluent'],
            ],
            'skills' => [
                ['name' => 'Laravel', 'level' => 'Expert'],
                ['name' => 'Vue.js', 'level' => 'Expert'],
                ['name' => 'Facebook Ads', 'level' => 'Expert'],
                ['name' => 'SEO', 'level' => 'Intermediate'],
            ],
            'education' => [
                ['school' => 'Delhi Technological University', 'degree' => 'B.Tech in Computer Science', 'year' => '2016'],
            ],
            'certifications' => [
                ['name' => 'Meta Certified Media Planning Professional', 'from' => 'Meta', 'year' => '2023'],
                ['name' => 'AWS Certified Solutions Architect', 'from' => 'Amazon', 'year' => '2022'],
            ],
            'response_time' => '1 hour',
            'avg_rating' => 4.9,
            'total_reviews' => 124,
        ]);

        // 3. Create Sample Service Listings
        $marketingService = Service::where('slug', 'marketing-advertising-agencies')->first() ?: Service::first();

        ServiceListing::create([
            'user_id' => $user->id,
            'service_id' => $marketingService ? $marketingService->id : 1,
            'title' => 'I will manage your Facebook and Instagram Ad campaigns for high ROI',
            'slug' => 'facebook-instagram-ads-management',
            'description' => 'I will help you reach your target audience and grow your business through professional Meta ad management. I handle everything from audience research to creative setup and daily optimization.',
            'pricing_tiers' => [
                [
                    'name' => 'Basic',
                    'description' => '1 Ad campaign setup + Audience research for 3 days.',
                    'price' => 5000,
                    'delivery' => 3,
                    'revisions' => 1,
                    'features' => ['Audience Research' => true, 'Campaign Setup' => true]
                ],
                [
                    'name' => 'Standard',
                    'description' => '2 Ad campaigns + A/B Testing + Management for 7 days.',
                    'price' => 12000,
                    'delivery' => 7,
                    'revisions' => 3,
                    'features' => ['Audience Research' => true, 'Campaign Setup' => true, 'Management' => true]
                ],
                [
                    'name' => 'Premium',
                    'description' => 'Full monthly management (4+ campaigns) + Weekly reports.',
                    'price' => 25000,
                    'delivery' => 30,
                    'revisions' => 10,
                    'features' => ['Audience Research' => true, 'Campaign Setup' => true, 'Management' => true, 'Reporting' => true]
                ],
            ],
            'faqs' => [
                ['question' => 'Do you provide the ad copies?', 'answer' => 'Yes, I provide high-converting ad copies for all tiers.'],
                ['question' => 'Is the ad spend included in the price?', 'answer' => 'No, the price is for my management services only. You will pay the ad spend directly to Meta.'],
            ],
            'tags' => ['Facebook Ads', 'Instagram Marketing', 'Meta Ads', 'Social Media'],
            'gallery' => [],
            'is_active' => true,
        ]);
    }
}
