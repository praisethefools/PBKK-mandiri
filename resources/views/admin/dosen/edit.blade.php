@extends('adminlte::page')

@section('title', 'Edit Dosen')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Perbarui Data Dosen</h1>
            <p class="text-muted mb-0 small">Edit Biodata Staf Pengajar: {{ $dosen->nama }}</p>
        </div>
        <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-info shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-pencil-square mr-1"></i> Formulir Pembaruan Data Dosen
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.dosen.update', $dosen->id) }}">
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">NIP Dosen <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                               value="{{ old('nip', $dosen->nip) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $dosen->nama) }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Email Dosen <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $dosen->email) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Departemen Homebase <span class="text-danger">*</span></label>
                        <select name="departemen_id" class="form-control @error('departemen_id') is-invalid @enderror" required>
                            @foreach($departemens as $dept)
                                <option value="{{ $dept->id }}" {{ old('departemen_id', $dosen->departemen_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nama }} ({{ $dept->kode }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="font-weight-bold">Laboratorium / Bidang Keahlian <span class="text-danger">*</span></label>
                    <input type="text" name="keahlian" class="form-control @error('keahlian') is-invalid @enderror"
                           value="{{ old('keahlian', $dosen->keahlian) }}" required>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-save mr-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@stop

