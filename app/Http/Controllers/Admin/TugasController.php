<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Tugas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class TugasController extends Controller implements HasMiddleware
{
    /**
     * Daftarkan middleware controller (Slide 34 & 40: Constructor middleware permission:hapus-tugas).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kelola-tugas', except: ['destroy']),
            new Middleware('permission:hapus-tugas', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan daftar tugas dengan query kondisional when() dan withQueryString().
     * (Slide 34 & 36)
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $departemenId = $request->input('departemen_id');
        $status = $request->input('status');

        $tugas = Tugas::query()
            ->with(['departemen', 'dosen'])
            // Kueri kondisional filter search box (Slide 36)
            ->when($search, function ($q, $search) {
                return $q->where(function ($sub) use ($search) {
                    $sub->where('judul', 'like', "%{$search}%")
                        ->orWhere('mata_kuliah', 'like', "%{$search}%");
                });
            })
            // Kueri kondisional filter dropdown departemen (Slide 36)
            ->when($departemenId, function ($q, $deptId) {
                return $q->where('departemen_id', $deptId);
            })
            // Kueri kondisional filter status (Aktif/Selesai)
            ->when($status, function ($q, $status) {
                return $q->where('status', $status);
            })
            ->latest('id')
            ->paginate(10)
            // Kunci query parameter agar filter tidak hilang saat berpindah halaman (Slide 29 & 36)
            ->withQueryString();

        $departemens = Departemen::orderBy('nama')->get();

        return view('admin.tugas.index', compact('tugas', 'departemens', 'search', 'departemenId', 'status'));
    }

    /**
     * Tampilkan formulir penambahan data tugas baru.
     */
    public function create(): View
    {
        $departemens = Departemen::orderBy('nama')->get();
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.tugas.create', compact('departemens', 'dosens'));
    }

    /**
     * Simpan data tugas baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'dosen_id' => 'required|exists:dosens,id',
            'judul' => 'required|string|max:200',
            'mata_kuliah' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|in:Aktif,Selesai',
        ]);

        Tugas::create($validated);

        return redirect()->route('admin.tugas.index')
            ->with('success', 'Data Tugas Kuliah berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit data tugas.
     */
    public function edit(Tugas $tugas): View
    {
        $departemens = Departemen::orderBy('nama')->get();
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.tugas.edit', compact('tugas', 'departemens', 'dosens'));
    }

    /**
     * Perbarui data tugas di database.
     */
    public function update(Request $request, Tugas $tugas): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'dosen_id' => 'required|exists:dosens,id',
            'judul' => 'required|string|max:200',
            'mata_kuliah' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'deadline' => 'required|date',
            'status' => 'required|in:Aktif,Selesai',
        ]);

        $tugas->update($validated);

        return redirect()->route('admin.tugas.index')
            ->with('success', 'Data Tugas Kuliah berhasil diperbarui.');
    }

    /**
     * Hapus data tugas dari sistem.
     * Hak Eksklusif: Hanya Admin murni (Slide 25, 34, 39, 40)
     */
    public function destroy(Tugas $tugas): RedirectResponse
    {
        abort_unless(
            auth()->user()->can('hapus-tugas'),
            403,
            'Akses ditolak: Hanya peran Admin yang berhak menghapus data tugas kuliah.'
        );

        $judul = $tugas->judul;
        $tugas->delete();

        return redirect()->route('admin.tugas.index')
            ->with('success', "Tugas '{$judul}' berhasil dihapus dari sistem.");
    }
}
