<?php

namespace App\Models;

use App\Enums\StatusTitulo;
use App\Support\Encargos;
use Carbon\CarbonInterface;
use Database\Factories\TituloFactory;
use DomainException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/** Conta a receber. Valores em centavos. */
class Titulo extends Model
{
    /** @use HasFactory<TituloFactory> */
    use HasFactory;

    protected $fillable = ['cliente_id', 'descricao', 'valor', 'vencimento'];

    protected $attributes = ['status' => 'aberto'];

    protected function casts(): array
    {
        return [
            'vencimento' => 'date',
            'pago_em' => 'date',
            'status' => StatusTitulo::class,
            'valor' => 'integer',
            'valor_pago' => 'integer',
            'encargos_pagos' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function scopeEmAberto(Builder $query): void
    {
        $query->where('status', StatusTitulo::Aberto);
    }

    public function scopeVencidos(Builder $query, ?CarbonInterface $hoje = null): void
    {
        $query->emAberto()->whereDate('vencimento', '<', ($hoje ?? today())->toDateString());
    }

    public function diasEmAtraso(?CarbonInterface $em = null): int
    {
        $em = Carbon::parse($em ?? today())->startOfDay();

        return $this->vencimento->lt($em) ? (int) $this->vencimento->diffInDays($em) : 0;
    }

    public function encargos(?CarbonInterface $em = null): int
    {
        return $this->status === StatusTitulo::Aberto
            ? Encargos::calcular($this->valor, $this->diasEmAtraso($em))
            : 0;
    }

    public function valorAtualizado(?CarbonInterface $em = null): int
    {
        return $this->valor + $this->encargos($em);
    }

    /** Baixa o título pelo valor atualizado na data do pagamento. */
    public function registrarPagamento(CarbonInterface $data): void
    {
        if ($this->status !== StatusTitulo::Aberto) {
            throw new DomainException("Só títulos em aberto podem ser pagos (este está {$this->status->rotulo()}).");
        }
        if ($data->isFuture()) {
            throw new DomainException('A data de pagamento não pode estar no futuro.');
        }

        $encargos = $this->encargos($data);
        $this->forceFill([
            'status' => StatusTitulo::Pago,
            'pago_em' => $data,
            'encargos_pagos' => $encargos,
            'valor_pago' => $this->valor + $encargos,
        ])->save();
    }

    public function cancelar(): void
    {
        if ($this->status === StatusTitulo::Pago) {
            throw new DomainException('Título pago não pode ser cancelado; registre um estorno.');
        }

        $this->forceFill(['status' => StatusTitulo::Cancelado])->save();
    }
}
