<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;


class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'attempts',
        'coins',
        'timer',
        'last_update' // Добавьте это поле
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
    public function presents()
    {
    return $this->hasMany(Present::class);
    }

    public function markers()
    {
        return $this->hasMany(Marker::class);
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function associatePresent(Present $present)
    {
        $pivotData = [
            'user_id' => $this->id,
            'present_id' => $present->id,
            'created_at' => now(),
            'updated_at' => now()
        ];

        DB::table('present_user')->insert($pivotData);
    }

    /**
     * Add coins to the user's balance.
     *
     * @param int $amount
     * @return int
     */
    public function addCoins(int $amount): int
    {
        $this->coins += $amount;
        $this->save();
        
        return $this->coins;
    }

    /**
     * Deduct coins from the user's balance.
     *
     * @param int $amount
     * @return int|bool Returns new balance if successful, false if insufficient coins
     */
    public function deductCoins(int $amount)
    {
        if ($this->coins < $amount) {
            return false;
        }
        
        $this->coins -= $amount;
        $this->save();
        
        return $this->coins;
    }

    /**
     * Check if user has enough coins.
     *
     * @param int $amount
     * @return bool
     */
    public function hasEnoughCoins(int $amount): bool
    {
        return $this->coins >= $amount;
    }

    /**
     * Set timer to a specific countdown value.
     *
     * @param int $seconds
     * @return int
     */
    public function setTimer(int $seconds): int
    {
        $this->timer = $seconds;
        $this->save();
        
        return $this->timer;
    }

    /**
     * Reset timer to zero.
     *
     * @return int
     */
    public function resetTimer(): int
    {
        return $this->setTimer(0);
    }
}
