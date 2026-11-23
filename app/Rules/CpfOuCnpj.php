<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/** Valida CPF ou CNPJ pelos dígitos verificadores (aceita com ou sem máscara). */
final class CpfOuCnpj implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digitos = preg_replace('/\D/', '', (string) $value);

        $valido = match (strlen($digitos)) {
            11 => self::cpfValido($digitos),
            14 => self::cnpjValido($digitos),
            default => false,
        };

        if (! $valido) {
            $fail('Informe um CPF ou CNPJ válido.');
        }
    }

    private static function cpfValido(string $cpf): bool
    {
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        return self::digito(substr($cpf, 0, 9), range(10, 2)) === (int) $cpf[9]
            && self::digito(substr($cpf, 0, 10), range(11, 2)) === (int) $cpf[10];
    }

    private static function cnpjValido(string $cnpj): bool
    {
        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        return self::digito(substr($cnpj, 0, 12), [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]) === (int) $cnpj[12]
            && self::digito(substr($cnpj, 0, 13), [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]) === (int) $cnpj[13];
    }

    /** @param  int[]  $pesos */
    private static function digito(string $numeros, array $pesos): int
    {
        $soma = 0;
        foreach (str_split($numeros) as $i => $n) {
            $soma += (int) $n * $pesos[$i];
        }
        $resto = $soma % 11;

        return $resto < 2 ? 0 : 11 - $resto;
    }
}
