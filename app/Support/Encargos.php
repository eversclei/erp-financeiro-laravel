<?php

namespace App\Support;

/**
 * Encargos por atraso no padrão usual de boletos no Brasil:
 * multa única de 2% + juros de mora de 1% ao mês, pro rata dia (30 dias),
 * ambos sobre o valor original. Tudo em centavos.
 */
final class Encargos
{
    public const MULTA = 0.02;

    public const JUROS_AO_MES = 0.01;

    public static function calcular(int $valorCentavos, int $diasEmAtraso): int
    {
        if ($diasEmAtraso <= 0) {
            return 0;
        }

        $multa = $valorCentavos * self::MULTA;
        $juros = $valorCentavos * (self::JUROS_AO_MES / 30) * $diasEmAtraso;

        return (int) round($multa + $juros);
    }
}
