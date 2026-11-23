<?php

namespace Tests\Feature;

use App\Enums\StatusTitulo;
use App\Models\Cliente;
use App\Models\Titulo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ContasAReceberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-11-23');
    }

    public function test_pagamento_em_atraso_cobra_multa_e_juros(): void
    {
        $titulo = Titulo::factory()->create(['valor' => 100_000, 'vencimento' => '2026-10-24']); // 30 dias antes

        $this->post(route('titulos.pagar', $titulo), ['pago_em' => '2026-11-23'])
            ->assertRedirect(route('titulos.show', $titulo))
            ->assertSessionHas('sucesso');

        $titulo->refresh();
        $this->assertSame(StatusTitulo::Pago, $titulo->status);
        $this->assertSame(3_000, $titulo->encargos_pagos);
        $this->assertSame(103_000, $titulo->valor_pago);
    }

    public function test_pagamento_no_prazo_nao_tem_encargos(): void
    {
        $titulo = Titulo::factory()->create(['valor' => 50_000, 'vencimento' => '2026-11-30']);

        $this->post(route('titulos.pagar', $titulo), ['pago_em' => '2026-11-23']);

        $this->assertSame(50_000, $titulo->refresh()->valor_pago);
    }

    public function test_nao_paga_duas_vezes_nem_com_data_futura(): void
    {
        $titulo = Titulo::factory()->create(['vencimento' => '2026-11-30']);

        $this->post(route('titulos.pagar', $titulo), ['pago_em' => '2026-11-24'])->assertSessionHasErrors('pago_em');

        $this->post(route('titulos.pagar', $titulo), ['pago_em' => '2026-11-23']);
        $this->post(route('titulos.pagar', $titulo), ['pago_em' => '2026-11-23'])->assertSessionHasErrors('geral');
    }

    public function test_titulo_pago_nao_pode_ser_cancelado(): void
    {
        $titulo = Titulo::factory()->create(['vencimento' => '2026-11-23']);
        $titulo->registrarPagamento(today());

        $this->post(route('titulos.cancelar', $titulo))->assertSessionHasErrors('geral');
        $this->assertSame(StatusTitulo::Pago, $titulo->refresh()->status);
    }

    public function test_lancamento_aceita_valor_no_formato_brasileiro(): void
    {
        $cliente = Cliente::factory()->create();

        $this->post(route('titulos.store'), [
            'cliente_id' => $cliente->id,
            'descricao' => 'Implantação do sistema',
            'valor' => '1.234,56',
            'vencimento' => '2026-12-10',
        ])->assertRedirect();

        $this->assertDatabaseHas('titulos', ['cliente_id' => $cliente->id, 'valor' => 123_456, 'status' => 'aberto']);
    }

    public function test_cliente_com_documento_invalido_ou_repetido_e_recusado(): void
    {
        Cliente::factory()->create(['documento' => '52998224725']);

        $this->post(route('clientes.store'), ['nome' => 'Fulano', 'documento' => '123.456.789-00'])
            ->assertSessionHasErrors('documento');
        $this->post(route('clientes.store'), ['nome' => 'Fulano', 'documento' => '529.982.247-25'])
            ->assertSessionHasErrors('documento');

        $this->post(route('clientes.store'), ['nome' => 'Papelaria Central', 'documento' => '11.222.333/0001-81'])
            ->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', ['documento' => '11222333000181']);
    }

    public function test_painel_soma_aberto_vencido_e_faixas_de_atraso(): void
    {
        Titulo::factory()->create(['valor' => 10_000, 'vencimento' => '2026-12-01']); // a vencer
        Titulo::factory()->create(['valor' => 20_000, 'vencimento' => '2026-11-13']); // 10 dias
        Titulo::factory()->create(['valor' => 30_000, 'vencimento' => '2026-08-01']); // > 90 dias
        $pago = Titulo::factory()->create(['valor' => 40_000, 'vencimento' => '2026-11-20']);
        $pago->registrarPagamento(Carbon::parse('2026-11-20'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('aReceber', 60_000)
            ->assertViewHas('vencido', 50_000)
            ->assertViewHas('recebidoNoMes', 40_000)
            ->assertViewHas('aging', fn ($aging) => $aging['1 a 30 dias'] === 20_000 && $aging['Mais de 90 dias'] === 30_000)
            ->assertSee('R$ 600,00');
    }

    public function test_filtro_de_vencidos_mostra_so_os_atrasados(): void
    {
        Titulo::factory()->create(['descricao' => 'Mensalidade atrasada', 'vencimento' => '2026-11-01']);
        Titulo::factory()->create(['descricao' => 'Mensalidade futura', 'vencimento' => '2026-12-01']);

        $this->get(route('titulos.index', ['filtro' => 'vencidos']))
            ->assertOk()
            ->assertSee('Mensalidade atrasada')
            ->assertDontSee('Mensalidade futura');
    }
}
