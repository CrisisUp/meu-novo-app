<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atividade extends Model
{
    use HasFactory, Loggable;

    protected $fillable = ['nome', 'facilitador', 'dia_semana', 'horario', 'descricao'];

    public function idosos()
    {
        return $this->belongsToMany(Idoso::class);
    }
}
