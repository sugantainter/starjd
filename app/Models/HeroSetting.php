<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSetting extends Model
{
    protected $fillable = [
        'headline',
        'subheadline',
        'wallpaper_images',
        'cascade_images',
        'btn_creator_label',
        'btn_creator_url',
        'btn_brand_label',
        'btn_brand_url',
        'btn_browse_label',
        'btn_browse_url',
    ];

    protected function casts(): array
    {
        return [
            'wallpaper_images' => 'array',
            'cascade_images' => 'array',
        ];
    }

    public static function getForPublic(): array
    {
        $row = self::first();
        $wallpaper = $row?->wallpaper_images;
        $cascade = $row?->cascade_images;
        if (empty($wallpaper) || ! is_array($wallpaper)) {
            $wallpaper = [
                ['src' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?w=800&q=80'],
                ['src' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&q=80'],
                ['src' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&q=80'],
                ['src' => 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?w=800&q=80'],
                ['src' => 'https://images.unsplash.com/photo-1556761175-b413da4baf72?w=800&q=80'],
            ];
        }
        if (empty($cascade) || ! is_array($cascade)) {
            $cascade = [
                ['src' => 'https://later.com/static/1b915d30aa4b21839dc37f1d8cc31aad/4b779/inside-l.webp', 'alt' => 'Inside left'],
                ['src' => 'https://later.com/static/9ab27d0ea1dad250c3b3139da588a4d3/102bb/middle.webp', 'alt' => 'Middle'],
                ['src' => 'https://later.com/static/447307fa7254533db7b69f9261a8842e/435a7/inside-r.webp', 'alt' => 'Inside right'],
                ['src' => 'https://later.com/static/f789766aa86aec8fde2c5f78ed0b4cf8/7980a/outer-r.webp', 'alt' => 'Outer right'],
                ['src' => 'https://later.com/static/53dc907229ca6b860ea26841d00f39dc/9b018/outer-l.webp', 'alt' => 'Outer left'],
            ];
        }
        return [
            'headline' => $row?->headline ?? 'Influencer Marketing Made Easy',
            'subheadline' => $row?->subheadline,
            'wallpaper_images' => $wallpaper,
            'cascade_images' => $cascade,
            'btn_creator_label' => $row?->btn_creator_label ?? 'Join as Creator',
            'btn_creator_url' => $row?->btn_creator_url ?? '#join-creator',
            'btn_brand_label' => $row?->btn_brand_label ?? 'Join as Brand',
            'btn_brand_url' => $row?->btn_brand_url ?? '#join-brand',
            'btn_browse_label' => $row?->btn_browse_label ?? 'Browse Creators',
            'btn_browse_url' => $row?->btn_browse_url ?? '#featured',
        ];
    }
}
