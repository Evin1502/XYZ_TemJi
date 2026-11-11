<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * PBI 2: Warga dapat memilih wilayah (Sukapura atau Sukabirus) saat mendaftar akun.
 * PIC: AISYA DEVINA
 * Skenario Uji: Warga mendaftar akun dan memilih Sukapura atau Sukabirus;
 * tanpa memilih wilayah pendaftaran gagal; wilayah tersimpan di profil.
 */
class Pbi2PemilihanWilayahTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(WilayahSeeder::class);
    }

    private function fileIdentitasTiruan(string $nama = 'ktp_uji.png'): UploadedFile
    {
        return UploadedFile::fake()->create($nama, 100, 'image/png');
    }

    /**
     * Skenario 1: Dropdown formulir registrasi menampilkan pilihan wilayah Sukapura dan Sukabirus yang aktif.
     */
    public function test_dropdown_registrasi_menampilkan_pilihan_sukapura_dan_sukabirus(): void
    {
        $response = $this->get(route('warga.registrasi.form'));

        $response->assertOk();
        $response->assertSee('Sukapura');
        $response->assertSee('Sukabirus');
        $response->assertSee('Wilayah Layanan Domisili');
    }

    /**
     * Skenario 2: Warga mendaftar akun dan memilih Sukapura; data wilayah tersimpan di profil.
     */
    public function test_warga_mendaftar_dan_memilih_sukapura_wilayah_tersimpan_di_profil(): void
    {
        Storage::fake('public');

        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap'          => 'Ahmad Sukapura',
            'nik'                   => '3204081010900001',
            'no_kk'                 => '3204082020900001',
            'email'                 => 'ahmad.sukapura@temji.id',
            'no_hp'                 => '081234567891',
            'wilayah_id'            => $sukapura->id,
            'alamat'                => 'Jl. Radio Sukapura RT 01/RW 02 No. 12',
            'foto_identitas'        => $this->fileIdentitasTiruan(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user  = User::where('email', 'ahmad.sukapura@temji.id')->firstOrFail();
        $warga = Warga::where('user_id', $user->id)->firstOrFail();

        // Wilayah tersimpan di profil (User & Warga)
        $this->assertEquals($sukapura->id, $warga->wilayah_id);
        $this->assertEquals($sukapura->id, $user->wilayah_id);
        $this->assertSame('Sukapura', $warga->wilayah->nama);

        $response->assertRedirect(route('warga.registrasi.status', ['warga' => $warga->id]));

        // Wilayah tampil di halaman status profil
        $statusPage = $this->actingAs($user)->get(route('warga.registrasi.status', ['warga' => $warga->id]));
        $statusPage->assertOk();
        $statusPage->assertSee('Sukapura');
    }

    /**
     * Skenario 3: Warga mendaftar akun dan memilih Sukabirus; data wilayah tersimpan di profil.
     */
    public function test_warga_mendaftar_dan_memilih_sukabirus_wilayah_tersimpan_di_profil(): void
    {
        Storage::fake('public');

        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap'          => 'Siti Sukabirus',
            'nik'                   => '3204082020900002',
            'no_kk'                 => '3204083030900002',
            'email'                 => 'siti.sukabirus@temji.id',
            'no_hp'                 => '081234567892',
            'wilayah_id'            => $sukabirus->id,
            'alamat'                => 'Gang Sukabirus No. 45 RT 03/RW 01',
            'foto_identitas'        => $this->fileIdentitasTiruan(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user  = User::where('email', 'siti.sukabirus@temji.id')->firstOrFail();
        $warga = Warga::where('user_id', $user->id)->firstOrFail();

        $this->assertEquals($sukabirus->id, $warga->wilayah_id);
        $this->assertEquals($sukabirus->id, $user->wilayah_id);
        $this->assertSame('Sukabirus', $warga->wilayah->nama);

        $response->assertRedirect(route('warga.registrasi.status', ['warga' => $warga->id]));

        $statusPage = $this->actingAs($user)->get(route('warga.registrasi.status', ['warga' => $warga->id]));
        $statusPage->assertOk();
        $statusPage->assertSee('Sukabirus');
    }

    /**
     * Skenario 4: Tanpa memilih wilayah, pendaftaran gagal.
     */
    public function test_tanpa_memilih_wilayah_pendaftaran_gagal(): void
    {
        Storage::fake('public');

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap'          => 'Warga Tanpa Wilayah',
            'nik'                   => '3204083030900003',
            'no_kk'                 => '3204084040900003',
            'email'                 => 'tanpawilayah@temji.id',
            'no_hp'                 => '081234567893',
            'wilayah_id'            => '', // Kosong / tanpa memilih wilayah
            'alamat'                => 'Jl. Raya No. 1',
            'foto_identitas'        => $this->fileIdentitasTiruan(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['wilayah_id']);
        $this->assertDatabaseMissing('users', ['email' => 'tanpawilayah@temji.id']);
        $this->assertDatabaseMissing('warga', ['nik' => '3204083030900003']);
    }

    /**
     * Skenario 5: Pendaftaran dengan wilayah nonaktif ditolak.
     */
    public function test_pilihan_wilayah_nonaktif_ditolak(): void
    {
        Storage::fake('public');

        $wilayahNonaktif = Wilayah::create([
            'nama'      => 'Wilayah Uji Nonaktif',
            'jumlah_kk' => 0,
            'is_aktif'  => false,
        ]);

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap'          => 'Warga Wilayah Nonaktif',
            'nik'                   => '3204084040900004',
            'no_kk'                 => '3204085050900004',
            'email'                 => 'nonaktif@temji.id',
            'no_hp'                 => '081234567894',
            'wilayah_id'            => $wilayahNonaktif->id,
            'alamat'                => 'Jl. Uji No. 99',
            'foto_identitas'        => $this->fileIdentitasTiruan(),
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['wilayah_id']);
        $this->assertDatabaseMissing('users', ['email' => 'nonaktif@temji.id']);
    }

    /**
     * Skenario 6: Wilayah warga terdaftar tampil pada portal dashboard warga.
     */
    public function test_wilayah_terpilih_tampil_pada_portal_dashboard_warga(): void
    {
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();

        $user = User::create([
            'name'       => 'Warga Dashboard',
            'email'      => 'dashboard.warga@temji.id',
            'role'       => 'warga',
            'password'   => 'password123',
            'wilayah_id' => $sukabirus->id,
        ]);

        Warga::create([
            'user_id'           => $user->id,
            'nik'               => '3204085050900005',
            'no_kk'             => '3204086060900005',
            'nama_lengkap'      => 'Warga Dashboard',
            'no_hp'             => '081234567895',
            'alamat'            => 'Jl. Sukabirus RT 02/RW 03 No. 7',
            'wilayah_id'        => $sukabirus->id,
            'foto_identitas'    => 'identitas_warga/sample_ktp.jpg',
            'status_verifikasi' => Warga::STATUS_TERVERIFIKASI,
        ]);

        $response = $this->actingAs($user)->get(route('warga.dashboard'));

        $response->assertOk();
        $response->assertSee('Sukabirus');
    }
}
