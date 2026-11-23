<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Financeiro') · Contas a Receber</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

    {{-- Material Web: componentes Material 3 oficiais do Google. --}}
    <script type="importmap">
        { "imports": { "@material/web/": "https://esm.run/@material/web/" } }
    </script>
    <script type="module">
        import '@material/web/all.js';
        import { styles as tipografia } from '@material/web/typography/md-typescale-styles.js';
        document.adoptedStyleSheets.push(tipografia.styleSheet);
    </script>

    <style>
        /* Tokens Material 3 (paleta verde-azulada para o módulo financeiro). */
        :root {
            --md-sys-color-primary: #006a60;
            --md-sys-color-on-primary: #ffffff;
            --md-sys-color-primary-container: #74f8e5;
            --md-sys-color-on-primary-container: #00201c;
            --md-sys-color-secondary-container: #cce8e3;
            --md-sys-color-on-secondary-container: #051f1c;
            --md-sys-color-tertiary-container: #d2e4ff;
            --md-sys-color-on-tertiary-container: #001c37;
            --md-sys-color-error: #ba1a1a;
            --md-sys-color-error-container: #ffdad6;
            --md-sys-color-on-error-container: #410002;
            --md-sys-color-surface: #f4fbf9;
            --md-sys-color-surface-container-low: #eef5f3;
            --md-sys-color-surface-container: #e9efed;
            --md-sys-color-surface-container-high: #e3eae8;
            --md-sys-color-on-surface: #161d1c;
            --md-sys-color-on-surface-variant: #3f4947;
            --md-sys-color-outline: #6f7977;
            --md-sys-color-outline-variant: #bec9c6;
            --md-ref-typeface-brand: 'Roboto';
            --md-ref-typeface-plain: 'Roboto';
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: Roboto, sans-serif; background: var(--md-sys-color-surface); color: var(--md-sys-color-on-surface); }
        a { color: inherit; }
        .app { display: grid; grid-template-columns: 96px 1fr; min-height: 100vh; }
        .trilho { display: flex; flex-direction: column; align-items: center; gap: 12px; padding: 16px 0;
            background: var(--md-sys-color-surface-container); }
        .trilho .logo { --md-icon-size: 32px; color: var(--md-sys-color-primary); margin-bottom: 16px; }
        .trilho a { display: flex; flex-direction: column; align-items: center; gap: 4px; text-decoration: none; width: 80px; }
        .trilho a span.pilula { display: grid; place-items: center; width: 56px; height: 32px; border-radius: 16px; }
        .trilho a.ativo span.pilula { background: var(--md-sys-color-secondary-container); }
        main { padding: 24px; max-width: 1200px; width: 100%; margin: 0 auto; }
        .cartao { background: var(--md-sys-color-surface-container-low); border: 1px solid var(--md-sys-color-outline-variant);
            border-radius: 16px; padding: 20px; }
        .aviso { display: flex; align-items: center; gap: 8px; padding: 12px 16px; border-radius: 12px; margin-bottom: 16px; }
        .aviso.sucesso { background: var(--md-sys-color-secondary-container); color: var(--md-sys-color-on-secondary-container); }
        .aviso.erro { background: var(--md-sys-color-error-container); color: var(--md-sys-color-on-error-container); }
        .cabecalho { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
        .cabecalho h1 { margin: 0; }
        .campo { width: 100%; margin-bottom: 16px; }
        .acoes { display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
        .sutil { color: var(--md-sys-color-on-surface-variant); }
        .vermelho { color: var(--md-sys-color-error); }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid var(--md-sys-color-outline-variant); }
        th { color: var(--md-sys-color-on-surface-variant); font-weight: 500; }
        td.num, th.num { text-align: right; white-space: nowrap; }
        tbody tr:hover { background: var(--md-sys-color-surface-container-high); }
        .tabela { overflow-x: auto; }
        .paginacao { margin-top: 16px; }
        @media (max-width: 700px) {
            .app { grid-template-columns: 1fr; }
            .trilho { flex-direction: row; justify-content: space-around; position: sticky; bottom: 0; order: 2; }
            .trilho .logo { display: none; }
        }
    </style>
    @stack('estilos')
</head>
<body>
<div class="app">
    <nav class="trilho" aria-label="Navegação principal">
        <md-icon class="logo">account_balance</md-icon>
        @foreach ([['dashboard', 'Painel', 'dashboard'], ['titulos.index', 'Títulos', 'receipt_long'], ['clientes.index', 'Clientes', 'groups']] as [$rota, $rotulo, $icone])
            @php $ativo = request()->routeIs(str_replace('.index', '.*', $rota)); @endphp
            <a href="{{ route($rota) }}" class="{{ $ativo ? 'ativo' : '' }}" @if ($ativo) aria-current="page" @endif>
                <span class="pilula"><md-icon>{{ $icone }}</md-icon></span>
                <span class="md-typescale-label-medium">{{ $rotulo }}</span>
            </a>
        @endforeach
    </nav>

    <main>
        @if (session('sucesso'))
            <div class="aviso sucesso" role="status"><md-icon>check_circle</md-icon>{{ session('sucesso') }}</div>
        @endif
        @error('geral')
            <div class="aviso erro" role="alert"><md-icon>error</md-icon>{{ $message }}</div>
        @enderror

        @yield('conteudo')
    </main>
</div>
</body>
</html>
