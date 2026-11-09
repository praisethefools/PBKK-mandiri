<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dosen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class DosenController extends Controller implements HasMiddleware
{
    /**
     * Daftarkan middleware controller (Slide 34 & 40: Pengaman Controller via permission Spatie).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kelola-dosen', except: ['destroy']),
            new Middleware('permission:hapus-dosen', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan daftar dosen dengan query kondisional when() dan withQueryString().
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $departemenId = $request->input('departemen_id');

        $dosens = Dosen::query()
            ->with(['departemen'])
            ->withCount(['mahasiswaBimbingan', 'tugas'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('keahlian', 'like', "%{$search}%");
                });
            })
            ->when($departemenId, function ($query, $departemenId) {
                return $query->where('departemen_id', $departemenId);
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $departemens = Departemen::orderBy('nama')->get();

        return view('admin.dosen.index', compact('dosens', 'departemens', 'search', 'departemenId'));
    }

    /**
     * Tampilkan formulir penambahan data dosen baru.
     */
    public function create(): View
    {
        $departemens = Departemen::orderBy('nama')->get();

        return view('admin.dosen.create', compact('departemens'));
    }

    /**
     * Simpan data dosen baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'nip' => 'required|string|max:30|unique:dosens,nip',
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:dosens,email',
            'keahlian' => 'required|string|max:150',
        ]);

        Dosen::create($validated);

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Data Dosen berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit data dosen.
     */
    public function edit(Dosen $dosen): View
    {
        $departemens = Departemen::orderBy('nama')->get();

        return view('admin.dosen.edit', compact('dosen', 'departemens'));
    }

    /**
     * Perbarui data dosen di database.
     */
    public function update(Request $request, Dosen $dosen): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'nip' => 'required|string|max:30|unique:dosens,nip,'.$dosen->id,
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:dosens,email,'.$dosen->id,
            'keahlian' => 'required|string|max:150',
        ]);

        $dosen->update($validated);

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Data Dosen berhasil diperbarui.');
    }

    /**
     * Hapus data dosen dari sistem.
     * Hak Eksklusif: Hanya Admin murni (Slide 25, 34, 39, 40)
     */
    public function destroy(Dosen $dosen): RedirectResponse
    {
        abort_unless(
            auth()->user()->can('hapus-dosen'),
            403,
            'Akses ditolak: Hanya peran Admin yang berhak menghapus data dosen.'
        );

        $nama = $dosen->nama;
        $dosen->delete();

        return redirect()->route('admin.dosen.index')
            ->with('success', "Data Dosen {$nama} berhasil dihapus dari sistem.");
    }
}
