<?php

namespace App\Http\Controllers;

use App\Enums\StatusTitulo;
use App\Models\Titulo;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

final class DashboardController extends Controller
{
    /** Faixas de atraso (aging) usadas na cobrança. */
    public const FAIXAS = [
        '1 a 30 dias' => [1, 30],
        '31 a 60 dias' => [31, 60],
        '61 a 90 dias' => [61, 90],
        'Mais de 90 dias' => [91, PHP_INT_MAX],
    ];

    public function __invoke(): View
    {
        $hoje = today();

        $vencidos = Titulo::vencidos($hoje)->get(['id', 'valor', 'vencimento']);
        $aging = collect(self::FAIXAS)->map(fn (array $faixa) => $vencidos
            ->filter(fn (Titulo $t) => $t->diasEmAtraso($hoje) >= $faixa[0] && $t->diasEmAtraso($hoje) <= $faixa[1])
            ->sum('valor'));

        $maioresDevedores = Titulo::vencidos($hoje)
            ->join('clientes', 'clientes.id', '=', 'titulos.cliente_id')
            ->groupBy('clientes.id', 'clientes.nome')
            ->orderByDesc('total')
            ->limit(5)
            ->get(['clientes.id', 'clientes.nome', DB::raw('SUM(titulos.valor) as total'), DB::raw('COUNT(*) as quantidade')]);

        return view('dashboard', [
            'aReceber' => (int) Titulo::emAberto()->sum('valor'),
            'vencido' => (int) $vencidos->sum('valor'),
            'recebidoNoMes' => (int) Titulo::where('status', StatusTitulo::Pago)
                ->whereBetween('pago_em', [$hoje->copy()->startOfMonth()->toDateString(), $hoje->toDateString()])
                ->sum('valor_pago'),
            'aging' => $aging,
            'maioresDevedores' => $maioresDevedores,
            'proximos' => Titulo::emAberto()->with('cliente')
                ->whereBetween('vencimento', [$hoje->toDateString(), $hoje->copy()->addDays(7)->toDateString()])
                ->orderBy('vencimento')->limit(8)->get(),
        ]);
    }
}
