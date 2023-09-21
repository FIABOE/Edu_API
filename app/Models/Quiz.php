<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'options', 'correct_option', 'type_quiz', 'filiere_id'];

protected $casts = [
    'options' => 'array', // Convertit automatiquement le champ "options" en tableau
];


    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }
}
