<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSetting;
use App\Models\Partner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SectionsController extends Controller
{
    public function index(): \Illuminate\Http\JsonResponse
    {
        $categories = [];
        $testimonials = [];
        $faqs = [];
        $steps = [];
        $hero = [];
        $partners = [];

        if (\Schema::hasTable('hero_settings')) {
            $hero = HeroSetting::getForPublic();
        }

        if (\Schema::hasTable('partners')) {
            $partners = Partner::orderBy('sort_order')->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'logo_url' => $p->logo_url,
                'link' => $p->link,
            ])->toArray();
        }

        if (\Schema::hasTable('categories')) {
            $categories = DB::table('categories')->orderBy('sort_order')->get()->map(fn ($r) => [
                'name' => $r->name,
                'count' => $r->count_display ?? '0',
                'image' => $r->image ? asset('storage/' . $r->image) : 'https://picsum.photos/seed/' . Str::slug($r->name) . '/400/500',
            ])->toArray();
        }
        if (\Schema::hasTable('testimonials')) {
            $testimonials = DB::table('testimonials')->orderBy('sort_order')->get()->map(fn ($r) => [
                'quote' => $r->quote,
                'name' => $r->name,
                'role' => $r->role,
                'avatar' => $r->avatar ? asset('storage/' . $r->avatar) : 'https://picsum.photos/seed/' . $r->id . '/80/80',
            ])->toArray();
        }
        if (\Schema::hasTable('faqs')) {
            $faqs = DB::table('faqs')->orderBy('sort_order')->get()->map(fn ($r) => [
                'question' => $r->question,
                'answer' => $r->answer,
            ])->toArray();
        }
        if (\Schema::hasTable('steps')) {
            $steps = DB::table('steps')->orderBy('sort_order')->get()->map(fn ($r) => [
                'title' => $r->title,
                'desc' => $r->desc,
            ])->toArray();
        }

        return response()->json([
            'categories' => $categories,
            'testimonials' => $testimonials,
            'faqs' => $faqs,
            'steps' => $steps,
            'hero' => $hero,
            'partners' => $partners,
        ]);
    }
}
