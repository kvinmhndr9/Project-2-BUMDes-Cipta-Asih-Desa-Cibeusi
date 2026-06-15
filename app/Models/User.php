<?php

namespace App\Models;

use App\Models\Concerns\ExposesPrimaryKeyAsId;
use App\Notifications\VerifyEmailOtpNotification;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use ExposesPrimaryKeyAsId;
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'User';

    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'no_hp',
        'password',
        'role',
        'id_wisata',
        'google_id',
        'avatar',
        'asal_kota',
        'verification_code',
        'verification_code_expires_at',
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
        'email_verified_at'            => 'datetime',
        'verification_code_expires_at' => 'datetime',
    ];

    /**
     * Kirim kode OTP 6 angka ke email user sebagai pengganti link verifikasi default.
     */
    public function sendEmailVerificationNotification(): void
    {
        $code = (string) random_int(100000, 999999);

        $this->forceFill([
            'verification_code'            => $code,
            'verification_code_expires_at' => now()->addMinutes(30),
        ])->save();

        $this->notify(new VerifyEmailOtpNotification($code, $this->name));
    }

    public function wisata()
    {
        return $this->belongsTo(Wisata::class, 'id_wisata', 'id_wisata');
    }

    public function tiket()
    {
        return $this->hasMany(Tiket::class, 'id_user', 'id_user');
    }

    public function isPengunjung(): bool
    {
        return $this->role === 'pengunjung';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengelolaBumdes(): bool
    {
        return $this->role === 'pengelola_bumdes';
    }

    /**
     * Get the user's avatar.
     *
     * @param  string|null  $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        return $value ?: asset('images/default-avatar.svg');
    }
}

