<?php

namespace Tests\Feature;

use App\Models\IuranWilayah;
use App\Models\TagihanIuran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IuranWilayahTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Halaman admin iuran dapat diakses dan menampilkan data wilayah.
     */
    public function test_halaman_admin_iuran_dapat_diakses_dan_menampilkan_data(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/iuran');

        $response->assertOk();
        $response->assertSee('Iuran Bulanan');
        $response->assertSee('Sukapura');
        $response->assertSee('Sukabirus');
    }

    /**
     * Test 2: Admin dapat memperbarui nominal iuran wilayah.
     */
    public function test_admin_dapat_mengubah_nominal_iuran(): void
    {
        $this->actingAsAdmin();

        $iuran = IuranWilayah::where('wilayah', 'Sukapura')->first();

        $response = $this->put("/admin/iuran/{$iuran->id}", [
            'nominal' => 30000,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('iuran_wilayahs', [
            'id'      => $iuran->id,
            'nominal' => 30000,
        ]);
    }

    /**
     * Test 3: Validasi nominal iuran tidak boleh bernilai negatif.
     */
    public function test_validasi_nominal_iuran_tidak_boleh_negatif(): void
    {
        $this->actingAsAdmin();

        $iuran = IuranWilayah::first();

        $response = $this->put("/admin/iuran/{$iuran->id}", [
            'nominal' => -5000,
        ]);

        $response->assertSessionHasErrors('nominal');
    }

    /**
     * Test 4: Admin dapat menerbitkan tagihan bulanan baru.
     */
    public function test_admin_dapat_menerbitkan_tagihan_bulanan(): void
    {
        $this->actingAsAdmin();

        $response = $this->post('/admin/iuran', [
            'bulan' => 'Desember 2026',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tagihan_iurans', [
            'wilayah'       => 'Sukapura',
            'bulan_tagihan' => 'Desember 2026',
            'status'        => 'Belum dibayar',
        ]);
    }

    /**
     * Test 5: Warga dapat mengakses halaman iuran miliknya.
     */
    public function test_warga_dapat_mengakses_halaman_iuran(): void
    {
        $this->actingAsWarga();

        // Buat tagihan belum bayar
        TagihanIuran::create([
            'nama_warga'     => 'Warga Uji',
            'wilayah'        => 'Sukapura',
            'bulan_tagihan'  => 'November 2026',
            'nominal_asal'   => 20000,
            'poin_digunakan' => 0,
            'potongan_poin'  => 0,
            'total_dibayar'  => 20000,
            'jatuh_tempo'    => '2026-11-10',
            'status'         => 'Belum dibayar',
        ]);

        $response = $this->get('/warga/iuran');

        $response->assertOk();
        $response->assertSee('November 2026');
        $response->assertSee('20.000');
    }

    /**
     * Test 6: Warga dapat membayar iuran secara tunai.
     */
    public function test_warga_dapat_membayar_iuran_tunai(): void
    {
        $this->actingAsWarga();

        $tagihan = TagihanIuran::create([
            'nama_warga'     => 'Warga Uji',
            'wilayah'        => 'Sukapura',
            'bulan_tagihan'  => 'November 2026',
            'nominal_asal'   => 20000,
            'poin_digunakan' => 0,
            'potongan_poin'  => 0,
            'total_dibayar'  => 20000,
            'jatuh_tempo'    => '2026-11-10',
            'status'         => 'Belum dibayar',
        ]);

        $response = $this->post("/warga/iuran/{$tagihan->id}/bayar", [
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertSame('Lunas', $tagihan->status);
        $this->assertSame('tunai', $tagihan->metode_pembayaran);
    }

    /**
     * Test 7: Warga dapat membayar iuran transfer dengan mengunggah bukti bayar.
     */
    public function test_warga_dapat_membayar_iuran_transfer_dengan_bukti(): void
    {
        Storage::fake('public');
        $this->actingAsWarga();

        $tagihan = TagihanIuran::create([
            'nama_warga'     => 'Warga Uji',
            'wilayah'        => 'Sukapura',
            'bulan_tagihan'  => 'November 2026',
            'nominal_asal'   => 20000,
            'poin_digunakan' => 0,
            'potongan_poin'  => 0,
            'total_dibayar'  => 20000,
            'jatuh_tempo'    => '2026-11-10',
            'status'         => 'Belum dibayar',
        ]);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 100, 'image/jpeg');

        $response = $this->post("/warga/iuran/{$tagihan->id}/bayar", [
            'metode_pembayaran' => 'transfer',
            'bukti_transfer'    => $file,
            'pakai_poin'        => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $tagihan->refresh();
        $this->assertSame('Lunas', $tagihan->status);
        $this->assertSame('transfer', $tagihan->metode_pembayaran);
        $this->assertNotNull($tagihan->bukti_transfer);
        $this->assertSame(100, $tagihan->poin_digunakan);
    }

    /**
     * Test 8: Pembayaran transfer tanpa bukti ditolak.
     */
    public function test_pembayaran_transfer_tanpa_bukti_ditolak(): void
    {
        $this->actingAsWarga();

        $tagihan = TagihanIuran::create([
            'nama_warga'     => 'Warga Uji',
            'wilayah'        => 'Sukapura',
            'bulan_tagihan'  => 'November 2026',
            'nominal_asal'   => 20000,
            'poin_digunakan' => 0,
            'potongan_poin'  => 0,
            'total_dibayar'  => 20000,
            'jatuh_tempo'    => '2026-11-10',
            'status'         => 'Belum dibayar',
        ]);

        $response = $this->post("/warga/iuran/{$tagihan->id}/bayar", [
            'metode_pembayaran' => 'transfer',
        ]);

        $response->assertSessionHasErrors('bukti_transfer');
    }

    /**
     * Test 9: Tamu atau non-admin tidak dapat mengakses admin iuran.
     */
    public function test_non_admin_tidak_dapat_akses_admin_iuran(): void
    {
        $response = $this->get('/admin/iuran');
        $response->assertRedirect('/login');

        $this->actingAsWarga();
        $response = $this->get('/admin/iuran');
        $response->assertRedirect('/warga/dashboard');
    }
}
