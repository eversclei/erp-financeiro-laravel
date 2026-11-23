<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Cliente> */
class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->company(),
            'documento' => self::cpfValido(),
            'email' => fake()->unique()->safeEmail(),
        ];
    }

    /** Gera um CPF com dígitos verificadores corretos. */
    public static function cpfValido(): string
    {
        $base = array_map(fn () => random_int(0, 9), range(1, 9));
        foreach ([10, 11] as $peso) {
            $soma = 0;
            foreach ($base as $i => $n) {
                $soma += $n * ($peso - $i);
            }
            $resto = $soma % 11;
            $base[] = $resto < 2 ? 0 : 11 - $resto;
        }

        return implode('', $base);
    }
}
