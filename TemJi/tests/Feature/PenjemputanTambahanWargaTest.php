<?php

namespace Tests\Feature;

use App\Models\PenjemputanTambahan;
use App\Models\User;
use App\Models\Warga;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjemputanTambahanWargaTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    private User $warga;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        // Ratna: warga Sukapura dari seeder, belum punya pengajuan penjemputan tambahan
        $this->warga = User::where('email', 'warga@temji.test')->firstOrFail();
        $this->actingAs($this->warga);
    }

    private function tanggal(int $hariKeDepan): string
    {
        return Carbon::today('Asia/Jakarta')->addDays($hariKeDepan)->toDateString();
    }

    private function dataPengajuan(array $ubah = []): array
    {
        return array_merge([
            'tanggal_diminta' => $this->tanggal(2),
            'alasan' => 'Sampah menumpuk setelah acara keluarga.',
        ], $ubah);
    }

    /**
     * Test 1: Warga melihat jadwal rutin wilayahnya sendiri saja.
     */
    public function test_warga_melihat_jadwal_rutin_wilayahnya_sendiri(): void
    {
        $response = $this->get('/warga/jadwal');

        $response->assertOk();
        $response->assertSee('Jadwal penjemputan');
        $response->assertSee('Sukapura');
        $response->assertSee('06.00–08.00');
        $response->assertSee('Pak Asep');
        $response->assertSee('Ajukan penjemputan tambahan');

        // Pak Ujang hanya bertugas di Sukabirus
        $response->assertDontSee('Pak Ujang');
        $response->assertViewHas('jadwalPerHari', fn ($perHari) => $perHari->flatten()->count() === 4
            && $perHari->flatten()->every(fn ($j) => $j->wilayah_id === $this->warga->wilayah_id_efektif));
    }

    /**
     * Test 2: Warga dapat mengajukan penjemputan tambahan; wilayah diambil dari data warga.
     */
    public function test_warga_dapat_mengajukan_penjemputan_tambahan(): void
    {
        $response = $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['wilayah_id' => 999]));

        $response->assertRedirect('/warga/jadwal');
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('penjemputan_tambahan', [
            'user_id' => $this->warga->id,
            'wilayah_id' => $this->warga->wilayah_id_efektif,
            'alasan' => 'Sampah menumpuk setelah acara keluarga.',
            'status' => 'menunggu',
        ]);

        $pengajuan = $this->warga->penjemputanTambahan()->firstOrFail();
        $this->assertSame($this->tanggal(2), $pengajuan->tanggal_diminta->toDateString());
        $this->assertMatchesRegularExpression('/^PT-\d{4}-\d{4}$/', $pengajuan->kode);

        $this->get('/warga/jadwal')->assertSee($pengajuan->kode)->assertSee('Menunggu');
    }

    /**
     * Test 3: Tanggal harus antara besok sampai 14 hari ke depan.
     */
    public function test_tanggal_di_luar_rentang_ditolak(): void
    {
        foreach ([$this->tanggal(0), $this->tanggal(-1), $this->tanggal(15), 'bukan-tanggal', ''] as $tanggal) {
            $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['tanggal_diminta' => $tanggal]))
                ->assertSessionHasErrors('tanggal_diminta');
        }

        $this->assertSame(0, $this->warga->penjemputanTambahan()->count());

        // Batas rentang masih diterima
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['tanggal_diminta' => $this->tanggal(14)]))
            ->assertSessionHasNoErrors();
    }

    /**
     * Test 4: Alasan wajib diisi dan minimal 10 karakter.
     */
    public function test_alasan_kosong_atau_terlalu_pendek_ditolak(): void
    {
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['alasan' => '']))
            ->assertSessionHasErrors(['alasan' => 'Alasan pengajuan wajib diisi.']);

        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['alasan' => 'penuh']))
            ->assertSessionHasErrors(['alasan' => 'Alasan pengajuan minimal 10 karakter.']);

        $this->assertSame(0, $this->warga->penjemputanTambahan()->count());
    }

    /**
     * Test 5: Warga tidak bisa mengajukan lagi selama masih ada pengajuan yang menunggu.
     */
    public function test_pengajuan_ganda_saat_masih_menunggu_ditolak(): void
    {
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan())->assertSessionHasNoErrors();

        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan(['tanggal_diminta' => $this->tanggal(5)]))
            ->assertSessionHasErrors('tanggal_diminta');

        $this->assertSame(1, $this->warga->penjemputanTambahan()->count());
    }

    /**
     * Test 6: Warga yang belum punya wilayah tidak bisa mengajukan.
     */
    public function test_warga_tanpa_wilayah_tidak_dapat_mengajukan(): void
    {
        Warga::where('user_id', $this->warga->id)->update(['wilayah_id' => null]);

        $this->get('/warga/jadwal')
            ->assertOk()
            ->assertSee('Wilayah Anda belum terdaftar')
            ->assertViewHas('jadwalPerHari', fn ($perHari) => $perHari->isEmpty());

        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan())
            ->assertSessionHasErrors('wilayah');

        $this->assertDatabaseMissing('penjemputan_tambahan', ['user_id' => $this->warga->id]);
    }

    /**
     * Test 7: Warga dapat membatalkan pengajuannya yang masih menunggu, lalu boleh mengajukan lagi.
     */
    public function test_warga_dapat_membatalkan_pengajuan_yang_menunggu(): void
    {
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan());
        $pengajuan = $this->warga->penjemputanTambahan()->firstOrFail();

        $this->patch("/warga/penjemputan-tambahan/{$pengajuan->id}/batal")
            ->assertRedirect('/warga/jadwal')
            ->assertSessionHas('success');

        $this->assertSame('dibatalkan', $pengajuan->fresh()->status);

        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan())->assertSessionHasNoErrors();
    }

    /**
     * Test 8: Pengajuan yang sudah diproses atau milik warga lain tidak bisa dibatalkan.
     */
    public function test_pengajuan_yang_sudah_diproses_atau_milik_orang_lain_tidak_bisa_dibatalkan(): void
    {
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan());
        $pengajuan = $this->warga->penjemputanTambahan()->firstOrFail();
        $pengajuan->update(['status' => PenjemputanTambahan::STATUS_DISETUJUI]);

        $this->patch("/warga/penjemputan-tambahan/{$pengajuan->id}/batal")->assertSessionHas('error');
        $this->assertSame('disetujui', $pengajuan->fresh()->status);

        // Pengajuan Budi (seeder) bukan milik Ratna -> 404
        $milikOrangLain = PenjemputanTambahan::where('kode', 'PT-SEED-0001')->firstOrFail();

        $this->patch("/warga/penjemputan-tambahan/{$milikOrangLain->id}/batal")->assertNotFound();
        $this->assertSame('menunggu', $milikOrangLain->fresh()->status);
        $this->get('/warga/jadwal')->assertDontSee('PT-SEED-0001');
    }

    /**
     * Test 9: Halaman hanya untuk warga yang login.
     */
    public function test_tamu_dan_non_warga_tidak_dapat_mengakses(): void
    {
        auth()->logout();
        $this->get('/warga/jadwal')->assertRedirect('/login');

        $this->actingAsAdmin();
        $this->get('/warga/jadwal')->assertRedirect(route('admin.dashboard'));
        $this->post('/warga/penjemputan-tambahan', $this->dataPengajuan())->assertRedirect(route('admin.dashboard'));
        $this->assertSame(2, PenjemputanTambahan::count()); // tetap hanya dua data seeder
    }
}
