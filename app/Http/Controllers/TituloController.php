<?php

namespace App\Http\Controllers;

use App\Enums\StatusTitulo;
use App\Models\Cliente;
use App\Models\Titulo;
use App\Support\Dinheiro;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class TituloController extends Controller
{
    public function index(Request $request): View
    {
        $filtro = $request->query('filtro', 'abertos');

        $titulos = Titulo::with('cliente')
            ->when($filtro === 'abertos', fn ($q) => $q->emAberto())
            ->when($filtro === 'vencidos', fn ($q) => $q->vencidos())
            ->when($filtro === 'pagos', fn ($q) => $q->where('status', StatusTitulo::Pago))
            ->when($request->integer('cliente'), fn ($q, $id) => $q->where('cliente_id', $id))
            ->orderBy($filtro === 'pagos' ? 'pago_em' : 'vencimento', $filtro === 'pagos' ? 'desc' : 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('titulos.index', compact('titulos', 'filtro'));
    }

    public function create(Request $request): View
    {
        return view('titulos.create', [
            'clientes' => Cliente::orderBy('nome')->get(['id', 'nome']),
            'clienteSelecionado' => $request->integer('cliente') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge(['valor' => Dinheiro::paraCentavos($request->input('valor'))]);

        $dados = $request->validate([
            'cliente_id' => ['required', 'exists:clientes,id'],
            'descricao' => ['required', 'string', 'min:3', 'max:200'],
            'valor' => ['required', 'integer', 'min:1', 'max:100000000000'],
            'vencimento' => ['required', 'date'],
        ]);

        $titulo = Titulo::create($dados);

        return redirect()->route('titulos.show', $titulo)->with('sucesso', 'Título lançado.');
    }

    public function show(Titulo $titulo): View
    {
        return view('titulos.show', ['titulo' => $titulo->load('cliente')]);
    }

    public function pagar(Request $request, Titulo $titulo): RedirectResponse
    {
        $dados = $request->validate(['pago_em' => ['required', 'date', 'before_or_equal:today']]);

        $this->executar(fn () => $titulo->registrarPagamento(Carbon::parse($dados['pago_em'])));

        return redirect()->route('titulos.show', $titulo)
            ->with('sucesso', 'Pagamento registrado: '.Dinheiro::formatar($titulo->valor_pago).'.');
    }

    public function cancelar(Titulo $titulo): RedirectResponse
    {
        $this->executar(fn () => $titulo->cancelar());

        return redirect()->route('titulos.show', $titulo)->with('sucesso', 'Título cancelado.');
    }

    /** Regras de domínio violadas voltam para a tela como erro de validação. */
    private function executar(callable $acao): void
    {
        try {
            $acao();
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['geral' => $e->getMessage()]);
        }
    }
}
