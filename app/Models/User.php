<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;


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
        'surname',
        'dateNais',
        'email',
        'password',
        'remember_token',
        'consent',
        'filiere_id',
        'objectif_id',
        'role',
    ];
     // Dans app/Models/User.php
public function toArray()
{
    return [
        'id' => $this->id,
        'name' => $this->name,
        'email' => $this->email,
        'role' => $this->role, // Assurez-vous d'inclure le champ 'role' ici
    ];
}

    
    protected $table = 'users';

    public function objectif()
    {
        return $this->belongsTo(Objectif::class);
    }

    public function filiere()
    {
        return $this->belongsTo(filiere::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin'; // Supposons que le rôle d'administrateur est défini comme 'admin'
    }


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
    ];
    
    public function sendPasswordResetNotification($token): void
    {
        $url = 'https://spa.test/reset-password?token=' . $token;
        $this->notify(new ResetPasswordNotification($url));
    }
}
