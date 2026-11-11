<?php

namespace Tests\Feature;

use App\Models\KategoriDaurUlang;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class KategoriDaurUlangTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();   // route admin kini di balik login

        // Jalankan seeder agar data awal terisi
        $this->seed(\Database\Seeders\KategoriDaurUlangSeeder::class);
    }

    /**
     * Test 1: Halaman admin kategori daur ulang dapat diakses dan menampilkan data sesuai seeder.
     */
    public function test_halaman_admin_kategori_dapat_diakses_dan_menampilkan_data(): void
    {
        $response = $this->get('/admin/kategori-daur-ulang');

        $response->assertStatus(200);
        $response->assertSee('Kategori daur ulang');
        $response->assertSee('Minyak jelantah');
        $response->assertSee('Botol PET bening');
        $response->assertSee('Botol PET berwarna');
        $response->assertSee('Gelas plastik');
        $response->assertSee('Saat ini 10 poin/kg');
    }

    /**
     * Test 2: Admin dapat menambah kategori baru beserta tarif awal.
     */
    public function test_admin_dapat_menambah_kategori_baru_beserta_tarif_awal(): void
    {
        $response = $this->post('/admin/kategori-daur-ulang', [
            'nama' => 'Kardus Bekas',
            'tipe' => 'Kertas & Karton',
            'satuan' => 'kg',
            'icon' => 'bottle',
            'poin_per_kg' => 15,
            'berlaku_sejak' => '2026-11-01',
            'deskripsi' => 'Kardus kemasan bersih kering',
        ]);

        $response->assertRedirect('/admin/kategori-daur-ulang');
        $this->assertDatabaseHas('kategori_daur_ulang', [
            'nama' => 'Kardus Bekas',
            'tipe' => 'Kertas & Karton',
            'is_active' => true,
        ]);

        $kategori = KategoriDaurUlang::where('nama', 'Kardus Bekas')->first();
        $this->assertDatabaseHas('riwayat_tarif_poin', [
            'kategori_id' => $kategori->id,
            'poin_per_kg' => 15,
        ]);
    }

    /**
     * Test 3: Admin dapat menonaktifkan dan mengaktifkan kembali kategori.
     */
    public function test_admin_dapat_toggle_status_kategori(): void
    {
        $kategori = KategoriDaurUlang::where('nama', 'Botol PET bening')->first();
        $this->assertTrue($kategori->is_active);

        // Nonaktifkan
        $response = $this->patch("/admin/kategori-daur-ulang/{$kategori->id}/toggle-status");
        $response->assertRedirect();
        $kategori->refresh();
        $this->assertFalse($kategori->is_active);

        // Aktifkan kembali
        $response2 = $this->patch("/admin/kategori-daur-ulang/{$kategori->id}/toggle-status");
        $response2->assertRedirect();
        $kategori->refresh();
        $this->assertTrue($kategori->is_active);
    }

    /**
     * Test 4: Kategori nonaktif tidak muncul pada scope active() (simulasi form warga).
     */
    public function test_kategori_nonaktif_tidak_muncul_pada_scope_active(): void
    {
        $activeCategories = KategoriDaurUlang::active()->pluck('nama')->toArray();

        $this->assertContains('Minyak jelantah', $activeCategories);
        $this->assertContains('Botol PET bening', $activeCategories);
        $this->assertNotContains('Gelas plastik', $activeCategories); // Gelas plastik berstatus nonaktif di seeder
    }

    /**
     * Test 5: Admin menjadwalkan tarif baru; tarif baru tersimpan dan riwayat tarif lama tetap terjaga utuh.
     */
    public function test_admin_menjadwalkan_tarif_baru_menjaga_riwayat_tarif_lama(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();
        $initialHistoryCount = $minyak->riwayatTarif()->count();

        $response = $this->post("/admin/kategori-daur-ulang/{$minyak->id}/jadwalkan-tarif", [
            'poin_per_kg' => 20,
            'berlaku_mulai' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'keterangan' => 'Penyesuaian tarif akhir tahun',
        ]);

        $response->assertRedirect();

        // Riwayat harus bertambah 1
        $this->assertEquals($initialHistoryCount + 1, $minyak->riwayatTarif()->count());

        // Tarif baru harus ada
        $this->assertDatabaseHas('riwayat_tarif_poin', [
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 20,
            'keterangan' => 'Penyesuaian tarif akhir tahun',
        ]);

        // Tarif lama (8 poin dan 10 poin) tetap ada di database
        $this->assertDatabaseHas('riwayat_tarif_poin', [
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 8,
        ]);
        $this->assertDatabaseHas('riwayat_tarif_poin', [
            'kategori_id' => $minyak->id,
            'poin_per_kg' => 10,
        ]);
    }

    /**
     * Test 6: Validasi input tarif tidak boleh bernilai negatif atau nol.
     */
    public function test_validasi_tarif_tidak_boleh_negatif_atau_nol(): void
    {
        $minyak = KategoriDaurUlang::where('nama', 'Minyak jelantah')->first();

        $response = $this->post("/admin/kategori-daur-ulang/{$minyak->id}/jadwalkan-tarif", [
            'poin_per_kg' => 0,
            'berlaku_mulai' => '2026-11-20',
        ]);

        $response->assertSessionHasErrors(['poin_per_kg']);
    }

    /**
     * Test 7: Admin dapat mengubah informasi nama, tipe, dan deskripsi kategori.
     */
    public function test_admin_dapat_mengubah_informasi_kategori(): void
    {
        $kategori = KategoriDaurUlang::where('nama', 'Botol PET bening')->first();

        $response = $this->put("/admin/kategori-daur-ulang/{$kategori->id}", [
            'nama' => 'Botol PET Bening Bersih',
            'tipe' => 'Botol plastik',
            'satuan' => 'kg',
            'icon' => 'bottle',
            'deskripsi' => 'Deskripsi botol bening terbarui',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('kategori_daur_ulang', [
            'id' => $kategori->id,
            'nama' => 'Botol PET Bening Bersih',
            'deskripsi' => 'Deskripsi botol bening terbarui',
        ]);
    }

    /**
     * Test 8: Validasi field nama dan satuan wajib diisi saat tambah kategori.
     */
    public function test_validasi_field_wajib_saat_tambah_kategori(): void
    {
        $response = $this->post('/admin/kategori-daur-ulang', [
            'nama' => '',
            'tipe' => '',
            'satuan' => '',
            'poin_per_kg' => -5,
        ]);

        $response->assertSessionHasErrors(['nama', 'tipe', 'satuan', 'poin_per_kg']);
    }

    /**
     * Test 9: Tamu dan non-admin tidak dapat mengakses halaman admin kategori daur ulang.
     */
    public function test_non_admin_dan_tamu_tidak_dapat_mengakses_halaman_kategori(): void
    {
        $this->actingAsWarga();
        $this->get(route('admin.kategori.index'))->assertRedirect(route('warga.dashboard'));

        Auth::logout();
        $this->get(route('admin.kategori.index'))->assertRedirect(route('login'));
    }
}
