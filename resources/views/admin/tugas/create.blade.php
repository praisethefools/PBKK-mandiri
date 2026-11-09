@extends('adminlte::page')

@section('title', 'Tambah Tugas Kuliah')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Tambah Penugasan Kuliah Baru</h1>
            <p class="text-muted mb-0 small">Publikasikan Tugas Baru untuk Mahasiswa FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.tugas.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-warning shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-plus-square-fill mr-1"></i> Formulir Penugasan Akademik
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.tugas.store') }}">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <h6 class="font-weight-bold mb-1"><i class="bi bi-exclamation-triangle-fill mr-1"></i> Terjadi kesalahan input:</h6>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-3">
                    <label class="font-weight-bold">Judul Penugasan <span class="text-danger">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                           value="{{ old('judul') }}" placeholder="Contoh: Tugas 4: Integrasi Dashboard & RBAC" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="mata_kuliah" class="form-control @error('mata_kuliah') is-invalid @enderror"
                               value="{{ old('mata_kuliah') }}" placeholder="Contoh: Pemrograman Berbasis Kerangka Kerja" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Departemen Homebase <span class="text-danger">*</span></label>
                        <select name="departemen_id" class="form-control @error('departemen_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Departemen --</option>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ old('departemen_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Dosen Pengampu / Penanggung Jawab <span class="text-danger">*</span></label>
                        <select name="dosen_id" class="form-control @error('dosen_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->departemen->kode ?? 'Dosen' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Tenggat Waktu (Deadline) <span class="text-danger">*</span></label>
                        <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline', now()->addDays(7)->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Status Penugasan <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ old('status') === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="font-weight-bold">Deskripsi & Instruksi Pengerjaan</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror"
                              placeholder="Rincian ketentuan tugas, kriteria penilaian, dan instruksi pengumpulan...">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.tugas.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                    <i class="bi bi-save mr-1"></i> Simpan Penugasan
                </button>
            </div>
        </form>
    </div>
@stop

