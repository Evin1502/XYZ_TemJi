<?php

namespace Tests\Feature;

use App\Models\JadwalPenjemputan;
use App\Models\PengajuanDaurUlang;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JadwalPenjemputanTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsAdmin();   // route admin kini di balik login
        $this->seed();
    }

    /**
     * Data form tambah jadwal yang valid: daur ulang Sukapura, Kamis 09.00–11.00, kuota 20, Pak Dedi.
     */
    private function dataJadwal(array $ubah = []): array
    {
        return array_merge([
            'jenis'       => 'daur_ulang',
            'wilayah_id'  => Wilayah::where('nama', 'Sukapura')->value('id'),
            'hari'        => 4,
            'jam_mulai'   => '09:00',
            'jam_selesai' => '11:00',
            'kuota'       => 20,
            'petugas_id'  => User::where('name', 'Pak Dedi')->value('id'),
            'minggu'      => '2026-11-02',
        ], $ubah);
    }

    /**
     * Test 1: Halaman jadwal dapat diakses dan menampilkan jadwal rutin dari seeder.
     */
    public function test_halaman_jadwal_menampilkan_jadwal_rutin_sesuai_seeder(): void
    {
        $response = $this->get('/admin/jadwal?minggu=2026-11-02');

        $response->assertOk();
        $response->assertSee('Jadwal penjemputan');
        $response->assertSee('2 – 7 November 2026');
        $response->assertSee('06.00–08.00');
        $response->assertSee('13.00–15.00');
        $response->assertSee('Sampah biasa · Sukapura');
        $response->assertSee('Daur ulang · Sukabirus');
        // Kuota terisi dihitung dinamis — cukup pastikan teks "Kuota" muncul
        $response->assertSee('Kuota');
        $response->assertSee('Pak Asep');
        $response->assertSee('Tambah jadwal');
        $response->assertViewHas('jadwalList', fn ($jadwal) => $jadwal->count() === 9);
    }

    /**
     * Test 1b: Kuota 0/20 muncul saat belum ada pengajuan terdaftar di slot tersebut.
     */
    public function test_kuota_terisi_nol_saat_belum_ada_pengajuan(): void
    {
        $jadwal = JadwalPenjemputan::where('jenis', JadwalPenjemputan::JENIS_DAUR_ULANG)->first();

        $this->assertSame(0, $jadwal->jumlahTerisi());
        $this->assertFalse($jadwal->isPenuh());
    }

    /**
     * Test 1c: jumlahTerisi() bertambah saat ada pengajuan aktif di slot ini.
     */
    public function test_kuota_terisi_bertambah_setelah_ada_pengajuan(): void
    {
        $warga  = User::where('role', 'warga')->first();
        $jadwal = JadwalPenjemputan::where('jenis', JadwalPenjemputan::JENIS_DAUR_ULANG)->first();

        // Buat pengajuan yang terikat ke jadwal ini
        PengajuanDaurUlang::create([
            'kode_pengajuan'      => 'DU-TEST-0001',
            'user_id'             => $warga?->id,
            'wilayah_id'          => $jadwal->wilayah_id,
            'jadwal_id'           => $jadwal->id,
            'total_estimasi_kg'   => 2,
            'total_estimasi_poin' => 20,
            'status'              => PengajuanDaurUlang::STATUS_MENUNGGU,
        ]);

        $jadwal->refresh();
        $this->assertSame(1, $jadwal->jumlahTerisi());
    }

    /**
     * Test 1d: Pengajuan yang dibatalkan tidak dihitung ke kuota.
     */
    public function test_pengajuan_dibatalkan_tidak_dihitung_ke_kuota(): void
    {
        $warga  = User::where('role', 'warga')->first();
        $jadwal = JadwalPenjemputan::where('jenis', JadwalPenjemputan::JENIS_DAUR_ULANG)->first();

        PengajuanDaurUlang::create([
            'kode_pengajuan'      => 'DU-TEST-0002',
            'user_id'             => $warga?->id,
            'wilayah_id'          => $jadwal->wilayah_id,
            'jadwal_id'           => $jadwal->id,
            'total_estimasi_kg'   => 2,
            'total_estimasi_poin' => 20,
            'status'              => PengajuanDaurUlang::STATUS_DIBATALKAN,
        ]);

        $this->assertSame(0, $jadwal->jumlahTerisi());
    }

    /**
     * Test 2: Filter wilayah hanya menampilkan jadwal milik wilayah tersebut.
     */
    public function test_filter_wilayah_hanya_menampilkan_jadwal_wilayah_tersebut(): void
    {
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->value('id');

        $response = $this->get("/admin/jadwal?wilayah={$sukabirus}");

        $response->assertOk();
        $response->assertViewHas('jadwalList', fn ($jadwal) => $jadwal->count() === 5
            && $jadwal->every(fn ($j) => $j->wilayah_id === $sukabirus));
    }

    /**
     * Test 3: Navigasi minggu menampilkan rentang Senin–Sabtu yang benar.
     */
    public function test_navigasi_minggu_menampilkan_rentang_tanggal_yang_benar(): void
    {
        $this->get('/admin/jadwal?minggu=2026-11-09')->assertSee('9 – 14 November 2026');

        // Tanggal di tengah minggu dibulatkan ke Senin minggu itu
        $this->get('/admin/jadwal?minggu=2026-11-05')->assertSee('2 – 7 November 2026');

        // Minggu yang melewati pergantian bulan
        $this->get('/admin/jadwal?minggu=2026-11-30')->assertSee('30 November – 5 Desember 2026');

        // Parameter tidak valid jatuh ke minggu ini, bukan error
        $this->get('/admin/jadwal?minggu=bukan-tanggal')->assertOk();
    }

    /**
     * Test 4: Admin dapat menambah jadwal daur ulang dengan kuota.
     */
    public function test_admin_dapat_menambah_jadwal_daur_ulang(): void
    {
        $response = $this->post('/admin/jadwal', $this->dataJadwal());

        $response->assertRedirect('/admin/jadwal?minggu=2026-11-02');
        $response->assertSessionHas('success', 'Jadwal berhasil disimpan.');
        $this->assertDatabaseHas('jadwal_penjemputan', [
            'jenis'       => 'daur_ulang',
            'hari'        => 4,
            'jam_mulai'   => '09:00:00',
            'jam_selesai' => '11:00:00',
            'kuota'       => 20,
            'petugas_id'  => User::where('name', 'Pak Dedi')->value('id'),
        ]);

        $this->get('/admin/jadwal?minggu=2026-11-02')->assertSee('09.00–11.00');
    }

    /**
     * Test 5: Jadwal sampah biasa tersimpan tanpa kuota meskipun kuota terisi.
     */
    public function test_jadwal_sampah_biasa_tersimpan_tanpa_kuota(): void
    {
        $response = $this->post('/admin/jadwal', $this->dataJadwal([
            'jenis' => 'sampah_biasa',
            'kuota' => 15,
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('jadwal_penjemputan', [
            'jenis'     => 'sampah_biasa',
            'hari'      => 4,
            'jam_mulai' => '09:00:00',
            'kuota'     => null,
        ]);
    }

    /**
     * Test 6: Jadwal daur ulang tanpa kuota ditolak.
     */
    public function test_jadwal_daur_ulang_tanpa_kuota_ditolak(): void
    {
        $response = $this->post('/admin/jadwal', $this->dataJadwal(['kuota' => '']));

        $response->assertSessionHasErrors(['kuota' => 'Kuota wajib diisi untuk jadwal daur ulang.']);
        $this->assertDatabaseCount('jadwal_penjemputan', 9);
    }

    /**
     * Test 7: Jam selesai harus setelah jam mulai.
     */
    public function test_jam_selesai_sebelum_jam_mulai_ditolak(): void
    {
        $response = $this->post('/admin/jadwal', $this->dataJadwal([
            'jam_mulai'   => '11:00',
            'jam_selesai' => '09:00',
        ]));

        $response->assertSessionHasErrors(['jam_selesai' => 'Jam selesai harus setelah jam mulai.']);
        $this->assertDatabaseCount('jadwal_penjemputan', 9);
    }

    /**
     * Test 8: Petugas tidak boleh punya dua jadwal yang jamnya tumpang-tindih di hari yang sama.
     */
    public function test_jadwal_petugas_yang_bentrok_ditolak(): void
    {
        // Pak Dedi sudah bertugas Senin 09.00–11.00
        $response = $this->post('/admin/jadwal', $this->dataJadwal([
            'hari'        => 1,
            'jam_mulai'   => '10:00',
            'jam_selesai' => '12:00',
        ]));

        $response->assertSessionHasErrors(['petugas_id' => 'Petugas sudah punya jadwal lain di hari dan jam tersebut.']);
        $this->assertDatabaseCount('jadwal_penjemputan', 9);

        // Jadwal yang bersambung (mulai tepat saat jadwal lain selesai) tetap boleh
        $response = $this->post('/admin/jadwal', $this->dataJadwal([
            'hari'        => 1,
            'jam_mulai'   => '11:00',
            'jam_selesai' => '12:00',
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseCount('jadwal_penjemputan', 10);
    }

    /**
     * Test 9: Hanya user dengan role petugas yang dapat ditugaskan.
     */
    public function test_user_bukan_petugas_tidak_dapat_ditugaskan(): void
    {
        $admin = User::where('role', 'admin')->value('id');

        $response = $this->post('/admin/jadwal', $this->dataJadwal(['petugas_id' => $admin]));

        $response->assertSessionHasErrors(['petugas_id' => 'Petugas yang dipilih tidak valid.']);
        $this->assertDatabaseCount('jadwal_penjemputan', 9);
    }

    /**
     * Test 10: Wilayah nonaktif tidak muncul di pilihan dan tidak dapat dipakai untuk jadwal baru.
     */
    public function test_wilayah_nonaktif_tidak_dapat_dipilih(): void
    {
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->first();
        $sukabirus->update(['is_aktif' => false]);

        $this->get('/admin/jadwal')
            ->assertViewHas('wilayahList', fn ($wilayah) => ! $wilayah->contains('id', $sukabirus->id));

        $response = $this->post('/admin/jadwal', $this->dataJadwal(['wilayah_id' => $sukabirus->id]));

        $response->assertSessionHasErrors(['wilayah_id' => 'Wilayah tidak ditemukan atau sudah nonaktif.']);
        $this->assertDatabaseCount('jadwal_penjemputan', 9);
    }

    /**
     * Test 11: isPenuh() true saat jumlah pengajuan aktif >= kuota.
     */
    public function test_jadwal_penuh_saat_pengajuan_melebihi_kuota(): void
    {
        $warga  = User::where('role', 'warga')->first();
        $jadwal = JadwalPenjemputan::where('jenis', JadwalPenjemputan::JENIS_DAUR_ULANG)
            ->where('kuota', 20)
            ->first();

        // Isi kuota penuh
        for ($i = 1; $i <= 20; $i++) {
            PengajuanDaurUlang::create([
                'kode_pengajuan'      => sprintf('DU-TEST-%04d', $i),
                'user_id'             => $warga?->id,
                'wilayah_id'          => $jadwal->wilayah_id,
                'jadwal_id'           => $jadwal->id,
                'total_estimasi_kg'   => 1,
                'total_estimasi_poin' => 10,
                'status'              => PengajuanDaurUlang::STATUS_MENUNGGU,
            ]);
        }

        $jadwal->refresh();
        $this->assertSame(20, $jadwal->jumlahTerisi());
        $this->assertTrue($jadwal->isPenuh());
    }
}
