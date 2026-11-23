<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Titulo;
use Illuminate\Database\Seeder;

/** Carteira de demonstração com títulos a vencer, vencidos em várias faixas e pagos. */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = collect([
            ['nome' => 'Papelaria Central Ltda', 'documento' => '11222333000181', 'email' => 'financeiro@papelariacentral.com.br'],
            ['nome' => 'Mercado Bom Preço', 'documento' => '00000000000191', 'email' => 'contas@bompreco.com.br'],
            ['nome' => 'Clínica Vida Plena', 'documento' => '33000167000101', 'email' => 'adm@vidaplena.com.br'],
            ['nome' => 'João da Silva', 'documento' => '52998224725', 'email' => 'joao.silva@email.com'],
            ['nome' => 'Oficina Rápida Auto', 'documento' => '60701190000104', 'email' => null],
        ])->map(fn (array $dados) => Cliente::create($dados));

        foreach ($clientes as $cliente) {
            Titulo::factory()->count(2)->for($cliente)->state(fn () => ['vencimento' => today()->addDays(random_int(1, 25))])->create();
            Titulo::factory()->for($cliente)->vencidoHa(random_int(3, 25))->create();
            Titulo::factory()->for($cliente)->vencidoHa(random_int(35, 100))->create();
        }

        // Títulos já pagos no mês, alguns com atraso (pagos com encargos).
        Titulo::factory()->count(6)->recycle($clientes)
            ->state(fn () => ['vencimento' => today()->subDays(random_int(0, 15))])
            ->create()
            ->each(fn (Titulo $t) => $t->registrarPagamento(today()->subDays(random_int(0, min(5, $t->diasEmAtraso())))));
    }
}
