<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'institution_id',
        'phone',
        'dob',
        'gender',
        'address',
        'designation',
        'specialty',
        'qualification',
        'registration_no',
        'bio',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'dob' => 'date',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function profileImageUrl(): string
    {
        if ($this->profile_image && file_exists(public_path('profile_images/' . $this->profile_image))) {
            return asset('profile_images/' . $this->profile_image);
        }
        return '';
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class, 'user_units');
    }

    public function views()
    {
        return $this->belongsToMany(UnitView::class, 'user_views');
    }
}
