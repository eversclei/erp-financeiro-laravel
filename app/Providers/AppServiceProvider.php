<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // @reais($centavos) → "R$ 1.234,56"
        Blade::directive('reais', fn (string $expressao) => "<?php echo e(\\App\\Support\\Dinheiro::formatar({$expressao})); ?>");
    }
}
