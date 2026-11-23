@extends('layouts.app')

@section('titulo', $titulo->descricao)

@push('estilos')
    <style>
        .grade { display: grid; grid-template-columns: 1fr 340px; gap: 16px; margin-top: 8px; }
        @media (max-width: 900px) { .grade { grid-template-columns: 1fr; } }
        dl { display: grid; grid-template-columns: 180px 1fr; gap: 12px 16px; margin: 0; }
        dt { color: var(--md-sys-color-on-surface-variant); }
        dd { margin: 0; }
        .total { font-size: 1.6rem; color: var(--md-sys-color-primary); }
    </style>
@endpush

@section('conteudo')
    @php $aberto = $titulo->status === \App\Enums\StatusTitulo::Aberto; @endphp

    <md-text-button href="{{ route('titulos.index') }}"><md-icon slot="icon">arrow_back</md-icon>Títulos</md-text-button>

    <div class="grade">
        <article class="cartao">
            <div class="cabecalho">
                <h1 class="md-typescale-headline-small">{{ $titulo->descricao }}</h1>
                @include('partials.status', ['titulo' => $titulo])
            </div>
            <dl class="md-typescale-body-large">
                <dt>Cliente</dt><dd>{{ $titulo->cliente->nome }} <span class="sutil">({{ $titulo->cliente->documentoFormatado() }})</span></dd>
                <dt>Vencimento</dt><dd>{{ $titulo->vencimento->format('d/m/Y') }}</dd>
                <dt>Valor original</dt><dd>@reais($titulo->valor)</dd>
                @if ($aberto)
                    <dt>Dias em atraso</dt><dd>{{ $titulo->diasEmAtraso() }}</dd>
                    <dt>Multa 2% + juros 1% a.m.</dt><dd class="{{ $titulo->encargos() ? 'vermelho' : '' }}">@reais($titulo->encargos())</dd>
                    <dt>Valor atualizado hoje</dt><dd class="total">@reais($titulo->valorAtualizado())</dd>
                @elseif ($titulo->status === \App\Enums\StatusTitulo::Pago)
                    <dt>Pago em</dt><dd>{{ $titulo->pago_em->format('d/m/Y') }}</dd>
                    <dt>Encargos pagos</dt><dd>@reais($titulo->encargos_pagos)</dd>
                    <dt>Valor recebido</dt><dd class="total">@reais($titulo->valor_pago)</dd>
                @endif
            </dl>
        </article>

        @if ($aberto)
            <aside class="cartao">
                <h2 class="md-typescale-title-large" style="margin-top:0">Registrar pagamento</h2>
                <p class="sutil md-typescale-body-medium">Os encargos são calculados pela data do pagamento.</p>
                <form method="POST" action="{{ route('titulos.pagar', $titulo) }}">
                    @csrf
                    <md-outlined-text-field class="campo" type="date" label="Data do pagamento" name="pago_em" required
                        max="{{ today()->toDateString() }}" value="{{ old('pago_em', today()->toDateString()) }}"
                        @error('pago_em') error error-text="{{ $message }}" @enderror></md-outlined-text-field>
                    <div class="acoes">
                        <md-filled-button type="submit"><md-icon slot="icon">payments</md-icon>Dar baixa</md-filled-button>
                    </div>
                </form>
                <md-divider style="margin:16px 0"></md-divider>
                <form method="POST" action="{{ route('titulos.cancelar', $titulo) }}">
                    @csrf
                    <div class="acoes">
                        <md-text-button type="submit"><md-icon slot="icon">block</md-icon>Cancelar título</md-text-button>
                    </div>
                </form>
            </aside>
        @endif
    </div>
@endsection
