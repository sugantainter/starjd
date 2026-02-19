<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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
}
