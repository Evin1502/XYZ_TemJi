<?php

namespace Tests\Feature;

use App\Models\PenjemputanTambahan;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjemputanTambahanAdminTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    private User $admin;

    /** Pengajuan Budi (Sukapura) yang masih menunggu, dari seeder. */
    private PenjemputanTambahan $pengajuan;

    protected function setUp(): void
    {
        parent::setUp();

        // Waktu dibekukan di hari Jumat agar hari dalam minggu pada skenario di bawah selalu sama
        Carbon::setTestNow(Carbon::parse('2026-11-13 09:00:00', 'Asia/Jakarta'));

        $this->admin = $this->actingAsAdmin();
        $this->seed();

        $this->pengajuan = PenjemputanTambahan::where('kode', 'PT-SEED-0001')->firstOrFail();
    }

    private function petugas(string $nama): int
    {
        return User::where('name', $nama)->value('id');
    }

    /**
     * Data form setujui yang valid: Pak Asep, Selasa 17 November 2026, 10.00–11.00.
     */
    private function dataSetujui(array $ubah = []): array
    {
        return array_merge([
            'petugas_id' => $this->petugas('Pak Asep'),
            'tanggal_jemput' => '2026-11-17',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
        ], $ubah);
    }

    private function setujui(array $ubah = [], ?PenjemputanTambahan $pengajuan = null)
    {
        $pengajuan ??= $this->pengajuan;

        return $this->patch("/admin/penjemputan-tambahan/{$pengajuan->id}/setujui", $this->dataSetujui($ubah));
    }

    /**
     * Test 1: Halaman menampilkan pengajuan yang menunggu beserta hitungan per status.
     */
    public function test_halaman_menampilkan_pengajuan_yang_menunggu(): void
    {
        $response = $this->get('/admin/penjemputan-tambahan');

        $response->assertOk();
        $response->assertSee('Penjemputan tambahan');
        $response->assertSee('PT-SEED-0001');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Setujui & jadwalkan', false);
        $response->assertSee('Menunggu (1)');
        $response->assertSee('Semua (4)');

        // Default hanya yang menunggu
        $response->assertDontSee('PT-SEED-0003');
        $response->assertViewHas('terpilih', fn ($terpilih) => $terpilih->is($this->pengajuan));
    }

    /**
     * Test 2: Filter status dan wilayah mempersempit daftar.
     */
    public function test_filter_status_dan_wilayah(): void
    {
        $this->get('/admin/penjemputan-tambahan?status=disetujui')
            ->assertSee('PT-SEED-0003')
            ->assertDontSee('PT-SEED-0001')
            ->assertDontSee('Setujui & jadwalkan', false);

        $sukabirus = Wilayah::where('nama', 'Sukabirus')->value('id');

        $this->get("/admin/penjemputan-tambahan?status=semua&wilayah={$sukabirus}")
            ->assertViewHas('pengajuanList', fn ($list) => $list->pluck('kode')->all() === ['PT-SEED-0004']);

        // Status tidak dikenal jatuh ke "menunggu", bukan error
        $this->get('/admin/penjemputan-tambahan?status=ngawur')->assertOk()->assertViewHas('status', 'menunggu');
    }

    /**
     * Test 3: Admin menyetujui pengajuan dan memasukkannya ke jadwal petugas.
     */
    public function test_admin_dapat_menyetujui_dan_menjadwalkan(): void
    {
        $response = $this->setujui();

        $response->assertRedirect('/admin/penjemputan-tambahan');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penjemputan_tambahan', [
            'id' => $this->pengajuan->id,
            'status' => 'disetujui',
            'petugas_id' => $this->petugas('Pak Asep'),
            'jam_mulai' => '10:00:00',
            'jam_selesai' => '11:00:00',
            'diproses_oleh' => $this->admin->id,
        ]);
        $this->assertSame('2026-11-17', $this->pengajuan->fresh()->tanggal_jemput->toDateString());
        $this->assertNotNull($this->pengajuan->fresh()->diproses_pada);
    }

    /**
     * Test 4: Penjemputan tambahan tidak boleh menabrak jadwal rutin petugas.
     */
    public function test_bentrok_dengan_jadwal_rutin_ditolak(): void
    {
        // Pak Asep rutin Senin 06.00–08.00 di Sukapura
        $this->setujui(['tanggal_jemput' => '2026-11-16', 'jam_mulai' => '07:00', 'jam_selesai' => '09:00'])
            ->assertSessionHasErrors(['petugas_id' => 'Petugas sudah punya jadwal lain pada tanggal dan jam tersebut.']);

        $this->assertSame('menunggu', $this->pengajuan->fresh()->status);

        // Tepat setelah jadwal rutin selesai tetap boleh
        $this->setujui(['tanggal_jemput' => '2026-11-16', 'jam_mulai' => '08:00', 'jam_selesai' => '09:00'])
            ->assertSessionHasNoErrors();
    }

    /**
     * Test 5: Petugas tidak boleh punya dua penjemputan tambahan di waktu yang sama.
     */
    public function test_bentrok_dengan_penjemputan_tambahan_lain_ditolak(): void
    {
        // PT-SEED-0003: Pak Dedi, Selasa 17 November 2026, 13.00–14.00
        $this->setujui(['petugas_id' => $this->petugas('Pak Dedi'), 'jam_mulai' => '13:30', 'jam_selesai' => '14:30'])
            ->assertSessionHasErrors('petugas_id');

        $this->assertSame('menunggu', $this->pengajuan->fresh()->status);

        // Petugas lain di jam yang sama tidak bentrok
        $this->setujui(['petugas_id' => $this->petugas('Pak Asep'), 'jam_mulai' => '13:30', 'jam_selesai' => '14:30'])
            ->assertSessionHasNoErrors();
    }

    /**
     * Test 6: Validasi form setujui.
     */
    public function test_validasi_form_setujui(): void
    {
        $this->setujui(['petugas_id' => $this->admin->id])
            ->assertSessionHasErrors(['petugas_id' => 'Petugas yang dipilih tidak valid.']);

        $this->setujui(['tanggal_jemput' => '2026-11-12'])
            ->assertSessionHasErrors(['tanggal_jemput' => 'Tanggal jemput tidak boleh sebelum hari ini.']);

        // 15 November 2026 adalah hari Minggu
        $this->setujui(['tanggal_jemput' => '2026-11-15'])
            ->assertSessionHasErrors(['tanggal_jemput' => 'Penjemputan hanya dapat dijadwalkan Senin sampai Sabtu.']);

        $this->setujui(['jam_mulai' => '11:00', 'jam_selesai' => '10:00'])
            ->assertSessionHasErrors(['jam_selesai' => 'Jam selesai harus setelah jam mulai.']);

        $this->assertSame('menunggu', $this->pengajuan->fresh()->status);
    }

    /**
     * Test 7: Admin menolak pengajuan dengan alasan wajib.
     */
    public function test_admin_dapat_menolak_dengan_alasan(): void
    {
        $this->patch("/admin/penjemputan-tambahan/{$this->pengajuan->id}/tolak", ['alasan_penolakan' => ''])
            ->assertSessionHasErrors(['alasan_penolakan' => 'Alasan penolakan wajib diisi.']);
        $this->assertSame('menunggu', $this->pengajuan->fresh()->status);

        $this->patch("/admin/penjemputan-tambahan/{$this->pengajuan->id}/tolak", [
            'alasan_penolakan' => 'Jadwal rutin wilayah Anda jatuh keesokan harinya.',
        ])->assertRedirect('/admin/penjemputan-tambahan')->assertSessionHas('success');

        $this->assertDatabaseHas('penjemputan_tambahan', [
            'id' => $this->pengajuan->id,
            'status' => 'ditolak',
            'alasan_penolakan' => 'Jadwal rutin wilayah Anda jatuh keesokan harinya.',
            'petugas_id' => null,
            'diproses_oleh' => $this->admin->id,
        ]);
    }

    /**
     * Test 8: Pengajuan yang sudah diproses atau dibatalkan tidak bisa diproses lagi.
     */
    public function test_pengajuan_yang_sudah_diproses_tidak_dapat_diproses_lagi(): void
    {
        $disetujui = PenjemputanTambahan::where('kode', 'PT-SEED-0003')->firstOrFail();
        $dibatalkan = PenjemputanTambahan::where('kode', 'PT-SEED-0002')->firstOrFail();

        $this->patch("/admin/penjemputan-tambahan/{$disetujui->id}/tolak", ['alasan_penolakan' => 'Berubah pikiran.'])
            ->assertSessionHasErrors('status');
        $this->assertSame('disetujui', $disetujui->fresh()->status);

        $this->setujui(pengajuan: $dibatalkan)->assertSessionHasErrors('status');
        $this->assertSame('dibatalkan', $dibatalkan->fresh()->status);

        $this->patch('/admin/penjemputan-tambahan/999999/tolak', ['alasan_penolakan' => 'Tidak ada.'])->assertNotFound();
    }

    /**
     * Test 9: Penjemputan tambahan yang disetujui tampil di kalender jadwal pada minggunya saja.
     */
    public function test_penjemputan_tambahan_disetujui_tampil_di_kalender_jadwal(): void
    {
        $this->setujui();

        $this->get('/admin/jadwal?minggu=2026-11-16')
            ->assertSee('Tambahan · Sukapura')
            ->assertSee('10.00–11.00')
            ->assertSee('PT-SEED-0001 · Pak Asep')
            ->assertViewHas('tambahanPerHari', fn ($perHari) => $perHari->get(2)?->count() === 2);

        $this->get('/admin/jadwal?minggu=2026-11-23')
            ->assertDontSee('PT-SEED-0001')
            ->assertViewHas('tambahanPerHari', fn ($perHari) => $perHari->isEmpty());
    }

    /**
     * Test 10: Warga melihat hasil proses admin di halamannya.
     */
    public function test_warga_melihat_hasil_persetujuan_dan_penolakan(): void
    {
        $this->setujui();

        $this->actingAs($this->pengajuan->user);
        $this->get('/warga/jadwal')
            ->assertSee('Disetujui')
            ->assertSee('Selasa, 17 November 2026 pukul 10.00–11.00 oleh Pak Asep');

        $this->actingAs(User::where('email', 'siti.aminah@temji.id')->firstOrFail());
        $this->get('/warga/jadwal')
            ->assertSee('Ditolak')
            ->assertSee('Alasan penolakan: Jadwal rutin wilayah Anda jatuh sehari setelah tanggal yang diminta.');
    }

    /**
     * Test 11: Hanya admin yang dapat mengakses dan memproses.
     */
    public function test_non_admin_tidak_dapat_mengakses(): void
    {
        $this->actingAs(User::where('email', 'warga@temji.test')->firstOrFail());

        $this->get('/admin/penjemputan-tambahan')->assertRedirect(route('warga.dashboard'));
        $this->setujui()->assertRedirect(route('warga.dashboard'));

        $this->actingAs(User::where('name', 'Pak Asep')->firstOrFail());
        $this->patch("/admin/penjemputan-tambahan/{$this->pengajuan->id}/tolak", ['alasan_penolakan' => 'Coba-coba.'])
            ->assertRedirect(route('petugas.dashboard'));

        $this->assertSame('menunggu', $this->pengajuan->fresh()->status);
    }
}
