<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Database\Seeders\WargaSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerifikasiWargaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

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
     * Test 3: Admin dapat menyetujui verifikasi warga dan memperbarui jumlah KK wilayah terkait.
     */
    public function test_admin_dapat_menyetujui_verifikasi_warga_dan_sinkronisasi_jumlah_kk_wilayah(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $siti = Warga::where('nik', '3204084509920002')->firstOrFail();
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();

        $this->assertEquals(0, $sukabirus->jumlah_kk);
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
        $this->assertEquals(1, $sukabirus->jumlah_kk);
    }

    /**
     * Test 4: Dua anggota keluarga dengan Nomor KK sama tetap dihitung 1 KK unik pada wilayah.
     */
    public function test_dua_anggota_keluarga_dengan_no_kk_sama_tetap_terhitung_satu_kk_unik_pada_wilayah(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();
        $budi = Warga::where('nik', '3204081205850001')->firstOrFail();

        $this->assertEquals(1, $sukapura->jumlah_kk);

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
            'no_kk' => $budi->no_kk, // Nomor KK sama
            'nama_lengkap' => 'Ratna Sari',
            'no_hp' => '081223344557',
            'alamat' => $budi->alamat,
            'wilayah_id' => $sukapura->id,
            'foto_identitas' => 'identitas_warga/sample_ktp_ratna.jpg',
            'status_verifikasi' => Warga::STATUS_MENUNGGU,
        ]);

        $this->actingAs($admin)->patch(route('admin.verifikasi-warga.approve', $wargaSatuKk->id));

        $sukapura->refresh();
        $this->assertEquals(1, $sukapura->jumlah_kk);

        // Sekarang setujui warga dari KK berbeda (Rian Pratama) di Sukapura -> jumlah_kk naik menjadi 2
        $rian = Warga::where('nik', '3204082111980003')->firstOrFail();
        $this->actingAs($admin)->patch(route('admin.verifikasi-warga.approve', $rian->id));

        $sukapura->refresh();
        $this->assertEquals(2, $sukapura->jumlah_kk);
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
}
