<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Encaminhamento extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'idoso_id',
        'user_id',
        'instituicao_destino',
        'especialidade',
        'motivo',
        'prioridade',
        'data_encaminhamento',
        'status',
        'observacoes_retorno',
    ];

    public function idoso()
    {
        return $this->belongsTo(Idoso::class);
    }

    public function profissional()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
