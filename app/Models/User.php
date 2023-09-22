<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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

    public function toArray()
    {
      
    return [
        //'id' => $this->id,
        'Nom' => $this->surname,
        'Prenom' => $this->name,
        'Date de Naissance' => $this->dateNais,
        'Email' => $this->email,
        'Filiere' => $this->getFiliereLabel(), 
        'Objectif hebdomadaire' => $this->getObjectifLabel(),
    ];

    }

    public function getFiliereLabel()
    {
        // Récupérez le libellé de la filière associée
        return $this->filiere->libelle;
    }

    public function getObjectifLabel()
    {
        // Récupérez le libellé de l'objectif associé
        return $this->objectif->libelle;
    }

    protected $table = 'users';

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'filiere_id');
    }

    public function objectif()
    {
        return $this->belongsTo(Objectif::class, 'objectif_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin'; 
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

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
