<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TahunAkademik;

use Illuminate\Support\Facades\URL;

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
        if (config('app.env') === 'production' || str_contains(config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }
        View::composer('*', function ($view) {
            $activeYearId = session('active_tahun_akademik_id');
            $tahunList = TahunAkademik::orderBy('tanggal_mulai', 'desc')->get();
            $activeTahun = $tahunList->firstWhere('id_tahun_akademik', $activeYearId)
                           ?? $tahunList->firstWhere('is_aktif', true)
                           ?? $tahunList->first();
            $isYearActive = $activeTahun ? (bool)$activeTahun->is_aktif : true;

            $view->with([
                'currentActiveTahun'  => $activeTahun,
                'isCurrentYearActive' => $isYearActive,
            ]);
        });
    }
}
