<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestante extends Model
{
    protected $fillable = [
        'nome',
        'gestante_id',
        'data_nascimento',
        'idade',
    ];

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'gestante_id');
    }
}
