<?php

namespace App\Models;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Idoso extends Model
{
    use HasFactory, Loggable;

    protected $table = 'idosos';

    protected $fillable = [
        'codigo_registro',
        'nome',
        'foto',
        'data_nascimento',
        'sexo',
        'grau_dependencia',
        'data_admissao',
        'data_desligamento',
        'motivo_desligamento',
        'cpf',
        'nis',
        'contato_emergencia_nome',
        'contato_emergencia_telefone',
        'alergias',
        'medicamentos',
        'observacoes',
    ];

    /**
     * Retorna o texto formatado do sexo/gênero.
     */
    public function getSexoTextoAttribute()
    {
        return match($this->sexo) {
            'cis_m' => 'Cisgênero Masculino',
            'cis_f' => 'Cisgênero Feminino',
            'trans_m' => 'Transgênero Masculino',
            'trans_f' => 'Transgênero Feminino',
            'agenero' => 'Agênero',
            'nao_declarado' => 'Não declarado',
            default => $this->sexo
        };
    }

    /**
     * Mascaramento de CPF para LGPD (Ex: 123.***.***-01)
     */
    public function getCpfMaskedAttribute()
    {
        if (!$this->cpf) return 'Não informado';

        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $this->cpf);

        if (strlen($cpf) != 11) return $this->cpf;

        return substr($cpf, 0, 3) . '.***.***-' . substr($cpf, -2);
    }

    /**
     * Mascaramento de NIS para LGPD (Ex: 123.*****.**-1)
     */
    public function getNisMaskedAttribute()
    {
        if (!$this->nis) return 'Não informado';

        $nis = preg_replace('/[^0-9]/', '', $this->nis);

        if (strlen($nis) != 11) return $this->nis;

        return substr($nis, 0, 3) . '.*****.**-' . substr($nis, -1);
    }

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
                return $query->where(function($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                      ->orWhere('cpf', 'like', "%{$search}%");
                });
            })
            // Filtro de Status (Ativos por padrão se não for 'desligados' ou 'todos')
            ->when($filtro == 'desligados', function ($query) {
                return $query->whereNotNull('data_desligamento');
            })
            ->when($filtro == 'todos', function ($query) {
                // Não aplica filtro de data_desligamento
            }, function ($query) use ($filtro) {
                // Comportamento padrão: apenas ativos
                if ($filtro != 'desligados') {
                    $query->whereNull('data_desligamento');
                }
            })
            ->when($filtro == 'sem_cpf', function ($query) {
                return $query->whereNull('cpf')->orWhere('cpf', '');
            })
            ->when($filtro == 'com_medicamento', function ($query) {
                return $query->whereNotNull('medicamentos')->where('medicamentos', '!=', '');
            })
            ->when($filtro == 'faixa_60_64', function ($query) {
                return $query->whereBetween('data_nascimento', [now()->subYears(65)->addDay(), now()->subYears(60)]);
            })
            ->when($filtro == 'faixa_65_69', function ($query) {
                return $query->whereBetween('data_nascimento', [now()->subYears(70)->addDay(), now()->subYears(65)]);
            })
            ->when($filtro == 'faixa_70_74', function ($query) {
                return $query->whereBetween('data_nascimento', [now()->subYears(75)->addDay(), now()->subYears(70)]);
            })
            ->when($filtro == 'faixa_75_mais', function ($query) {
                return $query->where('data_nascimento', '<=', now()->subYears(75));
            });
    }

    /**
     * Atributo virtual para calcular a idade exata.
     */
    public function getIdadeAttribute()
    {
        return \Carbon\Carbon::parse($this->data_nascimento)->age;
    }

    /**
     * Atributo virtual para definir a categoria (faixa etária).
     */
    public function getFaixaEtariaAttribute()
    {
        $idade = $this->idade;

        if ($idade >= 60 && $idade <= 64) {
            return '60-64 anos';
        } elseif ($idade >= 65 && $idade <= 69) {
            return '65-69 anos';
        } elseif ($idade >= 70 && $idade <= 74) {
            return '70-74 anos';
        } elseif ($idade >= 75) {
            return '75 anos ou mais';
        }

        return 'Menor de 60 anos';
    }
}
