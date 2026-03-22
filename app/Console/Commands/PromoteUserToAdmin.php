<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class PromoteUserToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cdi:promote-admin {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promove um usuário para o cargo de administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('Usuário não encontrado!');
            return;
        }

        $user->update(['role' => 'admin']);
        $this->info("O usuário {$user->name} agora é um ADMINISTRADOR.");
    }
}
