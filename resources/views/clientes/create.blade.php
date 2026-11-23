@extends('layouts.app')

@section('titulo', 'Novo cliente')

@section('conteudo')
    <md-text-button href="{{ route('clientes.index') }}"><md-icon slot="icon">arrow_back</md-icon>Clientes</md-text-button>

    <form method="POST" action="{{ route('clientes.store') }}" class="cartao" style="max-width:640px;margin-top:8px">
        @csrf
        <h1 class="md-typescale-headline-small" style="margin-top:0">Novo cliente</h1>

        <md-outlined-text-field class="campo" label="Nome ou razão social" name="nome" required maxlength="150"
            value="{{ old('nome') }}"
            @error('nome') error error-text="{{ $message }}" @enderror></md-outlined-text-field>

        <md-outlined-text-field class="campo" label="CPF ou CNPJ" name="documento" required inputmode="numeric"
            value="{{ old('documento') }}"
            @error('documento') error error-text="{{ $message }}" @enderror></md-outlined-text-field>

        <md-outlined-text-field class="campo" label="E-mail do financeiro" name="email" type="email"
            value="{{ old('email') }}"
            @error('email') error error-text="{{ $message }}" @enderror></md-outlined-text-field>

        <div class="acoes">
            <md-text-button href="{{ route('clientes.index') }}">Cancelar</md-text-button>
            <md-filled-button type="submit">Cadastrar</md-filled-button>
        </div>
    </form>
@endsection
