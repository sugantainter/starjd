@component('mail::message')
# Activate Your Account – Special Offer Inside

Hi **{{ $name }}**,\

We noticed you haven't completed the payment step to activate your creator profile. Without payment, your profile remains hidden from brands.

@component('mail::button', ['url' => $paymentUrl])
Complete Payment
@endcomponent

**Special Offer:** Use the coupon code **{{ $couponCode ?? 'WELCOME20' }}** to get a discount on your first plan.

If you have any questions, just reply to this email – we're here to help.

Thanks,<br>{{ config('app.name') }} Team
@endcomponent
