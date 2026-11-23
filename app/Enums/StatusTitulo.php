<?php

namespace App\Enums;

enum StatusTitulo: string
{
    case Aberto = 'aberto';
    case Pago = 'pago';
    case Cancelado = 'cancelado';

    public function rotulo(): string
    {
        return match ($this) {
            self::Aberto => 'Em aberto',
            self::Pago => 'Pago',
            self::Cancelado => 'Cancelado',
        };
    }
}
