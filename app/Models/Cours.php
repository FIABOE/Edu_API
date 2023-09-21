<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = ['pdf_file', 'pdf_file_name', 'filiere_id'];
    
    protected $table = 'cours';

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public static function validationRules($filiere_id)
{
    $uniqueRule = Rule::unique('cours')->where(function ($query) use ($filiere_id) {
        return $query->where('filiere_id', $filiere_id);
    });

    return [
        'pdf_file' => [
            'required',
            'mimes:pdf',
            $uniqueRule,
        ],
        'filiere_id' => 'required|exists:filieres,id',
    ];
}
}
