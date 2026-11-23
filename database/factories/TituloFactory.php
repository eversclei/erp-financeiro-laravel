<?php

namespace Database\Factories;

use App\Models\Cliente;
use App\Models\Titulo;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Titulo> */
class TituloFactory extends Factory
{
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'descricao' => fake()->randomElement(['Mensalidade', 'Licença de software', 'Consultoria', 'Manutenção', 'Implantação'])
                .' '.fake()->randomElement(['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro']),
            'valor' => fake()->numberBetween(5_000, 500_000),
            'vencimento' => today()->addDays(fake()->numberBetween(-120, 30)),
        ];
    }

    public function vencidoHa(int $dias): static
    {
        return $this->state(['vencimento' => today()->subDays($dias)]);
    }
}
