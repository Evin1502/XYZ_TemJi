<?php

namespace Tests\Feature;

use App\Models\AturanPenukaranPoin;
use App\Models\KategoriDaurUlang;
use App\Models\RiwayatPoin;
use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Database\Seeders\AturanPenukaranPoinSeeder;
use Database\Seeders\KategoriDaurUlangSeeder;
use Database\Seeders\RiwayatPoinWargaSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\AuthAsRole;
use Tests\TestCase;

class PanduanDanRiwayatPoinTest extends TestCase
{
    use RefreshDatabase;
    use AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            WilayahSeeder::class,
            KategoriDaurUlangSeeder::class,
            AturanPenukaranPoinSeeder::class,
            RiwayatPoinWargaSeeder::class,
        ]);
    }

    /**
     * Test 1: Halaman poin warga dapat diakses oleh warga terotentikasi dan menampilkan data header akun.
     */
    public function test_halaman_poin_warga_dapat_diakses_oleh_warga_terotentikasi(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Poin saya');
        $response->assertSee('Dewi Lestari');
        $response->assertSee('Sukapura');
    }

    /**
     * Test 2: Halaman menampilkan saldo poin dan kalkulasi setara rupiah secara akurat persis mockup.
     */
    public function test_halaman_menampilkan_saldo_poin_dan_setara_rupiah_secara_akurat(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Saldo poin');
        $response->assertSee('180');
        $response->assertSee('Setara potongan iuran');
        $response->assertSee('Rp 18.000');
    }

    /**
     * Test 3: Panduan poin hanya menampilkan kategori daur ulang aktif, sedangkan non-aktif disembunyikan.
     */
    public function test_panduan_poin_hanya_menampilkan_kategori_daur_ulang_yang_aktif(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Panduan poin');
        $response->assertSee('per kg ditimbang');

        // Kategori aktif harus muncul
        $response->assertSee('Minyak jelantah');
        $response->assertSee('10');
        $response->assertSee('Botol PET bening');
        $response->assertSee('8');
        $response->assertSee('Botol PET berwarna');
        $response->assertSee('5');

        // Kategori non-aktif (Gelas plastik) TIDAK boleh muncul di panduan
        $response->assertDontSee('Gelas plastik');
    }

    /**
     * Test 4: Kotak informasi poin hanya dari daur ulang tampil jelas di antarmuka.
     */
    public function test_kotak_informasi_poin_hanya_dari_daur_ulang_tampil_jelas(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Poin hanya dari daur ulang.');
        $response->assertSee('Sampah biasa dan bayar iuran tidak menambah poin.');
    }

    /**
     * Test 5: Riwayat poin ringkas menampilkan transaksi kredit dan debit dengan format badge mutasi.
     */
    public function test_riwayat_poin_menampilkan_transaksi_masuk_dan_keluar_dengan_format_badge(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Riwayat poin');
        $response->assertSee('Minyak jelantah 2,4 kg');
        $response->assertSee('DU-2610-0187 · 22 Okt');
        $response->assertSee('+24');
        $response->assertSee('Potongan iuran Oktober');
        $response->assertSee('Ditukar · 5 Okt');
        $response->assertSee('-100');
    }

    /**
     * Test 6: Modal lihat semua riwayat memuat seluruh rekam jejak transaksi dan kode pengajuan.
     */
    public function test_modal_lihat_semua_riwayat_berisi_seluruh_transaksi(): void
    {
        $dewiUser = User::where('email', 'dewi.lestari@temji.id')->first();
        $this->actingAs($dewiUser);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Lihat semua');
        $response->assertSee('Riwayat Mutasi Poin');
        $response->assertSee('Botol PET bening 32 kg');
        $response->assertSee('DU-2609-0094 · 28 Sep');
        $response->assertSee('+256');
    }

    /**
     * Test 7: Warga baru tanpa transaksi poin menampilkan kondisi saldo 0 dan pesan kosong ramah.
     */
    public function test_warga_tanpa_riwayat_poin_menampilkan_pesan_kosong(): void
    {
        $userBaru = User::create([
            'name' => 'Warga Baru Sukapura',
            'email' => 'warga.baru@temji.test',
            'role' => User::ROLE_WARGA,
            'password' => 'password123',
        ]);

        $wargaBaru = Warga::create([
            'user_id' => $userBaru->id,
            'nik' => '3204085501989999',
            'no_kk' => '3204085501189999',
            'nama_lengkap' => 'Warga Baru Sukapura',
            'no_hp' => '081299998888',
            'alamat' => 'Jl. Baru No. 1',
            'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
            'saldo_poin' => 0,
        ]);

        $this->actingAs($userBaru);

        $response = $this->get(route('warga.poin.index'));

        $response->assertStatus(200);
        $response->assertSee('Warga Baru Sukapura');
        $response->assertSee('Rp 0');
        $response->assertSee('Belum ada riwayat transaksi poin.');
    }

    /**
     * Test 8: Tamu (unauthenticated) yang membuka halaman warga poin dialihkan ke halaman login.
     */
    public function test_tamu_yang_mengakses_halaman_warga_poin_dialihkan_ke_login(): void
    {
        $response = $this->get(route('warga.poin.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test 9: Pengguna dengan role petugas atau admin yang mengakses /warga/poin dialihkan.
     */
    public function test_pengguna_petugas_atau_admin_yang_mengakses_warga_poin_dialihkan(): void
    {
        // Admin dialihkan
        $admin = $this->actingAsAdmin();
        $responseAdmin = $this->get(route('warga.poin.index'));
        $responseAdmin->assertRedirect(route('admin.dashboard'));

        // Petugas dialihkan
        $petugas = User::create([
            'name' => 'Petugas Uji',
            'email' => 'petugas.uji@temji.test',
            'role' => User::ROLE_PETUGAS,
            'password' => 'password123',
        ]);
        $this->actingAs($petugas);
        $responsePetugas = $this->get(route('warga.poin.index'));
        $responsePetugas->assertRedirect(route('petugas.dashboard'));
    }

    /**
     * Test 10: Helper atomic tambahPoin() dan kurangiPoin() pada model Warga menjaga integritas saldo dan ledger.
     */
    public function test_helper_tambah_poin_dan_kurangi_poin_menjaga_integritas_ledger(): void
    {
        $warga = Warga::where('nama_lengkap', 'Dewi Lestari')->first();
        $saldoAwal = $warga->saldo_poin; // 180

        // 1. Tambah poin (Setoran daur ulang baru)
        $riwayatTambah = $warga->tambahPoin(
            poin: 50,
            judul: 'Minyak jelantah 5 kg',
            keterangan: 'DU-2611-0001 · 13 Nov',
            kodePengajuan: 'DU-2611-0001',
            kategoriId: null,
            beratKg: 5.0
        );

        $this->assertEquals($saldoAwal + 50, $warga->saldo_poin);
        $this->assertEquals('masuk', $riwayatTambah->jenis);
        $this->assertEquals(50, $riwayatTambah->jumlah_poin);
        $this->assertEquals($saldoAwal + 50, $riwayatTambah->saldo_akhir);
        $this->assertEquals('+50', $riwayatTambah->formatted_poin);

        // 2. Kurangi poin (Penukaran potongan iuran)
        $riwayatKurang = $warga->kurangiPoin(
            poin: 100,
            judul: 'Potongan iuran November',
            keterangan: 'Ditukar · 13 Nov'
        );

        $this->assertEquals($saldoAwal + 50 - 100, $warga->saldo_poin);
        $this->assertEquals('keluar', $riwayatKurang->jenis);
        $this->assertEquals(100, $riwayatKurang->jumlah_poin);
        $this->assertEquals($saldoAwal + 50 - 100, $riwayatKurang->saldo_akhir);
        $this->assertEquals('-100', $riwayatKurang->formatted_poin);
    }
}
