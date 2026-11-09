@extends('adminlte::page')

@section('title', 'Tambah Dosen')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Tambah Data Dosen</h1>
            <p class="text-muted mb-0 small">Pendaftaran Staf Pengajar & Dosen Wali FTEIC ITS</p>
        </div>
        <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali ke Daftar
        </a>
    </div>
@stop

@section('content')
    <div class="card card-outline card-success shadow-sm">
        <div class="card-header">
            <h3 class="card-title font-weight-bold">
                <i class="bi bi-person-plus-fill mr-1"></i> Formulir Staf Dosen Baru
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.dosen.store') }}">
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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">NIP Dosen <span class="text-danger">*</span></label>
                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                               value="{{ old('nip') }}" placeholder="Contoh: 198001152005011001" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Nama Lengkap & Gelar <span class="text-danger">*</span></label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" placeholder="Contoh: Dr. Ir. Nama Dosen, M.Kom." required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold">Email Dosen <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="nama.dosen@its.ac.id" required>
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

                <div class="mb-3">
                    <label class="font-weight-bold">Laboratorium / Bidang Keahlian <span class="text-danger">*</span></label>
                    <input type="text" name="keahlian" class="form-control @error('keahlian') is-invalid @enderror"
                           value="{{ old('keahlian') }}" placeholder="Contoh: Rekayasa Perangkat Lunak, Kecerdasan Buatan" required>
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                <a href="{{ route('admin.dosen.index') }}" class="btn btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save mr-1"></i> Simpan Data Dosen
                </button>
            </div>
        </form>
    </div>
@stop

