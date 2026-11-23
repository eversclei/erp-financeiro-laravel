<?php

namespace App\Support;

final class Dinheiro
{
    /** 123456 → "R$ 1.234,56" */
    public static function formatar(?int $centavos): string
    {
        return 'R$ '.number_format(($centavos ?? 0) / 100, 2, ',', '.');
    }

    /** "1.234,56", "1234.56" ou "1234" → 123456. Retorna null se não for um valor. */
    public static function paraCentavos(?string $texto): ?int
    {
        $texto = trim(str_replace(['R$', ' '], '', (string) $texto));
        if ($texto === '') {
            return null;
        }

        // Formato brasileiro: ponto de milhar e vírgula decimal.
        if (str_contains($texto, ',')) {
            $texto = str_replace(['.', ','], ['', '.'], $texto);
        }

        return is_numeric($texto) ? (int) round(((float) $texto) * 100) : null;
    }
}
