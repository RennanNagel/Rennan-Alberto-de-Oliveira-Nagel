<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // 25 clientes aleatórios
        Client::factory()->count(40)->create();

        // Usuário padrão para testes
        Client::factory()->create([
            'name'               => 'Teste',
            'email'              => 'teste@exemplo.com',
            'phone'              => '101010101',
            'is_active'          => true,
            'password'           => Hash::make('teste123'),
            'email_verified_at'  => now(),
        ]);
    }
}
