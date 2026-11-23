<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150);
            $table->string('documento', 14)->unique()->comment('CPF ou CNPJ, só dígitos');
            $table->string('email', 160)->nullable();
            $table->timestamps();
        });

        // Valores em centavos (inteiros) para não haver erro de arredondamento.
        Schema::create('titulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->restrictOnDelete();
            $table->string('descricao', 200);
            $table->unsignedBigInteger('valor');
            $table->date('vencimento');
            $table->string('status', 20)->default('aberto');
            $table->date('pago_em')->nullable();
            $table->unsignedBigInteger('valor_pago')->nullable();
            $table->unsignedBigInteger('encargos_pagos')->nullable();
            $table->timestamps();

            $table->index(['status', 'vencimento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('titulos');
        Schema::dropIfExists('clientes');
    }
};
