@extends('layouts.app')

@section('titulo', 'Lançar título')

@section('conteudo')
    <md-text-button href="{{ route('titulos.index') }}"><md-icon slot="icon">arrow_back</md-icon>Títulos</md-text-button>

    <form method="POST" action="{{ route('titulos.store') }}" class="cartao" style="max-width:640px;margin-top:8px">
        @csrf
        <h1 class="md-typescale-headline-small" style="margin-top:0">Lançar título a receber</h1>

        <md-outlined-select class="campo" label="Cliente" name="cliente_id" required
            @error('cliente_id') error error-text="{{ $message }}" @enderror>
            @foreach ($clientes as $cliente)
                <md-select-option value="{{ $cliente->id }}" @selected((int) old('cliente_id', $clienteSelecionado) === $cliente->id)>
                    <div slot="headline">{{ $cliente->nome }}</div>
                </md-select-option>
            @endforeach
        </md-outlined-select>

        <md-outlined-text-field class="campo" label="Descrição" name="descricao" required maxlength="200"
            value="{{ old('descricao') }}"
            @error('descricao') error error-text="{{ $message }}" @enderror></md-outlined-text-field>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
            <md-outlined-text-field class="campo" label="Valor" name="valor" required prefix-text="R$ " inputmode="decimal"
                placeholder="0,00" value="{{ old('valor') }}"
                @error('valor') error error-text="{{ $message }}" @enderror></md-outlined-text-field>

            <md-outlined-text-field class="campo" label="Vencimento" name="vencimento" type="date" required
                value="{{ old('vencimento', today()->addDays(30)->toDateString()) }}"
                @error('vencimento') error error-text="{{ $message }}" @enderror></md-outlined-text-field>
        </div>

        <div class="acoes">
            <md-text-button href="{{ route('titulos.index') }}">Cancelar</md-text-button>
            <md-filled-button type="submit">Lançar</md-filled-button>
        </div>
    </form>
@endsection
