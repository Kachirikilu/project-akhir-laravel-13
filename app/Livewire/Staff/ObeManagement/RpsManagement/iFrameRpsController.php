<?php

namespace App\Livewire\Staff\ObeManagement\RpsManagement;

use App\Models\Akademik\RPS;
use App\Models\ProgramStudi\Prodi;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Staff\ObeManagement\RpsManagement\WithRPSShow;
use App\Http\Controllers\Controller;

class iFrameRpsController extends Controller
{
    use WithRPSShow;

    public function preview($rpsId, $prId = null)
    {
        $rps = RPS::with(['mk_rel', 'tim_dosens', 'tim_dosens.dosens', 'cpmks.scpmks', 'refs', 'mk_rel.prodis'])->findOrFail($rpsId);
        if (!$rps->mk_rel || $rps->mk_rel->prodis->isEmpty()) {
            abort(404, 'Program Studi tidak ditemukan pada mata kuliah ini!');
        }

        $prodis = $rps->mk_rel->prodis->sortBy([
            ['nama_pr', 'asc'],
            ['strata', 'desc'],
        ]);
        $selectedPr = null;
        if ($prId) {
            $selectedPr = $prodis->find($prId);
        } 

        if (!$selectedPr) {
            $selectedPr = $prodis->firstWhere('id', Auth::user()->pr_id);
        }

        if (!$selectedPr) {
            $selectedPr = $prodis->first();
        }
        if (!$selectedPr) {
            abort(404, 'Data Program Studi tidak ditemukan!');
        }
        $prodi = Prodi::with(['dp_rel', 'dp_rel.fk_rel'])->findOrFail($selectedPr->id);
        $tim_dosen = $rps->tim_dosens->where('pr_id', $selectedPr->id);

        return view('staff.obe-management.rps-management.rps-pdf-print', compact('rps', 'prodi', 'tim_dosen'));
    }
}