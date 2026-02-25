<?php

return [
    /*
    | Platform commission on studio bookings (percentage, e.g. 10 = 10%).
    */
    'booking_commission_percent' => (float) env('STUDIO_BOOKING_COMMISSION_PERCENT', 10),

    /*
    | Cancellation policies (hours before start for full refund).
    | flexible: 24h, moderate: 72h, strict: 7 days = 168h.
    */
    'cancellation' => [
        'flexible' => 24,
        'moderate' => 72,
        'strict' => 168,
    ],
];
