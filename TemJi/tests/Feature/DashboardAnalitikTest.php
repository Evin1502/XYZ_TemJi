<?php

namespace Tests\Feature;

use App\Models\KategoriDaurUlang;
use App\Models\PengajuanDaurUlang;
use App\Models\PengajuanDaurUlangItem;
use App\Models\User;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAnalitikTest extends TestCase
{
    use RefreshDatabase;
    use \Tests\Support\AuthAsRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Admin dapat mengakses dashboard analitik.
     */
    public function test_admin_dapat_mengakses_dashboard_analitik(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard Analitik TemJi');
        $response->assertSee('Ekspor Laporan');
        $response->assertViewHas('trendData');
    }

    /**
     * Test 2: Filter wilayah, kategori, dan periode berfungsi tanpa error query.
     */
    public function test_filter_dashboard_analitik_berfungsi(): void
    {
        $this->actingAsAdmin();

        $sukapura = Wilayah::where('nama', 'Sukapura')->value('id');
        $kategori = KategoriDaurUlang::first()?->id;

        // Uji periode harian
        $responseHarian = $this->get("/admin/dashboard?wilayah={$sukapura}&kategori={$kategori}&periode=harian");
        $responseHarian->assertOk();

        // Uji periode mingguan
        $responseMingguan = $this->get("/admin/dashboard?wilayah={$sukapura}&kategori={$kategori}&periode=mingguan");
        $responseMingguan->assertOk();

        // Uji periode bulanan
        $responseBulanan = $this->get("/admin/dashboard?wilayah={$sukapura}&kategori={$kategori}&periode=bulanan");
        $responseBulanan->assertOk();
    }

    /**
     * Test 3: Admin dapat mengekspor laporan tren ke format CSV (PBI 17).
     */
    public function test_admin_dapat_ekspor_laporan_csv(): void
    {
        $this->actingAsAdmin();

        // Buat data pengajuan selesai
        $warga    = User::where('role', 'warga')->first();
        $wilayah  = Wilayah::first();
        $kategori = KategoriDaurUlang::first();

        $pengajuan = PengajuanDaurUlang::create([
            'kode_pengajuan'      => 'DU-TEST-EXP1',
            'user_id'             => $warga?->id,
            'wilayah_id'          => $wilayah?->id,
            'total_estimasi_kg'   => 5,
            'total_estimasi_poin' => 50,
            'status'              => 'selesai',
        ]);

        PengajuanDaurUlangItem::create([
            'pengajuan_id'         => $pengajuan->id,
            'kategori_id'          => $kategori?->id,
            'estimasi_berat_kg'    => 5,
            'poin_per_kg_snapshot' => 10,
            'estimasi_poin'        => 50,
        ]);

        $response = $this->get('/admin/dashboard/export?format=csv');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('temji-laporan-harian', (string) $response->headers->get('content-disposition'));

        // Cek isi stream CSV
        $content = $response->streamedContent();
        $this->assertStringContainsString('Periode', $content);
        $this->assertStringContainsString('Jumlah Penjemputan', $content);
        $this->assertStringContainsString('Total Berat Daur Ulang (Kg)', $content);
        $this->assertStringContainsString('5.00', $content);
    }

    /**
     * Test 4: Alias rute analitik dapat diakses admin.
     */
    public function test_rute_analitik_alias_dapat_diakses(): void
    {
        $this->actingAsAdmin();

        $response = $this->get('/admin/analitik');
        $response->assertOk();
        $response->assertSee('Dashboard Analitik TemJi');
    }

    /**
     * Test 5: Non-admin dan tamu tidak dapat mengakses dashboard analitik.
     */
    public function test_non_admin_dan_tamu_dialihkan(): void
    {
        $responseTamu = $this->get('/admin/dashboard');
        $responseTamu->assertRedirect('/login');

        $this->actingAsWarga();
        $responseWarga = $this->get('/admin/dashboard');
        $responseWarga->assertRedirect('/warga/dashboard');
    }
}
