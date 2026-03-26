<?php

namespace App\Console\Commands;

use App\Models\Idoso;
use Illuminate\Console\Command;

class GerarCodigosRegistroIdosos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdi:gerar-codigos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera códigos de registro para idosos que ainda não possuem um.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $idososSemCodigo = Idoso::where(function($q) {
                $q->whereNull('codigo_registro')->orWhere('codigo_registro', '');
            })
            ->orderBy('id')
            ->get();

        if ($idososSemCodigo->isEmpty()) {
            $this->info('Todos os idosos já possuem código de registro.');
            return;
        }

        $this->info('Gerando códigos para ' . $idososSemCodigo->count() . ' idosos...');

        // Cache para o sequencial por ano para evitar N+1 queries complexas
        $sequenciaisPorAno = [];

        foreach ($idososSemCodigo as $idoso) {
            $ano = $idoso->created_at ? $idoso->created_at->year : date('Y');
            
            if (!isset($sequenciaisPorAno[$ano])) {
                // Busca o último código gerado para este ano específico com bloqueio de leitura
                $ultimoIdosoAno = Idoso::withTrashed()
                    ->where('codigo_registro', 'like', "CDI-$ano-%")
                    ->orderByRaw('LENGTH(codigo_registro) DESC, codigo_registro DESC')
                    ->lockForUpdate()
                    ->first();

                $sequenciaisPorAno[$ano] = 1;
                if ($ultimoIdosoAno) {
                    $partes = explode('-', $ultimoIdosoAno->codigo_registro);
                    $sequenciaisPorAno[$ano] = ((int) end($partes)) + 1;
                }
            } else {
                // Incrementa a partir do cache local deste lote
                $sequenciaisPorAno[$ano]++;
            }

            $novoCodigo = 'CDI-' . $ano . '-' . str_pad($sequenciaisPorAno[$ano], 4, '0', STR_PAD_LEFT);
            
            $idoso->update(['codigo_registro' => $novoCodigo]);
            
            $this->line("Idoso: {$idoso->nome} -> Código: {$novoCodigo}");
        }

        $this->info('Processo concluído com sucesso!');
    }
}
