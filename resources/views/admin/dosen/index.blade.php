@extends('adminlte::page')

@section('title', 'Data Staf Dosen')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Manajemen Data Staf Dosen</h1>
            <p class="text-muted mb-0 small">Daftar Dosen Pengajar & Dosen Wali Fakultas FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.dosen.create') }}" class="btn btn-success btn-sm shadow-sm">
            <i class="bi bi-plus-circle mr-1"></i> Tambah Dosen
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

    <!-- Form Filter Pencarian via when() -->
    <div class="card card-outline card-success shadow-sm mb-4">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-search mr-1"></i> Filter Pencarian Staf Dosen
            </h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dosen.index') }}">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold">Kata Kunci (Nama / NIP / Bidang Keahlian)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                            </div>
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Ketik nama, NIP, atau keahlian dosen..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label class="small font-weight-bold">Departemen / Jurusan</label>
                        <select name="departemen_id" class="form-control">
                            <option value="">-- Semua Departemen FTEIC --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ request('departemen_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary btn-sm mr-2">
                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="bi bi-filter mr-1"></i> Cari Dosen
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel Data Dosen -->
    <div class="card card-outline card-secondary shadow-sm">
        <div class="card-header border-0 d-flex justify-content-between align-items-center">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-person-lines-fill mr-1"></i> Daftar Staf Dosen
                <span class="badge badge-success ml-2">{{ $dosens->total() }} Data Ditemukan</span>
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>NIP</th>
                            <th>Nama & Gelar</th>
                            <th>Departemen</th>
                            <th>Bidang Keahlian</th>
                            <th class="text-center">Bimbingan</th>
                            <th class="text-center">Tugas Diampu</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dosens as $index => $d)
                            <tr>
                                <td>{{ $dosens->firstItem() + $index }}</td>
                                <td>
                                    <span class="badge badge-light border font-monospace">
                                        {{ $d->nip }}
                                    </span>
                                </td>
                                <td>
                                    <div class="font-weight-bold">{{ $d->nama }}</div>
                                    <small class="text-muted">{{ $d->email }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $d->departemen->kode ?? '-' }}</span>
                                    <small class="d-block text-muted">{{ $d->departemen->nama ?? '-' }}</small>
                                </td>
                                <td>
                                    <span class="text-dark small">{{ $d->keahlian }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-secondary px-2 py-1">
                                        {{ $d->mahasiswa_bimbingan_count }} Mahasiswa
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-warning px-2 py-1">
                                        {{ $d->tugas_count }} Tugas
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.dosen.edit', $d->id) }}"
                                           class="btn btn-info btn-sm"
                                           title="Edit Dosen">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        {{-- Hak Eksklusif Hapus Data: Hanya Admin murni --}}
                                        @can('hapus-dosen')
                                            <form action="{{ route('admin.dosen.destroy', $d->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus data dosen {{ $d->nama }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-danger btn-sm"
                                                        title="Hapus Dosen (Hak Eksklusif Admin)">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    Tidak ada data dosen yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($dosens->hasPages())
            <div class="card-footer clearfix bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <small class="text-muted">
                        Menampilkan {{ $dosens->firstItem() }} sampai {{ $dosens->lastItem() }} dari {{ $dosens->total() }} data
                    </small>
                    <div>
                        {{ $dosens->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@stop

