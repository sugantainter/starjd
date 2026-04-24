@extends('layouts.app')

@php
    $metaTitle = $seo['title'] ?? (config('app.name', 'StarJD') . ' — Connect. Create. Collaborate.');
    $metaDescription = $seo['description'] ?? 'Connect with creators. Build your brand. StarJD helps brands find vetted creators and creators get discovered.';
    $metaKeywords = $seo['keywords'] ?? '';
    $canonical = $seo['canonical'] ?? url()->current();
@endphp

@section('title', $metaTitle)
@section('description', $metaDescription)
@section('keywords', $metaKeywords)
@section('canonical', $canonical)

@section('content')
    <div id="app">
        {{-- Server-rendered fallback while Vue app boots --}}
        <div class="min-h-[70vh] bg-[#fafaf9] px-4 py-10">
            <div class="mx-auto flex max-w-6xl flex-col items-center">
                <img
                    src="/logo.png"
                    alt="StarJD"
                    class="h-14 w-auto object-contain opacity-95 sm:h-16"
                    onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('hidden');"
                />
                <span class="hidden text-2xl font-bold text-[#1a1a1a]">StarJD</span>
                
                @if(isset($seo['title']))
                    <h1 class="mt-8 text-center text-4xl font-black text-[#1a1a1a] sm:text-5xl lg:text-6xl tracking-tight">
                        {{ $seo['title'] }}
                    </h1>
                    <p class="mt-6 max-w-3xl text-center text-lg md:text-xl text-[#64748b] leading-relaxed">
                        {{ $seo['description'] ?? '' }}
                    </p>
                    
                    @if(isset($seo['content']) && $seo['content'])
                        <div class="prose prose-lg mt-16 w-full max-w-4xl border-t border-gray-100 pt-16 text-left text-[#1a1a1a]">
                            {!! $seo['content'] !!}
                        </div>
                    @endif

                    @if(!empty($seo['creator_cards']))
                        <div class="mt-14 w-full max-w-5xl border-t border-gray-100 pt-10">
                            <h2 class="text-center text-2xl font-bold text-[#1a1a1a]">
                                Active creators in {{ $seo['location_name'] ?? 'this location' }}
                            </h2>
                            <p class="mt-3 text-center text-sm text-[#64748b]">
                                {{ number_format($seo['creator_count'] ?? count($seo['creator_cards'])) }} verified profiles currently available for collaboration.
                            </p>
                            <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                                @foreach($seo['creator_cards'] as $creator)
                                    <a
                                        href="{{ url('/creator-profile/' . $creator['slug']) }}"
                                        class="rounded-2xl border border-gray-100 bg-white p-5 text-left shadow-sm transition hover:border-[#e63946]/40 hover:shadow-md"
                                    >
                                        <h3 class="text-base font-bold text-[#1a1a1a]">{{ $creator['name'] }}</h3>
                                        <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-[#e63946]">{{ $creator['category'] }}</p>
                                        <p class="mt-3 line-clamp-2 text-sm text-[#64748b]">{{ $creator['tagline'] }}</p>
                                        @if(!empty($creator['min_rate']))
                                            <p class="mt-3 text-sm font-semibold text-[#1a1a1a]">Starting at Rs. {{ number_format((float)$creator['min_rate'], 0) }}</p>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    <h1 class="mt-8 text-center text-4xl font-black text-[#1a1a1a] sm:text-5xl tracking-tight">
                        Connect. Create. Collaborate.
                    </h1>
                    <p class="mt-6 max-w-2xl text-center text-lg text-[#64748b]">
                        Join India's premium influencer marketplace. Connect with vetted creators and professional content studios.
                    </p>
                @endif

                <div class="mt-10 flex flex-wrap justify-center gap-4">
                    <div class="rounded-2xl bg-[#e63946] px-8 py-4 text-sm font-bold text-white shadow-xl shadow-[#e63946]/20">
                        Join StarJD
                    </div>
                    <div class="rounded-2xl border border-[#e2e8f0] bg-white px-8 py-4 text-sm font-bold text-[#1a1a1a]">
                        Explore Marketplace
                    </div>
                </div>

                <div class="mt-16 w-full max-w-5xl">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div class="rounded-[2.5rem] bg-white p-10 shadow-2xl shadow-gray-200/50 ring-1 ring-gray-100">
                             <h2 class="text-xl font-bold text-[#1a1a1a]">For Creators</h2>
                             <p class="mt-4 text-sm text-[#64748b] leading-relaxed">Get discovered by top brands, join exclusive campaigns, and grow your personal brand with professional tools.</p>
                             <div class="mt-6 h-1 w-20 rounded-full bg-[#e63946]"></div>
                        </div>
                        <div class="rounded-[2.5rem] bg-white p-10 shadow-2xl shadow-gray-200/50 ring-1 ring-gray-100">
                             <h2 class="text-xl font-bold text-[#1a1a1a]">For Brands</h2>
                             <p class="mt-4 text-sm text-[#64748b] leading-relaxed">Find and hire vetted creators across all social platforms. Manage collaborations and get high-performing content.</p>
                             <div class="mt-6 h-1 w-20 rounded-full bg-emerald-500"></div>
                        </div>
                    </div>
                </div>

                @if($pages && $pages->count() > 0)
                    <div class="mt-20 w-full max-w-5xl border-t border-gray-100 pt-16">
                        <h3 class="text-center text-xs font-black uppercase tracking-[0.3em] text-[#e63946]">Trending Hubs</h3>
                        <div class="mt-10 grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-6">
                            @foreach($pages as $p)
                                <a href="{{ $p->publicPath() }}" class="rounded-xl border border-gray-100 bg-white/50 px-4 py-3 text-center text-[10px] font-bold text-[#64748b] transition hover:border-[#e63946]/30 hover:text-[#e63946]">
                                    {{ $p->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@if(!empty($seo['schema']) && is_array($seo['schema']))
    @push('scripts')
        @foreach($seo['schema'] as $schemaBlock)
            <script type="application/ld+json">{!! json_encode($schemaBlock, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endforeach
    @endpush
@endif
