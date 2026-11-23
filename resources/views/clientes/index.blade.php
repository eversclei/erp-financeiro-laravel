@extends('layouts.app')

@section('titulo', 'Clientes')

@section('conteudo')
    <div class="cabecalho">
        <h1 class="md-typescale-headline-medium">Clientes</h1>
        <md-filled-button href="{{ route('clientes.create') }}"><md-icon slot="icon">person_add</md-icon>Novo cliente</md-filled-button>
    </div>

    <form method="GET" action="{{ route('clientes.index') }}" style="margin-bottom:16px">
        <md-outlined-text-field class="campo" label="Buscar por nome ou CPF/CNPJ" name="busca" value="{{ $busca }}">
            <md-icon slot="leading-icon">search</md-icon>
        </md-outlined-text-field>
    </form>

    <div class="cartao tabela">
        <table>
            <thead><tr><th>Nome</th><th>CPF/CNPJ</th><th>E-mail</th><th class="num">Em aberto</th><th class="num">Vencidos</th><th></th></tr></thead>
            <tbody>
            @forelse ($clientes as $cliente)
                <tr>
                    <td>{{ $cliente->nome }}</td>
                    <td>{{ $cliente->documentoFormatado() }}</td>
                    <td>{{ $cliente->email ?? '—' }}</td>
                    <td class="num">@reais((int) $cliente->em_aberto)</td>
                    <td class="num {{ $cliente->vencidos ? 'vermelho' : '' }}">{{ $cliente->vencidos }}</td>
                    <td class="num">
                        <md-icon-button href="{{ route('titulos.index', ['cliente' => $cliente->id]) }}" aria-label="Ver títulos de {{ $cliente->nome }}">
                            <md-icon>receipt_long</md-icon>
                        </md-icon-button>
                        <md-icon-button href="{{ route('titulos.create', ['cliente' => $cliente->id]) }}" aria-label="Lançar título para {{ $cliente->nome }}">
                            <md-icon>add_card</md-icon>
                        </md-icon-button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="sutil">Nenhum cliente encontrado.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="paginacao">{{ $clientes->links('partials.paginacao') }}</div>
    </div>
@endsection
