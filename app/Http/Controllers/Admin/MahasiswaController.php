<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class MahasiswaController extends Controller implements HasMiddleware
{
    /**
     * Daftarkan middleware controller (Slide 34 & 40: Pengaman Controller via permission Spatie).
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:kelola-mahasiswa', except: ['destroy']),
            new Middleware('permission:hapus-mahasiswa', only: ['destroy']),
        ];
    }

    /**
     * Tampilkan daftar mahasiswa dengan query kondisional when() dan withQueryString().
     * (Slide 28, 29, 30, 35 & Challenge A+)
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $departemenId = $request->input('departemen_id');
        $status = $request->input('status');

        $mahasiswas = Mahasiswa::query()
            // Eager loading relasi mencegah masalah N+1 (Slide 31)
            ->with(['departemen', 'dosenWali'])
            // Kueri kondisional pencarian multi-kolom nama ATAU nrp via when() (Slide 28 & 35)
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nrp', 'like', "%{$search}%");
                });
            })
            // Kueri kondisional filter departemen via when() (Slide 30 & 36)
            ->when($departemenId, function ($query, $departemenId) {
                return $query->where('departemen_id', $departemenId);
            })
            // Kueri kondisional filter status aktif via when() (Slide 30)
            ->when($status !== null && $status !== '', function ($query) use ($status) {
                return $query->where('is_active', (bool) $status);
            })
            ->latest('id')
            ->paginate(10)
            // Kunci parameter pencarian saat berpindah halaman paginasi (Slide 29, 36)
            ->withQueryString();

        $departemens = Departemen::orderBy('nama')->get();

        return view('admin.mahasiswa.index', compact('mahasiswas', 'departemens', 'search', 'departemenId', 'status'));
    }

    /**
     * Tampilkan formulir penambahan data mahasiswa baru.
     */
    public function create(): View
    {
        $departemens = Departemen::orderBy('nama')->get();
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.mahasiswa.create', compact('departemens', 'dosens'));
    }

    /**
     * Simpan data mahasiswa baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'dosen_wali_id' => 'nullable|exists:dosens,id',
            'nrp' => 'required|string|max:20|unique:mahasiswas,nrp',
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mahasiswas,email',
            'angkatan' => 'required|integer|min:2018|max:2030',
            'ipk' => 'required|numeric|min:0.00|max:4.00',
            'is_active' => 'required|boolean',
        ]);

        Mahasiswa::create($validated);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data Mahasiswa berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit data mahasiswa.
     */
    public function edit(Mahasiswa $mahasiswa): View
    {
        $departemens = Departemen::orderBy('nama')->get();
        $dosens = Dosen::orderBy('nama')->get();

        return view('admin.mahasiswa.edit', compact('mahasiswa', 'departemens', 'dosens'));
    }

    /**
     * Perbarui data mahasiswa di database.
     */
    public function update(Request $request, Mahasiswa $mahasiswa): RedirectResponse
    {
        $validated = $request->validate([
            'departemen_id' => 'required|exists:departemens,id',
            'dosen_wali_id' => 'nullable|exists:dosens,id',
            'nrp' => 'required|string|max:20|unique:mahasiswas,nrp,'.$mahasiswa->id,
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:mahasiswas,email,'.$mahasiswa->id,
            'angkatan' => 'required|integer|min:2018|max:2030',
            'ipk' => 'required|numeric|min:0.00|max:4.00',
            'is_active' => 'required|boolean',
        ]);

        $mahasiswa->update($validated);

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', 'Data Mahasiswa berhasil diperbarui.');
    }

    /**
     * Hapus data mahasiswa dari sistem.
     * Hak Eksklusif: Hanya Admin murni (Slide 25, 34, 39, 40)
     */
    public function destroy(Mahasiswa $mahasiswa): RedirectResponse
    {
        // Otorisasi server-side berlapis mencegah manipulasi endpoint ilegal (Slide 25)
        abort_unless(
            auth()->user()->can('hapus-mahasiswa'),
            403,
            'Akses ditolak: Hanya peran Admin yang berhak menghapus data mahasiswa.'
        );

        $nama = $mahasiswa->nama;
        $mahasiswa->delete();

        return redirect()->route('admin.mahasiswa.index')
            ->with('success', "Data Mahasiswa {$nama} berhasil dihapus dari sistem.");
    }
}
