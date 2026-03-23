@extends('layouts.app')

@section('title', config('app.name', 'StarJD') . ' — Connect. Create. Collaborate.')
@section('description', 'Connect with creators. Build your brand. StarJD helps brands find vetted creators and creators get discovered.')

@section('content')
    <div id="app">
        {{-- Server-rendered fallback footer (replaced by Vue after mount) --}}
        <div id="footer-seo-grid" class="mt-auto border-t border-[#334155] bg-[#0f172a] py-12 px-4 lg:px-8">
            <h4 class="mb-6 text-center text-sm font-bold uppercase tracking-widest text-white">Our Pages</h4>
            <div class="mx-auto max-w-7xl grid grid-cols-2 gap-x-6 gap-y-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach($pages as $page)
                    <a href="{{ url('/page/' . $page->slug) }}" class="text-[11px] font-medium text-[#94a3b8] transition-colors hover:text-white truncate" title="{{ $page->title }}">{{ $page->title }}</a>
                @endforeach
            </div>
            
            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-[#334155] pt-8 md:flex-row">
                <p class="text-sm text-[#64748b]">© {{ date('Y') }} StarJD, powered by Suganta International. All rights reserved.</p>
                <div class="flex flex-wrap justify-center gap-6 text-sm">
                    <a href="{{ url('/about') }}" class="text-[#94a3b8] hover:text-white">About</a>
                    <a href="{{ url('/contact') }}" class="text-[#94a3b8] hover:text-white">Contact</a>
                    <a href="{{ url('/privacy') }}" class="text-[#94a3b8] hover:text-white">Privacy Policy</a>
                    <a href="{{ url('/terms') }}" class="text-[#94a3b8] hover:text-white">Terms of Service</a>
                    <a href="{{ url('/child-safety') }}" class="text-[#94a3b8] hover:text-white">Child Safety</a>
                </div>
            </div>
        </div>
    </div>
@endsection
