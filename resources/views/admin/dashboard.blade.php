@extends('adminlte::page')

@section('title', 'Dashboard Backoffice')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="m-0 text-dark font-weight-bold">Dashboard Backoffice Akademik</h1>
            <p class="text-muted mb-0 small">Fakultas Teknologi Elektro dan Informatika Cerdas (FTEIC) ITS</p>
        </div>
        <div>
            <span class="badge badge-info px-3 py-2 font-weight-normal">
                <i class="bi bi-clock mr-1"></i> {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>
@stop

@section('content')
    {{-- Banner Notifikasi Status Peran (Spatie RBAC) --}}
    @php
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isStaf = $user->hasRole('staf');
    @endphp

    <div class="alert {{ $isAdmin ? 'alert-primary' : 'alert-success' }} alert-dismissible fade show shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <div class="mr-3 font-size-lg">
                <i class="bi {{ $isAdmin ? 'bi-shield-lock-fill' : 'bi-person-check-fill' }} fa-2x"></i>
            </div>
            <div>
                <h5 class="alert-heading mb-1 font-weight-bold">
                    Selamat datang, {{ $user->name }}!
                </h5>
                <p class="mb-0 small">
                    Anda masuk dengan peran
                    <strong class="text-uppercase badge {{ $isAdmin ? 'badge-dark' : 'badge-light' }} p-1">
                        {{ $user->getRoleNames()->join(', ') }}
                    </strong>.
                    @if($isAdmin)
                        <span class="text-white-50">Anda memiliki <strong>Akses Penuh</strong> (melihat statistik, menambah, mengedit, dan mengeksekusi <strong>Hak Eksklusif Hapus Data</strong>).</span>
                    @else
                        <span>Anda memiliki <strong>Akses Terbatas</strong> (diizinkan mengelola data, namun tombol & aksi <strong>Hapus Data dinonaktifkan</strong> sesuai aturan RBAC).</span>
                    @endif
                </p>
            </div>
        </div>
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 4. WIDGET DASHBOARD AGREGAT (Slide 24 & 40: Minimal 3 widget box statistik dinamis count()) --}}
    <div class="row">
        <!-- Widget 1: Total Mahasiswa -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info shadow-sm">
                <div class="inner">
                    <h3>{{ $totalMahasiswa }}</h3>
                    <p class="mb-1 font-weight-bold">Total Mahasiswa</p>
                    <small class="d-block text-white-50">
                        {{ $totalMahasiswaAktif }} Aktif &bull; {{ $totalMahasiswaCuti }} Cuti
                    </small>
                </div>
                <div class="icon">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <a href="{{ route('admin.mahasiswa.index') }}" class="small-box-footer">
                    Lihat Data Mahasiswa <i class="bi bi-arrow-right-circle ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Widget 2: Total Staf Dosen -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success shadow-sm">
                <div class="inner">
                    <h3>{{ $totalDosen }}</h3>
                    <p class="mb-1 font-weight-bold">Total Staf Dosen</p>
                    <small class="d-block text-white-50">Dosen Wali FTEIC ITS</small>
                </div>
                <div class="icon">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
                <a href="{{ route('admin.dosen.index') }}" class="small-box-footer">
                    Lihat Data Dosen <i class="bi bi-arrow-right-circle ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Widget 3: Total Tugas Aktif -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning shadow-sm">
                <div class="inner">
                    <h3>{{ $totalTugasAktif }}</h3>
                    <p class="mb-1 font-weight-bold">Total Tugas Aktif</p>
                    <small class="d-block text-dark-50">Tenggat Waktu Berjalan</small>
                </div>
                <div class="icon">
                    <i class="bi bi-journal-check"></i>
                </div>
                <a href="{{ route('admin.tugas.index') }}" class="small-box-footer">
                    Lihat Seluruh Tugas <i class="bi bi-arrow-right-circle ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Widget 4: Departemen & Rata-rata IPK -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger shadow-sm">
                <div class="inner">
                    <h3>{{ $rataRataIpk }}</h3>
                    <p class="mb-1 font-weight-bold">Rata-Rata IPK</p>
                    <small class="d-block text-white-50">{{ $totalDepartemen }} Departemen FTEIC</small>
                </div>
                <div class="icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <a href="{{ route('admin.mahasiswa.index') }}" class="small-box-footer">
                    Distribusi Prestasi <i class="bi bi-arrow-right-circle ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Section 2: Ringkasan Departemen & Tugas Terbaru -->
    <div class="row">
        <!-- Kolom Kiri: Distribusi Departemen -->
        <div class="col-md-6">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="bi bi-building mr-1"></i> Distribusi Departemen FTEIC ITS
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-primary">{{ $departemens->count() }} Jurusan</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Departemen</th>
                                    <th class="text-center">Mahasiswa</th>
                                    <th class="text-center">Dosen</th>
                                    <th class="text-center">Tugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departemens as $dept)
                                    <tr>
                                        <td><span class="badge badge-secondary">{{ $dept->kode }}</span></td>
                                        <td class="font-weight-bold">{{ $dept->nama }}</td>
                                        <td class="text-center">{{ $dept->mahasiswas_count }}</td>
                                        <td class="text-center">{{ $dept->dosens_count }}</td>
                                        <td class="text-center">{{ $dept->tugas_count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: 5 Tugas Kuliah Terbaru -->
        <div class="col-md-6">
            <div class="card card-outline card-warning shadow-sm">
                <div class="card-header border-0">
                    <h3 class="card-title font-weight-bold">
                        <i class="bi bi-calendar-event mr-1"></i> Tugas Kuliah Mendatang
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tugas.index') }}" class="btn btn-tool btn-sm">
                            <i class="bi bi-arrow-up-right"></i> Selengkapnya
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($tugasTerbaru as $t)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 font-weight-bold text-truncate" style="max-width: 320px;">
                                        {{ $t->judul }}
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-book mr-1"></i> {{ $t->mata_kuliah }} &bull;
                                        <i class="bi bi-person mr-1"></i> {{ $t->dosen->nama }}
                                    </small>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-{{ $t->status === 'Aktif' ? 'warning' : 'success' }}">
                                        {{ $t->status }}
                                    </span>
                                    <small class="d-block text-muted font-italic">
                                        Deadline: {{ $t->deadline->format('d M Y') }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="p-3 text-center text-muted">Belum ada tugas kuliah yang terdaftar.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Ringkasan Hak Akses RBAC (Spatie) -->
    <div class="row">
        <div class="col-12">
            <div class="card card-outline card-info shadow-sm">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="bi bi-shield-check mr-1"></i> Matriks Otorisasi RBAC (Role-Based Access Control) Aktif
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="font-weight-bold text-primary mb-2">
                                    <i class="bi bi-person-badge"></i> Akun Anda Saat Ini
                                </h5>
                                <ul class="list-unstyled mb-0 small">
                                    <li><strong>Nama:</strong> {{ $user->name }}</li>
                                    <li><strong>Email:</strong> {{ $user->email }}</li>
                                    <li><strong>Peran (Role):</strong> <span class="badge badge-primary text-uppercase">{{ $user->getRoleNames()->join(', ') }}</span></li>
                                    <li><strong>Total Izin (Permissions):</strong> {{ $user->getAllPermissions()->count() }} izin aktif</li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h5 class="font-weight-bold text-secondary mb-2">
                                    <i class="bi bi-key-fill"></i> Kebijakan Hak Eksklusif
                                </h5>
                                <p class="small text-muted mb-2">
                                    Tindakan destruktif seperti <code>Hapus Mahasiswa</code>, <code>Hapus Dosen</code>, dan <code>Hapus Tugas</code> hanya dilisensikan kepada pemegang peran <strong>Admin</strong>.
                                </p>
                                <div class="d-flex flex-wrap gap-1">
                                    <span class="badge badge-{{ $user->can('kelola-mahasiswa') ? 'success' : 'secondary' }} mr-1">
                                        <i class="bi bi-{{ $user->can('kelola-mahasiswa') ? 'check' : 'x' }}"></i> Kelola Mahasiswa
                                    </span>
                                    <span class="badge badge-{{ $user->can('hapus-mahasiswa') ? 'danger' : 'secondary' }} mr-1">
                                        <i class="bi bi-{{ $user->can('hapus-mahasiswa') ? 'check' : 'x' }}"></i> Hapus Mahasiswa (Admin)
                                    </span>
                                    <span class="badge badge-{{ $user->can('hapus-tugas') ? 'danger' : 'secondary' }} mr-1">
                                        <i class="bi bi-{{ $user->can('hapus-tugas') ? 'check' : 'x' }}"></i> Hapus Tugas (Admin)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

