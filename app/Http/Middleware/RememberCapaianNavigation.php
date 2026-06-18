<?php

namespace App\Http\Middleware;

use App\Models\Akademik\CPL;
use App\Models\ProgramStudi\Prodi;
use Closure;
use Illuminate\Http\Request;

class RememberCapaianNavigation
{
    public function handle(Request $request, Closure $next)
    {
        $routeName = $request->route()?->getName();

        /*
        |--------------------------------------------------------------------------
        | CAPAIAN MANAGEMENT
        |--------------------------------------------------------------------------
        */
        if ($routeName === 'capaian-management') {

            $kodePr = $request->route('kode_pr');

            $prodiExists = Prodi::query()
                ->get()
                ->contains(function ($prodi) use ($kodePr) {
                    return $prodi->kode === $kodePr;
                });

            if (! $prodiExists) {
                return $next($request);
            }

            $history = session('prodi.history', []);

            unset($history[$kodePr]);

            $history[$kodePr] = [
                'kode_pr' => $kodePr,
                'url' => route('capaian-management', [
                    'kode_pr' => $kodePr,
                ]),
            ];

            $history = array_slice(
                $history,
                -5,
                null,
                true
            );

            ksort($history);

            session([
                'prodi.history' => $history,
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | RPS CAPAIAN MANAGEMENT
        |--------------------------------------------------------------------------
        */
        // if ($routeName === 'rps-capaian-management') {

        //     $kodePr = $request->route('kode_pr');
        //     $kodeCPL = $request->route('kode_cpl');

        //     $cplExists = CPL::query()
        //         ->get()
        //         ->contains(function ($cpl) use ($kodeCPL) {
        //             return $cpl->kode === $kodeCPL;
        //         });


        //     if (! $cplExists) {
        //         return $next($request);
        //     }

        //     $history = session('cpl.history', []);

        //     $key = $kodePr.'_'.$kodeCPL;

        //     unset($history[$key]);

        //     $history[$key] = [
        //         'kode_pr' => $kodePr,
        //         'kode_cpl' => $kodeCPL,
        //         'url' => route('rps-capaian-management', [
        //             'kode_pr' => $kodePr,
        //             'kode_cpl' => $kodeCPL,
        //         ]),
        //     ];

        //     $history = array_slice(
        //         $history,
        //         -15,
        //         null,
        //         true
        //     );

        //     uasort($history, function ($a, $b) {

        //         $prodiCompare = strcmp(
        //             $a['kode_pr'],
        //             $b['kode_pr']
        //         );

        //         return $prodiCompare !== 0
        //             ? $prodiCompare
        //             : strcmp(
        //                 $a['kode_cpl'],
        //                 $b['kode_cpl']
        //             );
        //     });

        //     session([
        //         'cpl.history' => $history,
        //     ]);
        // }

        return $next($request);
    }
}