<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Database\Seeders\WargaSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class VerifikasiWargaTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();   // route admin kini di balik login

        $this->seed([
            WilayahSeeder::class,
            WargaSeeder::class,
        ]);
    }

    /**
     * Test 1: Halaman admin verifikasi warga menampilkan daftar antrean dan statistik status.
     */
    public function test_halaman_admin_verifikasi_warga_menampilkan_antrean_dan_statistik(): void
    {
        $response = $this->get(route('admin.verifikasi-warga.index'));

        $response->assertStatus(200);
        $response->assertSee('Verifikasi identitas warga');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Rian Pratama');
        $response->assertSee('3 pengajuan akun warga');
    }

    /**
     * Test 2: Filter tab status dan pencarian NIK/Nama pada panel admin berfungsi akurat.
     */
    public function test_filter_tab_status_dan_pencarian_warga_berfungsi_akurat(): void
    {
        $responseMenunggu = $this->get(route('admin.verifikasi-warga.index', ['status' => 'menunggu']));
        $responseMenunggu->assertStatus(200);
        $responseMenunggu->assertSee('Siti Aminah');
        $responseMenunggu->assertSee('1 pengajuan akun warga');

        $responseSearch = $this->get(route('admin.verifikasi-warga.index', ['q' => '3204082111980003']));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Rian Pratama');
        $responseSearch->assertSee('1 pengajuan akun warga');
    }

    /**
     * Test 3: Admin dapat menyetujui verifikasi warga tanpa menimpa jumlah KK wilayah yang dikelola Admin PBI 1.
     */
    public function test_admin_dapat_menyetujui_verifikasi_warga_tanpa_menimpa_jumlah_kk_wilayah(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $siti = Warga::where('nik', '3204084509920002')->firstOrFail();
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();
        $sukabirus->update(['jumlah_kk' => 266]);

        $this->assertTrue($siti->isPending());

        $response = $this->actingAs($admin)->patch(route('admin.verifikasi-warga.approve', $siti->id), [
            'catatan_verifikasi' => 'Dokumen KTP asli sesuai domisili RW 03 Sukabirus.',
        ]);

        $response->assertRedirect(route('admin.verifikasi-warga.index', ['selected' => $siti->id]));

        $siti->refresh();
        $sukabirus->refresh();

        $this->assertTrue($siti->isVerified());
        $this->assertEquals($admin->id, $siti->diverifikasi_oleh);
        $this->assertNotNull($siti->diverifikasi_pada);
        $this->assertEquals('Dokumen KTP asli sesuai domisili RW 03 Sukabirus.', $siti->catatan_verifikasi);
        $this->assertEquals(266, $sukabirus->jumlah_kk);
    }

    /**
     * Test 4: Menyetujui beberapa warga dalam satu atau beda KK menjaga jumlah KK wilayah tetap utuh.
     */
    public function test_menyetujui_beberapa_warga_tidak_mengubah_jumlah_kk_wilayah(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();
        $sukapura->update(['jumlah_kk' => 300]);
        $budi = Warga::where('nik', '3204081205850001')->firstOrFail();

        // Anggota keluarga satu KK dengan Budi Santoso
        $userIstri = User::create([
            'name' => 'Ratna Sari',
            'email' => 'ratna.sari@temji.id',
            'role' => 'warga',
            'password' => 'password123',
        ]);

        $wargaSatuKk = Warga::create([
            'user_id' => $userIstri->id,
            'nik' => '3204085006880007',
            'no_kk' => $budi->no_kk,
            'nama_lengkap' => 'Ratna Sari',
            'no_hp' => '081223344557',
            'alamat' => $budi->alamat,
            'wilayah_id' => $sukapura->id,
            'foto_identitas' => 'identitas_warga/sample_ktp_ratna.jpg',
            'status_verifikasi' => Warga::STATUS_MENUNGGU,
        ]);

        $this->actingAs($admin)->patch(route('admin.verifikasi-warga.approve', $wargaSatuKk->id));
        $this->assertTrue($wargaSatuKk->fresh()->isVerified());

        $rian = Warga::where('nik', '3204082111980003')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.verifikasi-warga.approve', $rian->id));
        $this->assertTrue($rian->fresh()->isVerified());

        $this->assertEquals(300, $sukapura->fresh()->jumlah_kk);
    }

    /**
     * Test 5: Admin dapat menolak pengajuan identitas warga dengan alasan penolakan.
     */
    public function test_admin_dapat_menolak_verifikasi_warga_beserta_alasan_penolakan(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $siti = Warga::where('nik', '3204084509920002')->firstOrFail();

        $response = $this->actingAs($admin)->patch(route('admin.verifikasi-warga.reject', $siti->id), [
            'catatan_verifikasi' => 'Alamat pada foto KTP berada di luar wilayah layanan Sukabirus.',
        ]);

        $response->assertRedirect(route('admin.verifikasi-warga.index', ['selected' => $siti->id]));

        $siti->refresh();
        $this->assertTrue($siti->isRejected());
        $this->assertEquals('Alamat pada foto KTP berada di luar wilayah layanan Sukabirus.', $siti->catatan_verifikasi);
        $this->assertEquals($admin->id, $siti->diverifikasi_oleh);
        $this->assertNotNull($siti->diverifikasi_pada);
    }

    /**
     * Test 6: Validasi mewajibkan alasan penolakan minimal 5 karakter saat menolak pengajuan warga.
     */
    public function test_validasi_wajib_mengisi_alasan_saat_admin_menolak_pengajuan(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $siti = Warga::where('nik', '3204084509920002')->firstOrFail();

        $response = $this->actingAs($admin)->patch(route('admin.verifikasi-warga.reject', $siti->id), [
            'catatan_verifikasi' => '',
        ]);

        $response->assertSessionHasErrors(['catatan_verifikasi']);
        $this->assertTrue($siti->fresh()->isPending());
    }

    /**
     * Test 7: Tamu dan non-admin tidak dapat mengakses panel verifikasi warga.
     */
    public function test_non_admin_dan_tamu_tidak_dapat_mengakses_verifikasi_warga(): void
    {
        $this->actingAsWarga();
        $this->get(route('admin.verifikasi-warga.index'))->assertRedirect(route('warga.dashboard'));

        Auth::logout();
        $this->get(route('admin.verifikasi-warga.index'))->assertRedirect(route('login'));
    }
}
