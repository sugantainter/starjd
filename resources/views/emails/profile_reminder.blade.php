@component('mail::message')
<div style="margin:-8px 0 18px 0; border-radius:18px; overflow:hidden; border:1px solid #e2e8f0;">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 55%,#334155 100%); padding:22px 18px; text-align:center;">
        <img src="{{ url('/logo.png') }}" alt="StarJD" style="height:48px; width:auto; max-width:180px;">
        <p style="margin:10px 0 0 0; color:#cbd5e1; font-size:12px; letter-spacing:.3px;">Build trust with a complete profile</p>
    </div>
    <div style="background:#fff7ed; border-top:1px solid #fed7aa; padding:10px 14px; text-align:center; color:#9a3412; font-size:12px; font-weight:700;">
        Quick profile completion unlocks better visibility
    </div>
</div>

# Complete Your {{ $roleTitle ?? 'Account' }} Profile

Hi **{{ $name }}**,\

@php
    $heading = match($roleSlug ?? null) {
        'creator' => 'your creator profile is still missing some important details',
        'brand' => 'your brand profile is still missing some important details',
        'studio_owner' => 'your studio information is still incomplete',
        'professional' => 'your professional profile needs a few more details',
        'agency' => 'your agency account setup is not fully completed yet',
        default => 'your profile is still missing some important details',
    };
@endphp

We noticed that {{ $heading }}.

@component('mail::button', ['url' => $profileUrl])
Complete Your Profile
@endcomponent

<div style="margin:14px 0 8px 0; border-left:4px solid #fc4402; background:#f8fafc; padding:10px 12px;">
    <p style="margin:0; color:#0f172a; font-size:13px;"><strong>Why complete it?</strong></p>
</div>

@if(($roleSlug ?? null) === 'creator')
- Appear in search results for brands.
- Increase trust with a detailed bio and category.
- Unlock access to premium features.
@elseif(($roleSlug ?? null) === 'brand')
- Build credibility with creators.
- Get better campaign applications.
- Launch collaborations faster.
@elseif(($roleSlug ?? null) === 'studio_owner')
- Show studio details to attract bookings.
- Improve conversion with complete amenities and photos.
- Reduce booking questions with clear setup info.
@else
- Improve your visibility.
- Build more trust on your profile.
- Unlock better collaboration opportunities.
@endif

If you need any help, just reply to this email.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
