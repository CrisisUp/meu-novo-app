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
        $idososSemCodigo = Idoso::whereNull('codigo_registro')->orWhere('codigo_registro', '')->orderBy('id')->get();

        if ($idososSemCodigo->isEmpty()) {
            $this->info('Todos os idosos já possuem código de registro.');
            return;
        }

        $this->info('Gerando códigos para ' . $idososSemCodigo->count() . ' idosos...');

        foreach ($idososSemCodigo as $idoso) {
            $ano = $idoso->created_at ? $idoso->created_at->year : date('Y');
            
            // Busca o último código gerado para este ano específico
            $ultimoIdosoAno = Idoso::where('codigo_registro', 'like', "CDI-$ano-%")
                ->orderBy('codigo_registro', 'desc')
                ->first();

            $proximoSequencial = 1;
            if ($ultimoIdosoAno) {
                $proximoSequencial = ((int) substr($ultimoIdosoAno->codigo_registro, -4)) + 1;
            }

            $novoCodigo = 'CDI-' . $ano . '-' . str_pad($proximoSequencial, 4, '0', STR_PAD_LEFT);
            
            $idoso->update(['codigo_registro' => $novoCodigo]);
            
            $this->line("Idoso: {$idoso->nome} -> Código: {$novoCodigo}");
        }

        $this->info('Processo concluído com sucesso!');
    }
}
