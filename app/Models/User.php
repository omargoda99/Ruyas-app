<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Notifications\ResetPasswordNotification;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, HasRoleAndPermission, Notifiable, SoftDeletes ,Uuid;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    use SoftDeletes; // This enables soft deletes for the User model
    protected $table = 'users';


    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;


    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that are hidden.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'activated',
        'token',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'age',
        'gender',
        'ip_address',
        'country',
        'region',
        'city',
        'postal_code',
        'status',
        'activated',
        'token',
        'signup_ip_address',
        'signup_confirmation_ip_address',
        'signup_sm_ip_address',
        'admin_ip_address',
        'updated_ip_address',
        'deleted_ip_address',
    ];

    // protected $fillable = [
    //     'name', 'email', 'password_hash', 'age', 'gender', 'ip_address', 'country', 'region', 'city', 'postal_code', 'status'
    // ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'                                => 'integer',
        'name'                        => 'string',
        'phone'                         => 'string',
        'email'                             => 'string',
        'password'                          => 'string',
        'activated'                         => 'boolean',
        'token'                             => 'string',
        'signup_ip_address'                 => 'string',
        'signup_confirmation_ip_address'    => 'string',
        'signup_sm_ip_address'              => 'string',
        'admin_ip_address'                  => 'string',
        'updated_ip_address'                => 'string',
        'deleted_ip_address'                => 'string',
        'created_at'                        => 'datetime',
        'updated_at'                        => 'datetime',
        'deleted_at'                        => 'datetime',
    ];



    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return[];
    }
    public function favoriteDreams()
    {
        return $this->belongsToMany(Dream::class, 'user_dream_favorites')
                    ->withTimestamps();  // To automatically manage the created_at timestamps
    }

    // Relationship with subscriptions via the pivot table
    public function subscriptions()
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'user_subscription_coupon')
                    ->withPivot('used_at', 'starts_at', 'ends_at', 'is_active', 'coupon_id');
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

     // Relationship with dreams
     public function dreams()
     {
         return $this->hasMany(Dream::class, 'seer_id');
     }

     // Define a polymorphic relationship with AdminAction
    public function adminActions()
    {
        return $this->morphMany(AdminAction::class, 'target');
    }

    /**
     * Get the socials for the user.
     */
    public function social()
    {
        return $this->hasMany(\App\Models\Social::class);
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(\App\Models\Profile::class);
    }

    /**
     * The profiles that belong to the user.
     */
    public function profiles()
    {
        return $this->belongsToMany(\App\Models\Profile::class)->withTimestamps();
    }

    /**
     * Check if a user has a profile.
     *
     * @param  string  $name
     * @return bool
     */
    public function hasProfile($name)
    {
        foreach ($this->profiles as $profile) {
            if ($profile->name === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add/Attach a profile to a user.
     *
     * @param  Profile  $profile
     */
    public function assignProfile(Profile $profile)
    {
        return $this->profiles()->attach($profile);
    }

    /**
     * Remove/Detach a profile to a user.
     *
     * @param  Profile  $profile
     */
    public function removeProfile(Profile $profile)
    {
        return $this->profiles()->detach($profile);
    }
    public function complains()
    {
        return $this->hasMany(Complain::class);
    }

    public function interpreter()
    {
        return $this->hasOne(Interpreter::class, 'user_id');
    }

    public function subscriptionCoupons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserSubscriptionCoupon::class);
    }

    public function activeSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->subscriptionCoupons()
            ->where('is_active', true)
            ->where('ends_at', '>', now());
    }
      // Manually define the notifications relationship
      public function notifications()
      {
          return $this->morphMany('Illuminate\Notifications\DatabaseNotification', 'notifiable');
      }
      // Define the relationship to messages sent by the user (as the sender)
    public function sentMessages()
    {
        return $this->hasMany(ChMessage::class, 'from_id');
    }

    // Define the relationship to messages received by the user (as the receiver)
    public function receivedMessages()
    {
        return $this->hasMany(ChMessage::class, 'to_id');
    }
      public function conversations()
        {
            return $this->hasMany(Conversation::class, 'user_id');
        }
}
