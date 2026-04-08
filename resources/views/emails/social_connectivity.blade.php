@component('mail::message')
<div style="margin:-8px 0 18px 0; border-radius:18px; overflow:hidden; border:1px solid #e2e8f0;">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 55%,#334155 100%); padding:22px 18px; text-align:center;">
        <img src="{{ url('/logo.png') }}" alt="StarJD" style="height:48px; width:auto; max-width:180px;">
        <p style="margin:10px 0 0 0; color:#cbd5e1; font-size:12px; letter-spacing:.3px;">Take your next growth step</p>
    </div>
    <div style="background:#fef2f2; border-top:1px solid #fecaca; padding:10px 14px; text-align:center; color:#9f1239; font-size:12px; font-weight:700;">
        Your account is active - one more step to unlock momentum
    </div>
</div>

@php
    $title = match($roleSlug ?? null) {
        'creator' => 'Connect Your Social Accounts',
        'brand' => 'Launch Your First Campaign',
        'studio_owner' => 'Set Your Studio Availability',
        'professional' => 'Publish Your First Service',
        'agency' => 'Complete Agency Onboarding',
        default => 'Complete Your Next Step',
    };

    $body = match($roleSlug ?? null) {
        'creator' => 'Your creator profile looks great! To boost your visibility to brands, please connect your social media accounts (Instagram, YouTube, TikTok, etc.).',
        'brand' => 'Your account is active. The next step is to launch your first campaign so creators can start applying.',
        'studio_owner' => 'Your studio setup looks good. Add availability slots so brands and creators can book your studio instantly.',
        'professional' => 'You are almost ready. Add your first service listing so clients can discover and hire you.',
        'agency' => 'Your agency account is active. Complete your onboarding tasks to start collaborations faster.',
        default => 'You are almost done. Complete your next onboarding step to unlock full value on the platform.',
    };
@endphp

# {{ $title }}

Hi **{{ $name }}**,\

{{ $body }}

@component('mail::button', ['url' => $connectUrl])
{{ $ctaLabel ?? 'Continue' }}
@endcomponent

**Why do this now?**
@if(($roleSlug ?? null) === 'creator')
- Show your audience reach.
- Increase trust with brands.
- Unlock more collaboration opportunities.
@elseif(($roleSlug ?? null) === 'brand')
- Start receiving creator applications.
- Launch collaborations faster.
- Get measurable marketing outcomes.
@elseif(($roleSlug ?? null) === 'studio_owner')
- Make your studio bookable immediately.
- Reduce back-and-forth booking calls.
- Improve occupancy and revenue.
@else
- Get discovered faster.
- Improve account performance.
- Unlock more opportunities.
@endif

Need help? Just reply to this email.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
