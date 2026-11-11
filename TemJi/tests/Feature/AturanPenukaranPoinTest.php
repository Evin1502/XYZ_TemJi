<?php

namespace Tests\Feature;

use App\Models\AturanPenukaranPoin;
use Database\Seeders\AturanPenukaranPoinSeeder;
use Database\Seeders\KategoriDaurUlangSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AturanPenukaranPoinTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();   // route admin kini di balik login

        $this->seed([
            WilayahSeeder::class,
            KategoriDaurUlangSeeder::class,
            AturanPenukaranPoinSeeder::class,
        ]);
    }

    /**
     * Test 1: Halaman admin aturan penukaran poin dapat diakses dan menampilkan data seeder persis mockup.
     */
    public function test_halaman_aturan_penukaran_poin_dapat_diakses_dan_menampilkan_data_seeder(): void
    {
        $response = $this->get(route('admin.aturan-penukaran.index'));

        $response->assertStatus(200);
        $response->assertSee('Aturan penukaran poin');
        $response->assertSee('Poin bisa ditukar jadi potongan iuran');
        $response->assertSee('Aturan aktif');
        $response->assertSee('1 Sep 2026 - sekarang');
        $response->assertSee('1 Jul - 31 Agu 2026');
        $response->assertSee('Tagihan Sukapura, saldo 180 poin');
        $response->assertSee('Rp 20.000');
        $response->assertSee('Rp 18.000');
        $response->assertSee('Rp 10.000');
        $response->assertSee('- Rp 10.000');
        $response->assertSee('Poin terpakai 100 · sisa saldo 80 poin');
        $response->assertSee('Saldo melebihi batas tidak hangus');
    }

    /**
     * Test 2: Tab navigasi antara master kategori dan aturan penukaran saling terhubung timbal balik.
     */
    public function test_tab_navigasi_kategori_dan_aturan_penukaran_saling_terhubung(): void
    {
        // Akses halaman kategori, pastikan ada tab link ke aturan penukaran
        $responseKategori = $this->get(route('admin.kategori.index'));
        $responseKategori->assertStatus(200);
        $responseKategori->assertSee(route('admin.aturan-penukaran.index'));

        // Akses halaman aturan penukaran, pastikan ada tab link kembali ke kategori
        $responseAturan = $this->get(route('admin.aturan-penukaran.index'));
        $responseAturan->assertStatus(200);
        $responseAturan->assertSee(route('admin.kategori.index'));
    }

    /**
     * Test 3: Admin dapat menyimpan aturan penukaran baru dengan pola SCD Type 2 (aturan lama ditutup status Selesai).
     */
    public function test_admin_dapat_menyimpan_aturan_penukaran_baru_dengan_pola_scd_type_2(): void
    {
        $initialCount = AturanPenukaranPoin::count();
        $oldActive = AturanPenukaranPoin::aktif()->first();
        $this->assertNotNull($oldActive);

        $response = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 150,
            'batas_maksimal_potongan_persen' => 60,
            'minimal_poin_tukar' => 40,
            'berlaku_mulai' => '2027-01-01',
            'label_periode' => 'Tagihan Januari 2027',
        ]);

        $response->assertRedirect(route('admin.aturan-penukaran.index'));
        $response->assertSessionHas('success');

        // Jumlah rekam riwayat bertambah 1
        $this->assertEquals($initialCount + 1, AturanPenukaranPoin::count());

        // Aturan lama otomatis ditutup dan berstatus Selesai
        $oldActive->refresh();
        $this->assertNotNull($oldActive->berlaku_sampai);
        $this->assertEquals('Selesai', $oldActive->status_label);

        // Aturan baru tersimpan aktif
        $newActive = AturanPenukaranPoin::aktif()->first();
        $this->assertNotNull($newActive);
        $this->assertEquals(150, $newActive->nilai_rupiah_per_poin);
        $this->assertEquals(60, $newActive->batas_maksimal_potongan_persen);
        $this->assertEquals(40, $newActive->minimal_poin_tukar);
        $this->assertEquals('Tagihan Januari 2027', $newActive->periode_label);
    }

    /**
     * Test 4: Validasi menolak batas maksimal potongan di luar rentang 0 sampai 100 persen.
     */
    public function test_validasi_menolak_batas_maksimal_potongan_di_luar_rentang_0_sampai_100_persen(): void
    {
        // Kasus A: Lebih dari 100%
        $responseLebih = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 100,
            'batas_maksimal_potongan_persen' => 120, // Tidak valid
            'minimal_poin_tukar' => 50,
            'berlaku_mulai' => '2026-12-01',
        ]);
        $responseLebih->assertSessionHasErrors(['batas_maksimal_potongan_persen']);

        // Kasus B: Kurang dari 0% (negatif)
        $responseNegatif = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 100,
            'batas_maksimal_potongan_persen' => -10, // Tidak valid
            'minimal_poin_tukar' => 50,
            'berlaku_mulai' => '2026-12-01',
        ]);
        $responseNegatif->assertSessionHasErrors(['batas_maksimal_potongan_persen']);
    }

    /**
     * Test 5: Validasi menolak nilai rupiah per 1 poin yang bernilai nol atau negatif.
     */
    public function test_validasi_menolak_nilai_rupiah_per_poin_yang_nol_atau_negatif(): void
    {
        $responseNol = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 0, // Tidak valid
            'batas_maksimal_potongan_persen' => 50,
            'minimal_poin_tukar' => 50,
            'berlaku_mulai' => '2026-12-01',
        ]);
        $responseNol->assertSessionHasErrors(['nilai_rupiah_per_poin']);

        $responseNegatif = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => -100, // Tidak valid
            'batas_maksimal_potongan_persen' => 50,
            'minimal_poin_tukar' => 50,
            'berlaku_mulai' => '2026-12-01',
        ]);
        $responseNegatif->assertSessionHasErrors(['nilai_rupiah_per_poin']);
    }

    /**
     * Test 6: Validasi menolak minimal poin untuk ditukarkan yang bernilai negatif.
     */
    public function test_validasi_menolak_minimal_poin_tukar_yang_negatif(): void
    {
        $response = $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 100,
            'batas_maksimal_potongan_persen' => 50,
            'minimal_poin_tukar' => -15, // Tidak valid
            'berlaku_mulai' => '2026-12-01',
        ]);
        $response->assertSessionHasErrors(['minimal_poin_tukar']);
    }

    /**
     * Test 7: Validasi seluruh field wajib diisi saat menyimpan aturan penukaran baru.
     */
    public function test_validasi_seluruh_field_wajib_diisi_saat_menyimpan_aturan(): void
    {
        $response = $this->post(route('admin.aturan-penukaran.store'), []);

        $response->assertSessionHasErrors([
            'nilai_rupiah_per_poin',
            'batas_maksimal_potongan_persen',
            'minimal_poin_tukar',
            'berlaku_mulai',
        ]);
    }

    /**
     * Test 8: Kalkulasi simulasi pada model menghitung potongan, sisa bayar, dan poin terpakai secara akurat.
     */
    public function test_kalkulasi_simulasi_pada_model_menghitung_potongan_dan_sisa_bayar_secara_akurat(): void
    {
        $aturan = AturanPenukaranPoin::where('batas_maksimal_potongan_persen', 50)
            ->where('nilai_rupiah_per_poin', 100)
            ->firstOrFail();

        // Tagihan Sukapura Rp 20.000, Saldo 180 poin
        $hasil = $aturan->hitungSimulasi(20000, 180);

        $this->assertEquals(20000, $hasil['nominal_tagihan']);
        $this->assertEquals(180, $hasil['saldo_poin']);
        $this->assertEquals(18000, $hasil['nilai_saldo_rupiah']);
        $this->assertEquals(10000, $hasil['batas_potongan_rupiah']); // 50% dari 20.000
        $this->assertEquals(10000, $hasil['potongan_dipakai']);
        $this->assertEquals(10000, $hasil['sisa_yang_dibayar']);
        $this->assertEquals(100, $hasil['poin_terpakai']); // 10.000 / 100 = 100 poin
        $this->assertEquals(80, $hasil['sisa_saldo_poin']); // 180 - 100 = 80 poin
        $this->assertEquals(50, $hasil['persen_potongan']);
        $this->assertEquals(50, $hasil['persen_dibayar']);
    }

    /**
     * Test 9: Simulasi warga dengan saldo poin di bawah minimal tukar tidak mendapatkan potongan iuran.
     */
    public function test_simulasi_saldo_poin_kurang_dari_minimal_tukar_tidak_mendapat_potongan(): void
    {
        $aturan = AturanPenukaranPoin::where('minimal_poin_tukar', 50)->firstOrFail();

        // Warga hanya memiliki 40 poin (kurang dari minimal 50 poin)
        $hasil = $aturan->hitungSimulasi(20000, 40);

        $this->assertFalse($hasil['cukup_minimal']);
        $this->assertEquals(0, $hasil['potongan_dipakai']);
        $this->assertEquals(0, $hasil['poin_terpakai']);
        $this->assertEquals(20000, $hasil['sisa_yang_dibayar']);
        $this->assertEquals(40, $hasil['sisa_saldo_poin']);
    }

    /**
     * Test 10: Simulasi warga dengan saldo poin melebihi batas maksimal potongan, sisa poin tidak hangus.
     */
    public function test_simulasi_saldo_poin_melebihi_batas_potongan_sisa_poin_tidak_hangus(): void
    {
        $aturan = AturanPenukaranPoin::where('batas_maksimal_potongan_persen', 50)
            ->where('nilai_rupiah_per_poin', 100)
            ->firstOrFail();

        // Warga memiliki 500 poin (senilai Rp 50.000), tagihan Rp 20.000 (batas potongan 50% = Rp 10.000)
        $hasil = $aturan->hitungSimulasi(20000, 500);

        $this->assertEquals(10000, $hasil['potongan_dipakai']);
        $this->assertEquals(100, $hasil['poin_terpakai']); // Hanya 100 poin yang terpakai
        $this->assertEquals(400, $hasil['sisa_saldo_poin']); // 400 poin tersisa dan tidak hangus
        $this->assertEquals(10000, $hasil['sisa_yang_dibayar']);
    }

    /**
     * Test 11: Tamu dan non-admin tidak dapat mengakses maupun menyimpan aturan penukaran poin.
     */
    public function test_non_admin_dan_tamu_tidak_dapat_mengakses_aturan_penukaran_poin(): void
    {
        $this->actingAsWarga();
        $this->get(route('admin.aturan-penukaran.index'))->assertRedirect(route('warga.dashboard'));
        $this->post(route('admin.aturan-penukaran.store'), [
            'nilai_rupiah_per_poin' => 200,
            'batas_maksimal_potongan_persen' => 50,
            'minimal_poin_tukar' => 10,
            'berlaku_mulai' => '2027-02-01',
        ])->assertRedirect(route('warga.dashboard'));

        Auth::logout();
        $this->get(route('admin.aturan-penukaran.index'))->assertRedirect(route('login'));
        $this->post(route('admin.aturan-penukaran.store'), [])->assertRedirect(route('login'));
    }
}
