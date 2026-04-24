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
                    <h1 class="mt-8 text-center text-3xl font-black text-[#1a1a1a] sm:text-4xl">{{ $seo['title'] }}</h1>
                    <p class="mt-4 max-w-2xl text-center text-lg text-[#64748b]">{{ $seo['description'] ?? '' }}</p>
                @endif

                <p class="mt-6 text-sm font-medium text-[#e63946] animate-pulse">Loading your premium experience...</p>

                <div class="mt-8 w-full max-w-4xl space-y-4">
                    <div class="h-14 animate-pulse rounded-2xl bg-white shadow-sm ring-1 ring-[#e2e8f0]"></div>
                    <div class="h-44 animate-pulse rounded-3xl bg-white shadow-sm ring-1 ring-[#e2e8f0]"></div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                        <div class="h-32 animate-pulse rounded-2xl bg-white shadow-sm ring-1 ring-[#e2e8f0]"></div>
                        <div class="h-32 animate-pulse rounded-2xl bg-white shadow-sm ring-1 ring-[#e2e8f0]"></div>
                        <div class="h-32 animate-pulse rounded-2xl bg-white shadow-sm ring-1 ring-[#e2e8f0]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
