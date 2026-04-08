@component('mail::message')
<div style="margin:-8px 0 18px 0; border-radius:18px; overflow:hidden; border:1px solid #e2e8f0;">
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 55%,#334155 100%); padding:22px 18px; text-align:center;">
        <img src="{{ url('/logo.png') }}" alt="StarJD" style="height:48px; width:auto; max-width:180px;">
        <p style="margin:10px 0 0 0; color:#cbd5e1; font-size:12px; letter-spacing:.3px;">Creator • Brand • Studio • Professional</p>
    </div>
    <div style="background:#fff7ed; border-top:1px solid #fed7aa; padding:10px 14px; text-align:center; color:#9a3412; font-size:12px; font-weight:700;">
        Limited-time onboarding offer is active
    </div>
</div>

# Activate Your {{ $roleTitle ?? 'Account' }} – Special Offer Inside

Hi **{{ $name }}**,\

@php
    $roleCopy = match($roleSlug ?? null) {
        'creator' => 'activate your creator profile and start getting discovered by brands',
        'brand' => 'unlock your brand dashboard and start posting campaigns',
        'studio_owner' => 'activate your studio owner account and start receiving bookings',
        'professional' => 'activate your professional account and list your services',
        'agency' => 'activate your agency account and access collaboration tools',
        default => 'activate your account and unlock all platform features',
    };
@endphp

We noticed you have not completed the payment step to {{ $roleCopy }}.

@component('mail::button', ['url' => $paymentUrl])
Complete Payment
@endcomponent

<div style="margin:14px 0; border:1px dashed #fdba74; border-radius:12px; background:#fff7ed; padding:12px 14px;">
    <p style="margin:0 0 5px 0; color:#7c2d12; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.4px;">Special Offer</p>
    <p style="margin:0; color:#9a3412; font-size:14px;">
        Use coupon code <strong style="color:#ea580c; letter-spacing:.6px;">{{ $couponCode ?? 'WELCOME20' }}</strong> to get a discount on your first plan.
    </p>
</div>

If you have any questions, just reply to this email – we're here to help.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
