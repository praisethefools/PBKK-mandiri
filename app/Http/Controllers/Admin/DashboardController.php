<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama admin dengan widget statistik agregat.
     */
    public function index(Request $request): View
    {
        // 1. Widget Dashboard Agregat (Slide 24 & 40)
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalTugasAktif = Tugas::where('status', 'Aktif')->count();

        // Statistik pendukung
        $totalMahasiswaAktif = Mahasiswa::where('is_active', true)->count();
        $totalMahasiswaCuti = Mahasiswa::where('is_active', false)->count();
        $totalDepartemen = Departemen::count();
        $rataRataIpk = round((float) Mahasiswa::avg('ipk'), 2);

        // Data tugas terbaru
        $tugasTerbaru = Tugas::with(['departemen', 'dosen'])
            ->latest()
            ->take(5)
            ->get();

        // Data distribusi per departemen
        $departemens = Departemen::withCount(['mahasiswas', 'dosens', 'tugas'])
            ->get();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalTugasAktif',
            'totalMahasiswaAktif',
            'totalMahasiswaCuti',
            'totalDepartemen',
            'rataRataIpk',
            'tugasTerbaru',
            'departemens'
        ));
    }
}
