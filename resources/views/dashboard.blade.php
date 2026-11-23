@extends('layouts.app')

@section('titulo', 'Painel')

@push('estilos')
    <style>
        .kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .kpi { display: flex; gap: 16px; align-items: center; }
        .kpi md-icon { --md-icon-size: 36px; color: var(--md-sys-color-primary); }
        .kpi.alerta md-icon, .kpi.alerta .valor { color: var(--md-sys-color-error); }
        .kpi div { display: flex; flex-direction: column; }
        .grade { display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 16px; margin-bottom: 16px; }
        .faixa { display: grid; grid-template-columns: 130px 1fr 110px; gap: 12px; align-items: center; padding: 8px 0; }
        md-linear-progress { --md-linear-progress-active-indicator-color: var(--md-sys-color-error); }
        h2 { margin: 0 0 12px; }
    </style>
@endpush

@section('conteudo')
    <div class="cabecalho">
        <h1 class="md-typescale-headline-medium">Contas a receber</h1>
        <md-filled-button href="{{ route('titulos.create') }}"><md-icon slot="icon">add</md-icon>Lançar título</md-filled-button>
    </div>

    <section class="kpis">
        <div class="cartao kpi">
            <md-icon>account_balance_wallet</md-icon>
            <div><span class="sutil md-typescale-body-medium">Em aberto</span><span class="valor md-typescale-headline-small">@reais($aReceber)</span></div>
        </div>
        <div class="cartao kpi alerta">
            <md-icon>warning</md-icon>
            <div><span class="sutil md-typescale-body-medium">Vencido</span><span class="valor md-typescale-headline-small">@reais($vencido)</span></div>
        </div>
        <div class="cartao kpi">
            <md-icon>savings</md-icon>
            <div><span class="sutil md-typescale-body-medium">Recebido no mês</span><span class="valor md-typescale-headline-small">@reais($recebidoNoMes)</span></div>
        </div>
        <div class="cartao kpi">
            <md-icon>percent</md-icon>
            <div>
                <span class="sutil md-typescale-body-medium">Inadimplência</span>
                <span class="valor md-typescale-headline-small">{{ $aReceber ? number_format($vencido / $aReceber * 100, 1, ',', '.') : '0,0' }}%</span>
            </div>
        </div>
    </section>

    <section class="grade">
        <div class="cartao">
            <h2 class="md-typescale-title-large">Vencidos por faixa de atraso</h2>
            @foreach ($aging as $faixa => $valor)
                <div class="faixa">
                    <span class="md-typescale-body-medium">{{ $faixa }}</span>
                    <md-linear-progress value="{{ $vencido ? $valor / $vencido : 0 }}"></md-linear-progress>
                    <strong class="md-typescale-body-medium" style="text-align:right">@reais($valor)</strong>
                </div>
            @endforeach
        </div>

        <div class="cartao">
            <h2 class="md-typescale-title-large">Maiores devedores</h2>
            @forelse ($maioresDevedores as $devedor)
                <div class="faixa" style="grid-template-columns: 1fr auto">
                    <a href="{{ route('titulos.index', ['filtro' => 'vencidos', 'cliente' => $devedor->id]) }}" class="md-typescale-body-medium">
                        {{ $devedor->nome }} <span class="sutil">· {{ $devedor->quantidade }} título(s)</span>
                    </a>
                    <strong class="vermelho md-typescale-body-medium">@reais((int) $devedor->total)</strong>
                </div>
            @empty
                <p class="sutil">Nenhum título vencido. 🎉</p>
            @endforelse
        </div>
    </section>

    <section class="cartao">
        <h2 class="md-typescale-title-large">Vencem nos próximos 7 dias</h2>
        <div class="tabela">
            <table>
                <thead><tr><th>Vencimento</th><th>Cliente</th><th>Descrição</th><th class="num">Valor</th></tr></thead>
                <tbody>
                @forelse ($proximos as $titulo)
                    <tr onclick="location.href='{{ route('titulos.show', $titulo) }}'" style="cursor:pointer">
                        <td>{{ $titulo->vencimento->format('d/m/Y') }}</td>
                        <td>{{ $titulo->cliente->nome }}</td>
                        <td>{{ $titulo->descricao }}</td>
                        <td class="num">@reais($titulo->valor)</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="sutil">Nada vencendo nesta semana.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
