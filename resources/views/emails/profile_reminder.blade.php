@component('mail::message')
# Complete Your Creator Profile

Hi **{{ $name }}**,\

We noticed that your creator profile is still missing some important details. A complete profile helps you get discovered by brands and unlock more opportunities.

@component('mail::button', ['url' => $profileUrl])
Complete Your Profile
@endcomponent

**Why complete it?**
- Appear in search results for brands.
- Increase trust with a detailed bio and category.
- Unlock access to premium features.

If you need any help, just reply to this email.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
