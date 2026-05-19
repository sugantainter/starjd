<?php

namespace App\Models;

use App\Support\StoragePublicUrl;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'apple_sub',
        'password',
        'fcm_token',
        'avatar',
        'state_id',
        'city_id',
        'last_profile_reminder_at',
        'last_payment_reminder_at',
        'last_social_reminder_at',
    ];

    public function state(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_profile_reminder_at' => 'datetime',
            'last_payment_reminder_at' => 'datetime',
            'last_social_reminder_at' => 'datetime',
        ];
    }

    public function creatorProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function brandProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(BrandProfile::class);
    }

    public function professionalProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProfessionalProfile::class);
    }

    public function packages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Package::class, 'creator_id');
    }

    public function accessPayments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AccessPayment::class);
    }

    public function socialAccounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function collaborationsAsBrand(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Collaboration::class, 'brand_id');
    }

    public function collaborationsAsCreator(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Collaboration::class, 'creator_id');
    }

    public function creatorImagePosts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CreatorImagePost::class, 'creator_id');
    }

    public function wallet(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function campaignApplications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CampaignApplication::class, 'creator_id');
    }

    public function campaignsAsBrand(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Campaign::class, 'brand_id');
    }

    public function studios(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Studio::class);
    }

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function serviceListings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceListing::class);
    }

    public function serviceBookingsAsBuyer(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'buyer_id');
    }

    public function serviceBookingsAsSeller(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceBooking::class, 'seller_id');
    }

    /**
     * RBAC: User can have multiple roles; is_primary used for dashboard.
     */
    public function roles(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function hasRole(string $slug): bool
    {
        return $this->roles()->where('slug', $slug)->exists();
    }

    public function primaryRole(): ?Role
    {
        return $this->roles()->wherePivot('is_primary', true)->first();
    }

    /**
     * Role slug from primary role (replaces legacy users.role column).
     */
    public function getRoleAttribute(): ?string
    {
        return $this->primaryRole()?->slug;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if ($this->avatar) {
            if (str_starts_with($this->avatar, 'http')) {
                return $this->avatar;
            }
            return StoragePublicUrl::resolve($this->avatar);
        }
        return null;
    }
}
