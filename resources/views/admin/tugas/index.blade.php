@extends('adminlte::page')

@section('title', 'Daftar Tugas Kuliah')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Manajemen Tugas Kuliah</h1>
            <p class="text-muted mb-0 small">Daftar Penugasan Akademik & Proyek Kuliah FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.tugas.create') }}" class="btn btn-warning btn-sm shadow-sm text-dark font-weight-bold">
            <i class="bi bi-plus-circle mr-1"></i> Tambah Tugas Baru
        </a>
    </div>
@stop

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Form Multi-Filter via when() (Slide 36) -->
    <div class="card card-outline card-warning shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-funnel mr-1"></i> Filter Pencarian Tugas Kuliah (Slide 36)
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tugas.index') }}">
                <div class="row">
                    <div class="col-md-5 mb-2">
                        <label class="small font-weight-bold">Kata Kunci (Judul Tugas / Mata Kuliah)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Ketik judul tugas atau mata kuliah..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="small font-weight-bold">Departemen</label>
                        <select name="departemen_id" class="form-control">
                            <option value="">-- Semua Departemen FTEIC --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <label class="small font-weight-bold">Status Tugas</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ request('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('admin.tugas.index') }}" class="btn btn-secondary btn-sm mr-2">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-warning btn-sm font-weight-bold text-dark">
                        <i class="bi bi-filter mr-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Tugas -->
    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-journal-check mr-1"></i> Daftar Tugas Kuliah
                <span class="badge badge-warning text-dark ml-2">{{ $tugas->total() }} Data Ditemukan</span>
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Judul Tugas</th>
                            <th>Mata Kuliah & Departemen</th>
                            <th>Dosen Pengampu</th>
                            <th class="text-center">Tenggat Waktu</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tugas as $index => $t)
                            <tr>
                                <td>{{ $tugas->firstItem() + $index }}</td>
                                <td>
                                    <div class="font-weight-bold text-dark">{{ $t->judul }}</div>
                                    @if($t->deskripsi)
                                        <small class="text-muted d-block text-truncate" style="max-width: 340px;">
                                            {{ $t->deskripsi }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $t->mata_kuliah }}</div>
                                    <span class="badge badge-light border">{{ $t->departemen->kode ?? '-' }}</span>
                                </td>
                                <td>
                                    <small class="font-weight-bold text-dark">{{ $t->dosen->nama ?? '-' }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $t->deadline->isPast() ? 'badge-danger' : 'badge-light border' }} px-2 py-1">
                                        <i class="bi bi-calendar mr-1"></i> {{ $t->deadline->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $t->status === 'Aktif' ? 'badge-warning text-dark' : 'badge-success' }} px-2 py-1">
                                        {{ $t->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.tugas.edit', $t->id) }}"
                                           class="btn btn-info btn-sm"
                                           title="Edit Tugas">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Hak Eksklusif Hapus Data: Hanya Admin murni (Slide 20, 25, 34, 39, 40) --}}
                                        @can('hapus-tugas')
                                            <form action="{{ route('admin.tugas.destroy', $t->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus penugasan \'{{ $t->judul }}\'?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus Tugas (Hak Eksklusif Admin)">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada data tugas kuliah yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tugas->hasPages())
            <div class="card-footer clearfix bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <small class="text-muted">
                        Menampilkan {{ $tugas->firstItem() }} sampai {{ $tugas->lastItem() }} dari {{ $tugas->total() }} data
                    </small>
                    <div>
                        {{ $tugas->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

