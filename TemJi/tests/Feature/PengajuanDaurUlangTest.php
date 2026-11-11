<?php

namespace Tests\Feature;

use App\Models\KategoriDaurUlang;
use App\Models\PengajuanDaurUlang;
use App\Models\PengajuanDaurUlangItem;
use App\Models\RiwayatTarifPoin;
use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Carbon\Carbon;
use Database\Seeders\KategoriDaurUlangSeeder;
use Database\Seeders\PengajuanDaurUlangSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengajuanDaurUlangTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            WilayahSeeder::class,
            KategoriDaurUlangSeeder::class,
            PengajuanDaurUlangSeeder::class,
        ]);

        // Route kini di balik login warga: pakai akun warga dari PengajuanDaurUlangSeeder
        $this->actingAs(User::where('email', 'warga@temji.test')->firstOrFail());
    }

    /**
     * Test 1: Halaman pengajuan menampilkan judul, kategori aktif, dan tarif poin saat ini.
     */
    public function test_halaman_pengajuan_menampilkan_kategori_aktif_dan_tarif_poin_saat_ini(): void
    {
        $response = $this->get('/warga/daur-ulang');

        $response->assertOk();
        $response->assertSee('Ajukan daur ulang');
        $response->assertSee('Pastikan sudah dipisah per kategori');
        $response->assertSee('Apa yang mau disetor?');

        // Kategori aktif dan tarifnya
        $response->assertSee('Minyak jelantah');
        $response->assertSee('10 poin/kg');

        $response->assertSee('Botol PET bening');
        $response->assertSee('8 poin/kg');

        $response->assertSee('Botol PET berwarna');
        $response->assertSee('5 poin/kg');

        // Disclaimer timbangan
        $response->assertSee('Berat akhir ditimbang petugas. Poin dihitung dari berat timbang, bukan perkiraan.');
    }

    /**
     * Test 2: Kategori nonaktif tidak ditampilkan pada pilihan form warga.
     */
    public function test_kategori_nonaktif_tidak_ditampilkan_pada_halaman_pengajuan(): void
    {
        $response = $this->get('/warga/daur-ulang');

        $response->assertOk();
        $response->assertDontSee('Gelas plastik');
        $response->assertDontSee('4 poin/kg');
    }

    /**
     * Test 3: Warga dapat mengirim pengajuan daur ulang terpilah multi-kategori.
     */
    public function test_warga_dapat_mengirim_pengajuan_daur_ulang_terpilah_multi_kategori(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $petBening = KategoriDaurUlang::where('nama', 'Botol PET bening')->first();

        $payload = [
            'sudah_dipisah' => 1,
            'items' => [
                $minyak->id => ['selected' => 1, 'berat_kg' => 2],
                $petBening->id => ['selected' => 1, 'berat_kg' => 3],
            ],
        ];

        $response = $this->post('/warga/daur-ulang', $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Pengajuan terkirim');

        // Total estimasi kg = 2 + 3 = 5 kg
        // Total estimasi poin = (2 * 10) + (3 * 8) = 20 + 24 = 44 poin
        $this->assertDatabaseHas('pengajuan_daur_ulang', [
            'total_estimasi_kg' => 5,
            'total_estimasi_poin' => 44,
            'status' => PengajuanDaurUlang::STATUS_MENUNGGU_JADWAL,
        ]);

        $pengajuan = PengajuanDaurUlang::orderByDesc('id')->first();
        $this->assertCount(2, $pengajuan->items);
    }

    /**
     * Test 4: Kode pengajuan terbentuk secara otomatis dengan format DU-YYMM-XXXX dan unik.
     */
    public function test_kode_pengajuan_terbentuk_otomatis_sesuai_format_dan_unik(): void
    {
        $kode1 = PengajuanDaurUlang::generateKodePengajuan();
        $this->assertMatchesRegularExpression('/^DU-\d{4}-\d{4}$/', $kode1);

        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();

        // Buat pengajuan pertama
        $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [
                $minyak->id => ['selected' => 1, 'berat_kg' => 1],
            ],
        ]);
        $p1 = PengajuanDaurUlang::orderByDesc('id')->first();

        // Buat pengajuan kedua
        $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [
                $minyak->id => ['selected' => 1, 'berat_kg' => 2],
            ],
        ]);
        $p2 = PengajuanDaurUlang::orderByDesc('id')->first();

        $this->assertNotEquals($p1->kode_pengajuan, $p2->kode_pengajuan);
        $this->assertMatchesRegularExpression('/^DU-\d{4}-\d{4}$/', $p1->kode_pengajuan);
        $this->assertMatchesRegularExpression('/^DU-\d{4}-\d{4}$/', $p2->kode_pengajuan);
    }

    /**
     * Test 5: Bottom sheet "Pengajuan terkirim" tampil dengan kode pengajuan dan ringkasan item sesuai UI.
     */
    public function test_bottom_sheet_pengajuan_terkirim_tampil_dengan_kode_dan_ringkasan_item(): void
    {
        // Seeder sudah membuat contoh DU-2610-0214
        $response = $this->get('/warga/daur-ulang?kode=DU-2610-0214');

        $response->assertOk();
        $response->assertSee('Pengajuan terkirim');
        $response->assertSee('Tunjukkan kode ini ke petugas saat penjemputan.');
        $response->assertSee('Kode pengajuan');
        $response->assertSee('DU-2610-0214');
        $response->assertSee('Minyak jelantah ± 2 kg · Botol PET bening ± 3 kg');
        $response->assertSee('Salin kode');
        $response->assertSee('Pilih jadwal');
    }

    /**
     * Test 6: Pengajuan ditolak jika tidak ada kategori yang dipilih.
     */
    public function test_pengajuan_ditolak_jika_tidak_ada_kategori_yang_dipilih(): void
    {
        $response = $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [],
        ]);

        $response->assertSessionHasErrors(['selected_items']);
    }

    /**
     * Test 7: Pengajuan ditolak jika memilih kategori daur ulang nonaktif.
     */
    public function test_pengajuan_ditolak_jika_memilih_kategori_nonaktif(): void
    {
        $gelasNonaktif = KategoriDaurUlang::where('nama', 'Gelas plastik')->first();

        $response = $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [
                $gelasNonaktif->id => ['selected' => 1, 'berat_kg' => 2],
            ],
        ]);

        $response->assertSessionHasErrors(['selected_items']);
    }

    /**
     * Test 8: Pengajuan ditolak jika perkiraan berat nol atau negatif.
     */
    public function test_pengajuan_ditolak_jika_perkiraan_berat_nol_atau_negatif(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();

        $response = $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [
                $minyak->id => ['selected' => 1, 'berat_kg' => 0],
            ],
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test 9: Snapshot tarif poin mencatat nilai yang sedang berlaku saat transaksi dibuat.
     */
    public function test_poin_snapshot_menggunakan_tarif_aktif_saat_pengajuan_dibuat(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();

        $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [
                $minyak->id => ['selected' => 1, 'berat_kg' => 5],
            ],
        ]);

        $pengajuan = PengajuanDaurUlang::orderByDesc('id')->first();
        $item = $pengajuan->items->first();

        // Minyak jelantah memiliki tarif aktif 10 poin/kg
        $this->assertEquals(10, $item->poin_per_kg_snapshot);
        $this->assertEquals(50, $item->estimasi_poin);
    }

    /**
     * Test 10: Tombol "Pilih jadwal" mengarahkan ke halaman jadwal untuk kode pengajuan terkait.
     */
    public function test_tombol_pilih_jadwal_membuka_halaman_jadwal_untuk_kode_pengajuan(): void
    {
        $response = $this->get('/warga/daur-ulang/DU-2610-0214/jadwal');

        $response->assertOk();
        $response->assertSee('Pilih jadwal penjemputan');
        $response->assertSee('DU-2610-0214');
        $response->assertSee('Minyak jelantah ± 2 kg · Botol PET bening ± 3 kg');
        $response->assertSee('Konfirmasi Jadwal Penjemputan');
    }

    /**
     * Test 11 (PBI 4): pengajuan tanpa pernyataan "sudah dipisah" ditolak.
     */
    public function test_pengajuan_tanpa_pernyataan_sudah_dipisah_ditolak(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $sebelum = PengajuanDaurUlang::count();

        $response = $this->post('/warga/daur-ulang', [
            'items' => [$minyak->id => ['selected' => 1, 'berat_kg' => 2]],
        ]);

        $response->assertSessionHasErrors(['sudah_dipisah']);
        $this->assertSame($sebelum, PengajuanDaurUlang::count());
    }

    /**
     * Test 12 (PBI 1/4): wilayah pengajuan mengikuti wilayah warga, bukan default Sukapura.
     */
    public function test_wilayah_pengajuan_mengikuti_wilayah_warga_sukabirus(): void
    {
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();
        $user = User::create([
            'name' => 'Warga Sukabirus', 'email' => 'warga.sukabirus@temji.test',
            'role' => User::ROLE_WARGA, 'password' => 'password123',
        ]);
        Warga::create([
            'user_id' => $user->id, 'nik' => '3273000000000021', 'no_kk' => '3273000000000821',
            'nama_lengkap' => 'Warga Sukabirus', 'no_hp' => '081200000021',
            'alamat' => 'Jl. Contoh No. 21', 'wilayah_id' => $sukabirus->id,
            'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
        ]);
        $this->actingAs($user);

        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [$minyak->id => ['selected' => 1, 'berat_kg' => 2]],
        ])->assertSessionHasNoErrors();

        $pengajuan = PengajuanDaurUlang::where('user_id', $user->id)->firstOrFail();
        $this->assertSame($sukabirus->id, $pengajuan->wilayah_id);
    }

    /**
     * Test 13 (PBI 1): warga yang wilayahnya belum lengkap tidak bisa mengajukan, dan tidak jatuh ke wilayah lain.
     */
    public function test_warga_tanpa_wilayah_tidak_bisa_mengajukan(): void
    {
        $user = User::create([
            'name' => 'Warga Lama', 'email' => 'warga.lama@temji.test',
            'role' => User::ROLE_WARGA, 'password' => 'password123',
        ]);
        Warga::create([
            'user_id' => $user->id, 'nik' => '3273000000000022', 'no_kk' => '3273000000000822',
            'nama_lengkap' => 'Warga Lama', 'no_hp' => '081200000022',
            'alamat' => 'Alamat belum lengkap', 'wilayah_id' => null,
            'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
        ]);
        $this->actingAs($user);

        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $response = $this->post('/warga/daur-ulang', [
            'sudah_dipisah' => 1,
            'items' => [$minyak->id => ['selected' => 1, 'berat_kg' => 2]],
        ]);

        $response->assertSessionHasErrors(['wilayah']);
        $this->assertDatabaseMissing('pengajuan_daur_ulang', ['user_id' => $user->id]);
    }

    /**
     * Test 14: warga tidak dapat membuka pengajuan milik warga lain lewat kode.
     */
    public function test_warga_tidak_bisa_membuka_pengajuan_milik_warga_lain(): void
    {
        $lain = User::create([
            'name' => 'Warga Lain', 'email' => 'warga.lain@temji.test',
            'role' => User::ROLE_WARGA, 'password' => 'password123',
        ]);
        $this->actingAs($lain);

        // DU-2610-0214 milik Ratna (user di setUp)
        $this->get('/warga/daur-ulang/DU-2610-0214/jadwal')->assertNotFound();

        $this->get('/warga/daur-ulang?kode=DU-2610-0214')
            ->assertOk()
            ->assertDontSee('DU-2610-0214');
    }
}
