@extends('adminlte::page')

@section('title', 'Tambah Mahasiswa')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Tambah Data Mahasiswa</h1>
            <p class="text-muted mb-0 small">Pendaftaran Mahasiswa Baru di Lingkungan FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-primary shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-person-plus-fill mr-1"></i> Formulir Mahasiswa
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.mahasiswa.store') }}">
            @csrf
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <h6 class="font-weight-bold mb-1"><i class="bi bi-exclamation-triangle-fill mr-1"></i> Terdapat kesalahan input:</h6>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">NRP Mahasiswa <span class="text-danger">*</span></label>
                        <input type="text" name="nrp" class="form-control @error('nrp') is-invalid @enderror"
                               value="{{ old('nrp') }}" placeholder="Contoh: 5025241241" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" placeholder="Nama lengkap mahasiswa" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Email Institusi (@student.its.ac.id) <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="nama@student.its.ac.id" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Departemen / Jurusan <span class="text-danger">*</span></label>
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
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Dosen Wali</label>
                        <select name="dosen_wali_id" class="form-control @error('dosen_wali_id') is-invalid @enderror">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($dosens as $d)
                                <option value="{{ $d->id }}" {{ old('dosen_wali_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nama }} ({{ $d->departemen->kode ?? 'Dosen' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Tahun Angkatan <span class="text-danger">*</span></label>
                        <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror"
                               value="{{ old('angkatan', 2024) }}" min="2018" max="2030" required>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="font-weight-bold">IPK Terakhir <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="ipk" class="form-control @error('ipk') is-invalid @enderror"
                               value="{{ old('ipk', '3.50') }}" min="0.00" max="4.00" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="font-weight-bold">Status Akademik <span class="text-danger">*</span></label>
                        <select name="is_active" class="form-control @error('is_active') is-invalid @enderror" required>
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Cuti Akademik</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.mahasiswa.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save mr-1"></i> Simpan Mahasiswa
                </button>
            </div>
        </form>
    </div>
@stop

