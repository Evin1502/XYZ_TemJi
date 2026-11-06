<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthMultiRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            WilayahSeeder::class,
            UserSeeder::class,
        ]);
    }

    /**
     * Test 1: Halaman login dapat diakses oleh tamu (guest) dan memuat opsi peran TemJi.
     */
    public function test_halaman_login_dapat_diakses_oleh_tamu(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Akun');
        $response->assertSee('Otentikasi Multi-Role');
        $response->assertSee('admin@temji.id');
        $response->assertSee('petugas@temji.id');
        $response->assertSee('warga@temji.id');
    }

    /**
     * Test 2: Pengguna yang sudah login dialihkan dari halaman login ke dashboard perannya.
     */
    public function test_pengguna_yang_sudah_login_dialihkan_dari_halaman_login_ke_dashboard_perannya(): void
    {
        $admin = User::where('email', 'admin@temji.id')->firstOrFail();
        $responseAdmin = $this->actingAs($admin)->get(route('login'));
        $responseAdmin->assertRedirect(route('admin.dashboard'));

        $petugas = User::where('email', 'petugas@temji.id')->firstOrFail();
        $responsePetugas = $this->actingAs($petugas)->get(route('login'));
        $responsePetugas->assertRedirect(route('petugas.dashboard'));

        $warga = User::where('email', 'warga@temji.id')->firstOrFail();
        $responseWarga = $this->actingAs($warga)->get(route('login'));
        $responseWarga->assertRedirect(route('warga.dashboard'));
    }

    /**
     * Test 3: Pengguna Admin dapat login dengan kredensial valid dan dialihkan ke dashboard admin.
     */
    public function test_admin_dapat_login_dan_dialihkan_ke_dashboard_admin(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'admin@temji.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    /**
     * Test 4: Pengguna Petugas dapat login dengan kredensial valid dan dialihkan ke dashboard petugas.
     */
    public function test_petugas_dapat_login_dan_dialihkan_ke_dashboard_petugas(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'petugas@temji.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('petugas.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isPetugas());
    }

    /**
     * Test 5: Pengguna Warga dapat login dengan kredensial valid dan dialihkan ke dashboard warga.
     */
    public function test_warga_dapat_login_dan_dialihkan_ke_dashboard_warga(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'warga@temji.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('warga.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isWarga());
    }

    /**
     * Test 6: Login gagal dengan kata sandi yang salah menampilkan pesan error dan tetap guest.
     */
    public function test_login_gagal_dengan_kredensial_salah_menampilkan_error_validasi(): void
    {
        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => 'admin@temji.id',
            'password' => 'katasandisalah',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Test 7: Warga yang mencoba mengakses rute Admin dialihkan kembali ke dashboard warga dengan flash error.
     */
    public function test_warga_yang_mencoba_mengakses_rute_admin_dialihkan_kembali_ke_dashboard_warga(): void
    {
        $warga = User::where('email', 'warga@temji.id')->firstOrFail();

        $response = $this->actingAs($warga)->get(route('admin.dashboard'));

        $response->assertRedirect(route('warga.dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test 8: Petugas yang mencoba mengakses rute Admin dialihkan kembali ke dashboard petugas dengan flash error.
     */
    public function test_petugas_yang_mencoba_mengakses_rute_admin_dialihkan_kembali_ke_dashboard_petugas(): void
    {
        $petugas = User::where('email', 'petugas@temji.id')->firstOrFail();

        $response = $this->actingAs($petugas)->get(route('admin.dashboard'));

        $response->assertRedirect(route('petugas.dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test 9: Pengguna tamu (guest) yang mengakses rute terproteksi dialihkan ke halaman login.
     */
    public function test_tamu_yang_mengakses_rute_terproteksi_dialihkan_ke_halaman_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('petugas.dashboard'))->assertRedirect(route('login'));
        $this->get(route('warga.dashboard'))->assertRedirect(route('login'));
        $this->get(route('wilayah.index'))->assertRedirect(route('login'));
    }

    /**
     * Test 10: Pengguna yang logout berhasil mengakhiri sesi dan dialihkan ke halaman login.
     */
    public function test_logout_berhasil_mengakhiri_sesi_dan_mengalihkan_ke_halaman_login(): void
    {
        $admin = User::where('email', 'admin@temji.id')->firstOrFail();

        $response = $this->actingAs($admin)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Anda telah berhasil keluar dari akun TemJi.');
        $this->assertGuest();
    }
}
