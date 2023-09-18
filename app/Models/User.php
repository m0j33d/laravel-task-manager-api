<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use
        // Add more on top here
        HasApiTokens,
        HasFactory,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'avatar',
        'status'
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
    ];

    /**
     * The attributes that search keyword can be checked against
     */
    protected $searchables = [
        'first_name', 'last_name', 'email'
    ];

    protected $with = [
        'media'
    ];

    /**
     * Get all accounts that user belongs to
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    /**
     * Send a password reset notification to the user.
     *
     * @param  string  $token
     */
    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.frontend_url') . "/password-reset/$token?". http_build_query(["email" => $this->email]);
        $this->notify((new ResetPasswordNotification($url))->delay(now()->addSeconds(5)));
    }

    /**
     * Update Password for this model
     *
     * @param string $password
     *
     * @return bool
     */
    private function updateUserPassword(string $password)
    {
        return $this->update(['password' => Hash::make($password)]);
    }

    public function emailVerified(): bool
    {
        return isset($this->email_verified_at);
    }
}