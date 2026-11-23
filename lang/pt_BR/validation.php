<?php

// Mensagens de validação usadas no sistema, em português.
return [
    'required' => 'O campo :attribute é obrigatório.',
    'email' => 'Informe um :attribute válido.',
    'string' => 'O campo :attribute deve ser um texto.',
    'integer' => 'Informe um :attribute válido.',
    'date' => 'Informe uma data válida para :attribute.',
    'exists' => 'O :attribute selecionado não existe.',
    'unique' => 'Já existe um cadastro com este :attribute.',
    'before_or_equal' => 'A :attribute não pode ser uma data futura.',
    'min' => ['string' => 'O campo :attribute deve ter pelo menos :min caracteres.', 'numeric' => 'O :attribute deve ser maior que zero.'],
    'max' => ['string' => 'O campo :attribute deve ter no máximo :max caracteres.', 'numeric' => 'O :attribute é alto demais.'],

    'attributes' => [
        'nome' => 'nome',
        'documento' => 'CPF/CNPJ',
        'email' => 'e-mail',
        'cliente_id' => 'cliente',
        'descricao' => 'descrição',
        'valor' => 'valor',
        'vencimento' => 'vencimento',
        'pago_em' => 'data do pagamento',
    ],
];
