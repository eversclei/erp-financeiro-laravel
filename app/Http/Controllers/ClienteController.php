<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Rules\CpfOuCnpj;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->query('busca'));
        $digitos = preg_replace('/\D/', '', $busca);

        $clientes = Cliente::query()
            ->when($busca !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('nome', 'like', "%{$busca}%")
                ->when($digitos !== '', fn ($d) => $d->orWhere('documento', 'like', "%{$digitos}%"))))
            ->withSum(['titulos as em_aberto' => fn ($q) => $q->emAberto()], 'valor')
            ->withCount(['titulos as vencidos' => fn ($q) => $q->vencidos()])
            ->orderBy('nome')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', compact('clientes', 'busca'));
    }

    public function create(): View
    {
        return view('clientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['documento' => preg_replace('/\D/', '', (string) $request->input('documento'))]);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'min:3', 'max:150'],
            'documento' => ['required', new CpfOuCnpj, Rule::unique('clientes', 'documento')],
            'email' => ['nullable', 'email', 'max:160'],
        ]);

        Cliente::create($dados);

        return redirect()->route('clientes.index')->with('sucesso', 'Cliente cadastrado.');
    }
}
