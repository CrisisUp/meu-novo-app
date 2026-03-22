<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Idoso extends Model
{
    use HasFactory;

    protected $table = 'idosos';

    protected $fillable = [
        'codigo_registro',
        'nome',
        'foto',
        'data_nascimento',
        'cpf',
        'nis',
        'contato_emergencia_nome',
        'contato_emergencia_telefone',
        'alergias',
        'medicamentos',
        'observacoes',
    ];

    /**
     * Lógica para gerar o código de registro automático.
     */
    protected static function booted()
    {
        static::creating(function ($idoso) {
            $ano = date('Y');
            $ultimoIdoso = static::whereYear('created_at', $ano)->orderBy('id', 'desc')->first();
            $sequencial = $ultimoIdoso ? ((int) substr($ultimoIdoso->codigo_registro, -4)) + 1 : 1;
            
            $idoso->codigo_registro = 'CDI-' . $ano . '-' . str_pad($sequencial, 4, '0', STR_PAD_LEFT);
        });
    }

    public function frequencias()
    {
        return $this->hasMany(Frequencia::class);
    }

    public function encaminhamentos()
    {
        return $this->hasMany(Encaminhamento::class);
    }

    public function atividades()
    {
        return $this->belongsToMany(Atividade::class);
    }

    /**
     * Scope para aplicar filtros na listagem e exportação de idosos.
     */
    public function scopeFiltered($query, $search = null, $filtro = null)
    {
        return $query->when($search, function ($query, $search) {
                return $query->where('nome', 'like', "%{$search}%")
                             ->orWhere('cpf', 'like', "%{$search}%");
            })
            ->when($filtro == 'sem_cpf', function ($query) {
                return $query->whereNull('cpf')->orWhere('cpf', '');
            })
            ->when($filtro == 'com_medicamento', function ($query) {
                return $query->whereNotNull('medicamentos')->where('medicamentos', '!=', '');
            });
    }
}
