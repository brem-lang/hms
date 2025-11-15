<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Mail\TwoFactorMail;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [];

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

    public function isAdmin()
    {
        return $this->role === 'supervisor';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isStaff()
    {
        return $this->role === 'staff';
    }

    public function isFrontDesk()
    {
        return $this->role === 'front-desk';
    }

    public function generateCode()
    {
        $code = rand(100000, 999999);

        UserCode::updateOrCreate(
            ['user_id' => auth()->user()->id],
            ['code' => $code]
        );

        try {

            $details = [
                'title' => 'Email from Millenium Suites',
                'code' => $code,
                'name' => auth()->user()->name,
            ];

            Mail::to(auth()->user()->email)->send(new TwoFactorMail($details));
        } catch (Exception $e) {
            info('Error: '.$e->getMessage());
        }
    }

    public function userCodes()
    {
        return $this->hasMany(UserCode::class);
    }
}
