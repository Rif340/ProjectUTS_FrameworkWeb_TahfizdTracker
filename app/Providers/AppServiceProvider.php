<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\TahunAkademik;

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
