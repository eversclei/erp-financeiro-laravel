@php
    /** @var \App\Models\Titulo $titulo */
    $vencido = $titulo->status === \App\Enums\StatusTitulo::Aberto && $titulo->diasEmAtraso() > 0;
    [$fundo, $texto, $icone, $rotulo] = match (true) {
        $vencido => ['var(--md-sys-color-error-container)', 'var(--md-sys-color-on-error-container)', 'warning', 'Vencido há '.$titulo->diasEmAtraso().'d'],
        $titulo->status === \App\Enums\StatusTitulo::Pago => ['var(--md-sys-color-secondary-container)', 'var(--md-sys-color-on-secondary-container)', 'task_alt', 'Pago'],
        $titulo->status === \App\Enums\StatusTitulo::Cancelado => ['var(--md-sys-color-surface-container-high)', 'var(--md-sys-color-on-surface-variant)', 'block', 'Cancelado'],
        default => ['var(--md-sys-color-tertiary-container)', 'var(--md-sys-color-on-tertiary-container)', 'schedule', 'A vencer'],
    };
@endphp
<span class="md-typescale-label-medium"
      style="display:inline-flex;align-items:center;gap:4px;padding:2px 10px 2px 6px;border-radius:999px;white-space:nowrap;background:{{ $fundo }};color:{{ $texto }}">
    <md-icon style="--md-icon-size:16px">{{ $icone }}</md-icon>{{ $rotulo }}
</span>
