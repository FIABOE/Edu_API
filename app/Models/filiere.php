<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $table = 'filieres'; 

    protected $fillable = [
        'libelle', 
    ];

    public function cours()
    {
        return $this->hasMany(Cours::class);
    }
}
