# Studio Marketplace — Implementation Status

This document maps your professional marketplace checklist to what is implemented and what remains.

---

## ✅ 1. Business Model

| Item | Status | Notes |
|------|--------|--------|
| Two-sided marketplace (Studio Owners + Customers) | ✅ | Roles: `studio_owner`, customer; studio owner panel + public booking flow |
| Commission per booking | ✅ | `config/studio.php` → `booking_commission_percent` (default 10%); used in `BookingService::calculateTotal()` |
| Listing fee / Featured boost | ⚠️ | `studios.is_featured` exists; no paid “featured listing” purchase flow yet |
| Free listing | ✅ | Studios created as draft; no listing fee on create |
| Payment gateway / convenience fee | ⚠️ | Booking amounts calculated; Razorpay/Stripe integration and split payment flow TBD |

---

## ✅ 2. Studio Categories (Scalable)

| Item | Status | Notes |
|------|--------|--------|
| Production: Photography, Film, Podcast, Music, Rehearsal | ✅ | `StudioCategorySeeder` expanded |
| Property: Villa, Farmhouse, Bungalow, Penthouse, Warehouse, Industrial Loft | ✅ | Seeder |
| Event: Banquet Hall, Conference Room, Workshop Space, Rooftop Venue, Art Gallery | ✅ | Seeder |
| Creative: Dance, Yoga, Gym, Makeup Studio | ✅ | Seeder |
| Admin manage categories | ✅ | Admin API: `GET/POST/PUT/DELETE /api/admin/studio-categories` (StudioCategoryController) |
| Public filter by category | ✅ | `StudioPublicController::index()` + FilterSidebar; `/api/studios/categories` |

---

## ✅ 3. Amenities (Dynamic)

| Item | Status | Notes |
|------|--------|--------|
| Basic: AC, WiFi, Parking, Power Backup, Washroom, Changing Room | ✅ | `AmenitySeeder` |
| Equipment: Softbox, Green Screen, DSLR, Microphones, Soundproofing, Props | ✅ | Seeder |
| Add-ons: Photographer, Videographer, Editor, Makeup Artist, Catering | ✅ | Seeder (paid add-on logic TBD) |
| Backend dynamic (amenities table) | ✅ | `amenities` + `studio_amenity` pivot; model `Amenity` |
| Studio owner: add/remove amenities on studio | ✅ | Add Studio form: amenity multi-select; API: `amenity_ids` on create/update |
| Admin: add/remove/categorize amenities | ✅ | Admin API: `GET/POST/PUT/DELETE /api/admin/amenities` (AmenityController); public `/api/amenities` for listing |

---

## ✅ 4. Website Structure (Frontend)

| Page / Feature | Status | Notes |
|----------------|--------|--------|
| Home: hero, featured, categories, how it works, CTA | ⚠️ | Depends on main site; studio-specific home blocks TBD |
| Search: location, price, type, amenities, rating, date, instant book | ✅ | `Studios.vue` + FilterSidebar; sort (price, rating, newest); `available_date` filter |
| Map view | ⚠️ | Placeholder in `Studios.vue`; “Integrate your map provider here” |
| Studio detail: gallery, amenities, rules, cancellation, pricing, calendar, reviews, host, similar | ✅ | `StudioDetail.vue`: gallery, amenities, cancellation policy, pricing, calendar, reviews, similar studios, owner |
| Booking flow: date/time, extras, price breakdown, payment, confirmation | ✅ | `BookingWidget`; calculate API; store → confirm; commission in breakdown |
| Email + WhatsApp notification | ⚠️ | Not implemented |

---

## ✅ 5. Backend Panel Structure

| Panel | Feature | Status | Notes |
|-------|---------|--------|--------|
| Admin | Dashboard (revenue, bookings, users) | ⚠️ | Generic dashboard exists; studio-specific metrics TBD |
| Admin | Manage studios / approve listings | ✅ | Admin > Studio marketplace > Studios: list all, Approve (set active), Inactive, Feature; only **active** studios appear on public marketplace |
| Admin | Manage amenities & categories | ✅ | `GET/POST/PUT/DELETE` studio-categories & amenities under `/api/admin` |
| Admin | Payment logs, refunds, commission, promos, featured | ⚠️ | Commission in config; no admin UI for these |
| Studio Owner | Add studio, images, calendar, price, amenities | ✅ | Add Studio + Edit Studio: basic fields, amenities, images (upload/delete/set primary), availability slots (add/delete) |
| Studio Owner | View bookings, withdraw, analytics | ⚠️ | Bookings list API; withdraw/analytics TBD |
| Customer | My bookings, cancel, reviews, saved studios, invoice | ⚠️ | Booking create/confirm; customer “my bookings” and rest TBD |

---

## ✅ 6. Database (Core Tables)

| Table | Status | Notes |
|-------|--------|--------|
| users | ✅ | RBAC via roles + user_roles |
| studios | ✅ | Includes `cancellation_policy` (added in migration) |
| studio_images | ✅ | Model + relation; studio owner upload/delete/reorder/set primary via Edit Studio |
| studio_categories (categories) | ✅ | Seeder expanded |
| amenities | ✅ | Seeder added |
| studio_amenity (pivot) | ✅ | Synced on studio create/update |
| bookings | ✅ | Status, payment_status, platform_commission, studio_amount, cancellation_policy |
| payments / transactions | ✅ | `Transaction` model; escrow in `BookingService::confirmBooking()` |
| reviews | ✅ | Model + relation; approved scope |
| availability_slots | ✅ | Model + relation |
| featured_listings | ⚠️ | `studios.is_featured` column; no separate table |

Booking logic: `BookingService::validateAvailability()` prevents double booking (overlap check).

---

## ✅ 7. Payment & Legal (India Focus)

| Item | Status | Notes |
|------|--------|--------|
| Razorpay / Stripe | ⚠️ | Config/keys TBD; booking flow ready for gateway |
| Split payments / auto commission | ✅ | Commission calculated; split (studio_amount vs platform) in DB; actual gateway split TBD |
| Terms, Refund, Cancellation, Privacy, Host Agreement | ⚠️ | Legal pages (e.g. `/pages/terms`) TBD |
| GST invoice | ⚠️ | Not implemented |

---

## ✅ 8. Trust & Professional Features

| Feature | Status | Notes |
|---------|--------|--------|
| Verified Host Badge | ⚠️ | No `users.is_verified_host` or equivalent yet |
| KYC for studio owners | ⚠️ | Not implemented |
| Studio approval by admin | ⚠️ | Studios can be draft/active; no explicit “approve” step in admin |
| Minimum images | ⚠️ | Not enforced on publish |
| Watermark system | ⚠️ | Not implemented |
| Escrow payment | ✅ | `BookingService::confirmBooking()` creates transaction; wallet flow |
| Review moderation | ✅ | Reviews have approved scope; admin moderation TBD |

---

## Config & Code References

- **Commission:** `config/studio.php` → `booking_commission_percent` (default 10). Use `STUDIO_BOOKING_COMMISSION_PERCENT` in `.env` to override.
- **Cancellation windows:** `config/studio.php` → `cancellation.flexible` (24h), `moderate` (72h), `strict` (168h).
- **Public API:** `GET /api/studios`, `GET /api/studios/categories`, `GET /api/studios/{slug}`, `GET /api/amenities`, `GET /api/bookings/calculate`, `POST /api/bookings`, `POST /api/bookings/confirm`.
- **Studio owner API:** `GET/POST/PUT/DELETE /api/studio-owner/studios`, `GET /api/studio-owner/bookings`.

---

## Summary

- **Done:** Two-sided model, commission config, expanded categories & amenities seeders, cancellation policy on studios and in booking flow, amenities in Add Studio form, search/filters, studio detail (gallery, amenities, cancellation, pricing, calendar, reviews, similar), booking calculate/store/confirm, escrow/transaction flow, core DB tables, overlap check for double booking.
- **Next priorities:** Admin CRUD for studio categories & amenities, studio image upload & availability calendar, admin “approve listing” and featured/commission/payment UIs, payment gateway (Razorpay/Stripe) and split payment, legal pages, and trust features (verified host, KYC, minimum images, watermark).
