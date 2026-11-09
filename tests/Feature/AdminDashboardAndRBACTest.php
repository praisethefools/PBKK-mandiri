<?php

namespace Tests\Feature;

use App\Models\Departemen;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\AcademicDataSeeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardAndRBACTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected User $stafUser;

    protected User $mahasiswaUser;

    protected Departemen $deptIF;

    protected Departemen $deptSI;

    protected Dosen $dosen;

    protected Mahasiswa $mhs1;

    protected Mahasiswa $mhs2;

    protected Tugas $tugas;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Eksekusi Seeder Role & Permission Spatie
        $this->seed(RoleAndPermissionSeeder::class);

        // 2. Siapkan User dengan Peran masing-masing
        $this->adminUser = User::factory()->create([
            'name' => 'Administrator Utama',
            'email' => 'admin@akademik.its.ac.id',
        ]);
        $this->adminUser->assignRole('admin');

        $this->stafUser = User::factory()->create([
            'name' => 'Staf Tata Usaha',
            'email' => 'staf@akademik.its.ac.id',
        ]);
        $this->stafUser->assignRole('staf');

        $this->mahasiswaUser = User::factory()->create([
            'name' => 'Mahasiswa Biasa',
            'email' => 'mahasiswa@student.its.ac.id',
        ]);
        $this->mahasiswaUser->assignRole('mahasiswa');

        // 3. Siapkan Master Data Akademik
        $this->deptIF = Departemen::create([
            'kode' => 'IF',
            'nama' => 'Teknik Informatika',
            'fakultas' => 'FTEIC',
        ]);

        $this->deptSI = Departemen::create([
            'kode' => 'SI',
            'nama' => 'Sistem Informasi',
            'fakultas' => 'FTEIC',
        ]);

        $this->dosen = Dosen::create([
            'departemen_id' => $this->deptIF->id,
            'nip' => '198001152005011001',
            'nama' => 'Dr. Bambang Setiawan, M.Kom.',
            'email' => 'bambang@if.its.ac.id',
            'keahlian' => 'Kecerdasan Buatan',
        ]);

        $this->mhs1 = Mahasiswa::create([
            'departemen_id' => $this->deptIF->id,
            'dosen_wali_id' => $this->dosen->id,
            'nrp' => '5025241241',
            'nama' => 'Abdurrahman Arrafi',
            'email' => '5025241241@student.its.ac.id',
            'angkatan' => 2024,
            'ipk' => 3.92,
            'is_active' => true,
        ]);

        $this->mhs2 = Mahasiswa::create([
            'departemen_id' => $this->deptSI->id,
            'dosen_wali_id' => null,
            'nrp' => '5026211002',
            'nama' => 'Budi Santoso',
            'email' => 'budi.santoso@student.its.ac.id',
            'angkatan' => 2021,
            'ipk' => 3.20,
            'is_active' => false,
        ]);

        $this->tugas = Tugas::create([
            'departemen_id' => $this->deptIF->id,
            'dosen_id' => $this->dosen->id,
            'judul' => 'Tugas 4: Integrasi Dashboard & RBAC',
            'mata_kuliah' => 'Pemrograman Berbasis Kerangka Kerja',
            'deskripsi' => 'Pengembangan backoffice AdminLTE dengan Spatie Permission',
            'deadline' => now()->addDays(7)->format('Y-m-d'),
            'status' => 'Aktif',
        ]);
    }

    // ==========================================
    // SPESIFIKASI 1 & 2: Backoffice & Auth Guard
    // ==========================================

    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_regular_user_without_backoffice_role_is_forbidden(): void
    {
        $response = $this->actingAs($this->mahasiswaUser)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_admin_and_staf_can_access_admin_dashboard(): void
    {
        $adminResponse = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Dashboard Backoffice');
        $adminResponse->assertSee('Administrator Utama');

        $stafResponse = $this->actingAs($this->stafUser)->get('/admin/dashboard');
        $stafResponse->assertStatus(200);
        $stafResponse->assertSee('Dashboard Backoffice');
        $stafResponse->assertSee('Staf Tata Usaha');
    }

    // ==========================================
    // SPESIFIKASI 3: Otorisasi DB Spatie RBAC
    // ==========================================

    public function test_spatie_roles_and_permissions_are_properly_configured(): void
    {
        $this->assertTrue($this->adminUser->hasRole('admin'));
        $this->assertTrue($this->adminUser->can('kelola-mahasiswa'));
        $this->assertTrue($this->adminUser->can('hapus-mahasiswa'));
        $this->assertTrue($this->adminUser->can('hapus-tugas'));

        $this->assertTrue($this->stafUser->hasRole('staf'));
        $this->assertTrue($this->stafUser->can('kelola-mahasiswa'));
        $this->assertTrue($this->stafUser->can('kelola-tugas'));
        $this->assertFalse($this->stafUser->can('hapus-mahasiswa'));
        $this->assertFalse($this->stafUser->can('hapus-dosen'));
        $this->assertFalse($this->stafUser->can('hapus-tugas'));
    }

    // ==========================================
    // SPESIFIKASI 4: Widget Dashboard Agregat (count())
    // ==========================================

    public function test_dashboard_renders_aggregate_count_widgets(): void
    {
        $response = $this->actingAs($this->adminUser)->get('/admin/dashboard');

        $response->assertStatus(200);
        // Widget 1: Total Mahasiswa (2 data)
        $response->assertSee('Total Mahasiswa');
        // Widget 2: Total Staf Dosen (1 data)
        $response->assertSee('Total Staf Dosen');
        // Widget 3: Total Tugas Aktif (1 data)
        $response->assertSee('Total Tugas Aktif');
    }

    // ==========================================
    // SPESIFIKASI 5 & HAK EKSKLUSIF: Pengaman Controller & Blade Directive
    // ==========================================

    public function test_admin_can_see_delete_button_in_blade_and_delete_mahasiswa(): void
    {
        // 1. Blade check: Admin melihat tombol hapus
        $viewResponse = $this->actingAs($this->adminUser)->get('/admin/mahasiswa');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Hapus Data (Hak Eksklusif Admin)');

        // 2. Controller check: Admin dapat menghapus data mahasiswa
        $deleteResponse = $this->actingAs($this->adminUser)
            ->delete("/admin/mahasiswa/{$this->mhs2->id}");

        $deleteResponse->assertRedirect(route('admin.mahasiswa.index'));
        $this->assertDatabaseMissing('mahasiswas', ['id' => $this->mhs2->id]);
    }

    public function test_staf_cannot_see_delete_button_in_blade(): void
    {
        $response = $this->actingAs($this->stafUser)->get('/admin/mahasiswa');

        $response->assertStatus(200);
        $response->assertDontSee('Hapus Data (Hak Eksklusif Admin)');
        $response->assertSee('Peran Staf: Hak Hapus Dikunci');
    }

    public function test_staf_cannot_delete_mahasiswa_via_server_request(): void
    {
        // Simulasi request manipulasi endpoint (Slide 25) oleh Staf
        $response = $this->actingAs($this->stafUser)
            ->delete("/admin/mahasiswa/{$this->mhs1->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('mahasiswas', ['id' => $this->mhs1->id]);
    }

    public function test_staf_cannot_delete_dosen_or_tugas(): void
    {
        $dosenResponse = $this->actingAs($this->stafUser)
            ->delete("/admin/dosen/{$this->dosen->id}");
        $dosenResponse->assertStatus(403);
        $this->assertDatabaseHas('dosens', ['id' => $this->dosen->id]);

        $tugasResponse = $this->actingAs($this->stafUser)
            ->delete("/admin/tugas/{$this->tugas->id}");
        $tugasResponse->assertStatus(403);
        $this->assertDatabaseHas('tugas', ['id' => $this->tugas->id]);
    }

    // ==========================================
    // TANTANGAN 1 & 2: Kueri when() & withQueryString()
    // ==========================================

    public function test_mahasiswa_search_filter_with_conditional_when_query(): void
    {
        // 1. Pencarian berdasarkan NRP
        $responseNrp = $this->actingAs($this->adminUser)
            ->get('/admin/mahasiswa?search=5025241241');
        $responseNrp->assertStatus(200);
        $responseNrp->assertSee('Abdurrahman Arrafi');
        $responseNrp->assertDontSee('Budi Santoso');

        // 2. Pencarian berdasarkan Nama
        $responseNama = $this->actingAs($this->adminUser)
            ->get('/admin/mahasiswa?search=Budi');
        $responseNama->assertStatus(200);
        $responseNama->assertSee('Budi Santoso');
        $responseNama->assertDontSee('Abdurrahman Arrafi');

        // 3. Filter Departemen
        $responseDept = $this->actingAs($this->adminUser)
            ->get("/admin/mahasiswa?departemen_id={$this->deptSI->id}");
        $responseDept->assertStatus(200);
        $responseDept->assertSee('Budi Santoso');
        $responseDept->assertDontSee('Abdurrahman Arrafi');

        // 4. Filter Status Aktif
        $responseStatus = $this->actingAs($this->adminUser)
            ->get('/admin/mahasiswa?status=1');
        $responseStatus->assertStatus(200);
        $responseStatus->assertSee('Abdurrahman Arrafi');
        $responseStatus->assertDontSee('Budi Santoso');
    }

    public function test_pagination_preserves_query_string_with_withquerystring(): void
    {
        // Tambahkan data dummy agar mencapai paginasi (> 10 data)
        for ($i = 0; $i < 15; $i++) {
            Mahasiswa::create([
                'departemen_id' => $this->deptIF->id,
                'nrp' => '502524'.(2000 + $i),
                'nama' => "Mahasiswa Test {$i}",
                'email' => "mhs{$i}@test.com",
                'angkatan' => 2024,
                'ipk' => 3.50,
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($this->adminUser)
            ->get('/admin/mahasiswa?search=Mahasiswa&page=2');

        $response->assertStatus(200);
        // Link paginasi wajib membawa parameter ?search=Mahasiswa
        $response->assertSee('search=Mahasiswa');
    }

    // ==========================================
    // TANTANGAN 4: Auto-Login Test Accounts
    // ==========================================

    public function test_quick_login_routes_work_for_demo_evaluation(): void
    {
        // 1. Quick Login Admin
        $adminQuick = $this->get(route('quick.login', 'admin'));
        $adminQuick->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);

        // Logout
        $this->post('/logout');

        // 2. Quick Login Staf
        $stafQuick = $this->get(route('quick.login', 'staf'));
        $stafQuick->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->stafUser);

        // Logout
        $this->post('/logout');

        // 3. Quick Login Mahasiswa
        $mhsQuick = $this->get(route('quick.login', 'mahasiswa'));
        $mhsQuick->assertRedirect(route('beranda'));
        $this->assertAuthenticatedAs($this->mahasiswaUser);
    }

    // ==========================================
    // TANTANGAN 3: Auto-Seeder Faker Realistis
    // ==========================================

    public function test_academic_data_seeder_populates_realistic_faker_data(): void
    {
        $this->seed(AcademicDataSeeder::class);

        $this->assertGreaterThanOrEqual(5, Departemen::count());
        $this->assertGreaterThanOrEqual(15, Dosen::count());
        $this->assertGreaterThanOrEqual(50, Mahasiswa::count());
        $this->assertGreaterThanOrEqual(20, Tugas::count());
    }
}
