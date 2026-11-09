@extends('adminlte::page')

@section('title', 'Data Mahasiswa')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Manajemen Data Mahasiswa</h1>
            <p class="text-muted mb-0 small">Daftar Mahasiswa Terdaftar di Lingkungan FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.mahasiswa.create') }}" class="btn btn-primary btn-sm shadow-sm">
            <i class="bi bi-plus-circle mr-1"></i> Tambah Mahasiswa
        </a>
    </div>
@stop

@section('content')
    {{-- Notifikasi Flash Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- TANTANGAN A+ (Slide 28, 30, 35, 41): Form Pencarian & Multi-Variabel Filter via when() --}}
    <div class="card card-outline card-primary shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-funnel-fill mr-1"></i> Filter Pencarian Data Tingkat Lanjut (Challenge A+)
            </h3>
            <div class="card-tools">
                <span class="badge badge-light border">Query when() & withQueryString()</span>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.mahasiswa.index') }}">
                <div class="row">
                    <!-- 1. Keyword Search (Nama / NRP) - Slide 30 & 35 -->
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Kata Kunci (Nama atau NRP)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Ketik nama atau NRP mahasiswa..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- 2. Departemen Filter - Slide 30 & 36 -->
                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Jurusan / Departemen</label>
                        <select name="departemen_id" class="form-control">
                            <option value="">-- Semua Departemen FTEIC --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 3. Status Filter (Toggle Aktif/Cuti) - Slide 30 -->
                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Status Akademik</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Cuti Akademik</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary btn-sm mr-2">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset Filter
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Mahasiswa -->
    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-table mr-1"></i> Daftar Mahasiswa
                <span class="badge badge-info ml-2">{{ $mahasiswas->total() }} Data Ditemukan</span>
            </h3>
            <div class="card-tools">
                @cannot('hapus-mahasiswa')
                    <span class="badge badge-warning text-dark px-2 py-1">
                        <i class="bi bi-shield-exclamation mr-1"></i> Peran Staf: Hak Hapus Dikunci
                    </span>
                @endcannot
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>NRP</th>
                            <th>Nama Mahasiswa</th>
                            <th>Departemen</th>
                            <th>Dosen Wali</th>
                            <th class="text-center">Angkatan</th>
                            <th class="text-center">IPK</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 160px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mahasiswas as $index => $mhs)
                            <tr>
                                <td>{{ $mahasiswas->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge badge-light border font-monospace font-weight-bold">
                                        {{ $mhs->nrp }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $mhs->nama }}</div>
                                    <small class="text-muted">{{ $mhs->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $mhs->departemen->kode ?? '-' }}</span>
                                    <small class="d-block text-muted">{{ $mhs->departemen->nama ?? '-' }}</small>
                                </td>
                                <td>
                                    <small class="font-weight-bold text-dark">
                                        {{ $mhs->dosenWali->nama ?? 'Belum Ditentukan' }}
                                    </small>
                                </td>
                                <td class="text-center">{{ $mhs->angkatan }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $mhs->ipk >= 3.5 ? 'badge-success' : 'badge-info' }} px-2 py-1">
                                        {{ number_format($mhs->ipk, 2) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $mhs->is_active ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                                        {{ $mhs->is_active ? 'Aktif' : 'Cuti' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Tombol Edit (Dapat diakses Admin dan Staf) -->
                                        <a href="{{ route('admin.mahasiswa.edit', $mhs->id) }}"
                                           class="btn btn-info btn-sm"
                                           title="Edit Data">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- HAK EKSKLUSIF (Slide 20, 25, 39, 40): Tombol Hapus Hanya Dirender untuk Admin via @can --}}
                                        @can('hapus-mahasiswa')
                                            <form action="{{ route('admin.mahasiswa.destroy', $mhs->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data mahasiswa {{ $mhs->nama }} ({{ $mhs->nrp }})? Tindakan ini permanen!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus Data (Hak Eksklusif Admin)">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-search fa-2x d-block mb-2 text-muted"></i>
                                    Tidak ada data mahasiswa yang cocok dengan kriteria pencarian Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- PENGUNCI LINK PAGINATION (Slide 29, 36 & Challenge A+) --}}
        @if($mahasiswas->hasPages())
            <div class="card-footer clearfix bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <small class="text-muted">
                        Menampilkan {{ $mahasiswas->firstItem() }} sampai {{ $mahasiswas->lastItem() }} dari {{ $mahasiswas->total() }} data
                    </small>
                    <div>
                        {{ $mahasiswas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

