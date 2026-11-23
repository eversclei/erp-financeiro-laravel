@extends('layouts.app')

@section('titulo', 'Títulos')

@section('conteudo')
    <div class="cabecalho">
        <h1 class="md-typescale-headline-medium">Títulos</h1>
        <md-filled-button href="{{ route('titulos.create') }}"><md-icon slot="icon">add</md-icon>Lançar título</md-filled-button>
    </div>

    <nav class="acoes" style="justify-content:flex-start;margin-bottom:16px" aria-label="Filtro">
        @foreach (['abertos' => 'Em aberto', 'vencidos' => 'Vencidos', 'pagos' => 'Pagos', 'todos' => 'Todos'] as $chave => $rotulo)
            @if ($filtro === $chave)
                <md-filled-tonal-button href="{{ route('titulos.index', array_filter(['filtro' => $chave, 'cliente' => request('cliente')])) }}">{{ $rotulo }}</md-filled-tonal-button>
            @else
                <md-outlined-button href="{{ route('titulos.index', array_filter(['filtro' => $chave, 'cliente' => request('cliente')])) }}">{{ $rotulo }}</md-outlined-button>
            @endif
        @endforeach
        @if (request('cliente'))
            <md-text-button href="{{ route('titulos.index', ['filtro' => $filtro]) }}"><md-icon slot="icon">close</md-icon>Todos os clientes</md-text-button>
        @endif
    </nav>

    <div class="cartao tabela">
        <table>
            <thead>
            <tr>
                <th>{{ $filtro === 'pagos' ? 'Pago em' : 'Vencimento' }}</th>
                <th>Cliente</th>
                <th>Descrição</th>
                <th>Situação</th>
                <th class="num">Valor</th>
                <th class="num">{{ $filtro === 'pagos' ? 'Recebido' : 'Atualizado hoje' }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($titulos as $titulo)
                <tr onclick="location.href='{{ route('titulos.show', $titulo) }}'" style="cursor:pointer">
                    <td>{{ ($filtro === 'pagos' ? $titulo->pago_em : $titulo->vencimento)->format('d/m/Y') }}</td>
                    <td>{{ $titulo->cliente->nome }}</td>
                    <td>{{ $titulo->descricao }}</td>
                    <td>@include('partials.status', ['titulo' => $titulo])</td>
                    <td class="num">@reais($titulo->valor)</td>
                    <td class="num {{ $titulo->encargos() ? 'vermelho' : '' }}">
                        @reais($titulo->valor_pago ?? $titulo->valorAtualizado())
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="sutil">Nenhum título neste filtro.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="paginacao">{{ $titulos->links('partials.paginacao') }}</div>
    </div>
@endsection
