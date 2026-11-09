@extends('adminlte::page')

@section('title', 'Edit Tugas Kuliah')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Perbarui Data Tugas Kuliah</h1>
            <p class="text-muted mb-0 small">Edit Penugasan: {{ $tugas->judul }}</p>
        </div>
        <a href="{{ route('admin.tugas.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-info shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-pencil-square mr-1"></i> Formulir Pembaruan Penugasan
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.tugas.update', $tugas->id) }}">
            @csrf
            @method('PUT')
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
                           value="{{ old('judul', $tugas->judul) }}" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Mata Kuliah <span class="text-danger">*</span></label>
                        <input type="text" name="mata_kuliah" class="form-control @error('mata_kuliah') is-invalid @enderror"
                               value="{{ old('mata_kuliah', $tugas->mata_kuliah) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Departemen Homebase <span class="text-danger">*</span></label>
                        <select name="departemen_id" class="form-control @error('departemen_id') is-invalid @enderror" required>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ old('departemen_id', $tugas->departemen_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Dosen Pengampu <span class="text-danger">*</span></label>
                        <select name="dosen_id" class="form-control @error('dosen_id') is-invalid @enderror" required>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_id', $tugas->dosen_id) == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->departemen->kode ?? 'Dosen' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Tenggat Waktu (Deadline) <span class="text-danger">*</span></label>
                        <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                               value="{{ old('deadline', $tugas->deadline->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Status Penugasan <span class="text-danger">*</span></label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                            <option value="Aktif" {{ old('status', $tugas->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="Selesai" {{ old('status', $tugas->status) === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="font-weight-bold">Deskripsi & Instruksi Pengerjaan</label>
                    <textarea name="deskripsi" rows="4" class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.tugas.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@stop

