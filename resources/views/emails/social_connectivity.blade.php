@component('mail::message')
# Connect Your Social Accounts

Hi **{{ $name }}**,\

Your creator profile looks great! To boost your visibility to brands, please connect your social media accounts (Instagram, YouTube, TikTok, etc.).

@component('mail::button', ['url' => $connectUrl])
Connect Social Accounts
@endcomponent

**Benefits of connecting:**
- Show your audience reach.
- Increase trust with brands.
- Unlock more collaboration opportunities.

Need help? Just reply to this email.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
