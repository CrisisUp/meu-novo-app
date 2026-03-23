<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frequencia extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'idoso_id',
        'user_id',
        'data',
        'entrada',
        'saida',
        'status',
        'observacoes',
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
