<?php

namespace Tests\Unit;

use App\Rules\CpfOuCnpj;
use App\Support\Dinheiro;
use App\Support\Encargos;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EncargosTest extends TestCase
{
    public function test_sem_atraso_nao_ha_encargos(): void
    {
        $this->assertSame(0, Encargos::calcular(100_000, 0));
    }

    public function test_multa_de_2_por_cento_mais_juros_de_1_por_cento_ao_mes_pro_rata(): void
    {
        // R$ 1.000,00 com 30 dias de atraso: multa R$ 20,00 + juros R$ 10,00.
        $this->assertSame(3_000, Encargos::calcular(100_000, 30));
        // 1 dia: multa R$ 20,00 + juros R$ 0,33.
        $this->assertSame(2_033, Encargos::calcular(100_000, 1));
    }

    #[DataProvider('valores')]
    public function test_converte_texto_em_centavos(?string $texto, ?int $esperado): void
    {
        $this->assertSame($esperado, Dinheiro::paraCentavos($texto));
    }

    public static function valores(): array
    {
        return [
            'brasileiro com milhar' => ['1.234,56', 123_456],
            'com prefixo' => ['R$ 10,5', 1_050],
            'ponto decimal' => ['99.90', 9_990],
            'inteiro' => ['300', 30_000],
            'vazio' => ['', null],
            'texto' => ['abc', null],
        ];
    }

    public function test_formata_centavos_em_reais(): void
    {
        $this->assertSame('R$ 1.234,56', Dinheiro::formatar(123_456));
    }

    #[DataProvider('documentos')]
    public function test_valida_cpf_e_cnpj(string $documento, bool $valido): void
    {
        $falhou = false;
        (new CpfOuCnpj)->validate('documento', $documento, function () use (&$falhou) {
            $falhou = true;
        });

        $this->assertSame($valido, ! $falhou);
    }

    public static function documentos(): array
    {
        return [
            ['529.982.247-25', true],
            ['11.222.333/0001-81', true],
            ['111.111.111-11', false],
            ['529.982.247-24', false],
            ['123', false],
        ];
    }
}
