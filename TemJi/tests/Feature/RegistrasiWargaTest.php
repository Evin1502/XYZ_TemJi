<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Database\Seeders\WargaSeeder;
use Database\Seeders\WilayahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrasiWargaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(WilayahSeeder::class);
    }

    /**
     * Buat berkas gambar PNG valid tanpa ketergantungan ekstensi GD pada mesin lokal/CI.
     */
    private function buatFotoKtpTiruan(string $namaBerkas = 'ktp_warga.png'): UploadedFile
    {
        $pngSatuPiksel = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
        );

        return UploadedFile::fake()->createWithContent($namaBerkas, $pngSatuPiksel);
    }

    /**
     * Test 1: Halaman formulir registrasi warga dapat diakses dan hanya menampilkan wilayah aktif.
     */
    public function test_halaman_registrasi_warga_dapat_diakses_dan_menampilkan_wilayah_aktif(): void
    {
        Wilayah::create([
            'nama' => 'Wilayah Nonaktif Uji',
            'jumlah_kk' => 0,
            'is_aktif' => false,
        ]);

        $response = $this->get(route('warga.registrasi.form'));

        $response->assertStatus(200);
        $response->assertSee('Formulir Pendaftaran Akun Warga');
        $response->assertSee('Sukapura');
        $response->assertSee('Sukabirus');
        $response->assertDontSee('Wilayah Nonaktif Uji');
    }

    /**
     * Test 2: Warga dapat mendaftar akun baru dengan bukti identitas valid dan berstatus awal menunggu.
     */
    public function test_warga_dapat_mendaftar_akun_dengan_identitas_valid_dan_berstatus_menunggu(): void
    {
        Storage::fake('public');

        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();
        $fotoKtp = $this->buatFotoKtpTiruan('ktp_andi_wijaya.png');

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Andi Wijaya',
            'nik' => '3204081708950009',
            'no_kk' => '3204081708200009',
            'email' => 'andi.wijaya@temji.id',
            'no_hp' => '081234567890',
            'wilayah_id' => $sukapura->id,
            'alamat' => 'Jl. Sukapura No. 88, RT 03/RW 05, Desa Sukapura',
            'foto_identitas' => $fotoKtp,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'andi.wijaya@temji.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('warga', $user->role);

        $warga = Warga::where('nik', '3204081708950009')->first();
        $this->assertNotNull($warga);
        $this->assertEquals($user->id, $warga->user_id);
        $this->assertEquals(Warga::STATUS_MENUNGGU, $warga->status_verifikasi);
        $this->assertEquals($sukapura->id, $warga->wilayah_id);

        Storage::disk('public')->assertExists($warga->foto_identitas);
        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('warga.registrasi.status', ['warga' => $warga->id]));
    }

    /**
     * Test 3: Validasi menolak NIK dan Nomor KK yang tidak terdiri dari tepat 16 digit angka.
     */
    public function test_validasi_menolak_nik_dan_no_kk_yang_bukan_16_digit_angka(): void
    {
        Storage::fake('public');
        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Warga Uji Validasi',
            'nik' => '320408123', // kurang dari 16 digit
            'no_kk' => '320408ABCDEFGHIJ', // mengandung huruf
            'email' => 'uji.digit@temji.id',
            'no_hp' => '081299998888',
            'wilayah_id' => $sukapura->id,
            'alamat' => 'Jl. Sukapura RT 01/RW 01',
            'foto_identitas' => $this->buatFotoKtpTiruan('ktp.png'),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['nik', 'no_kk']);
        $this->assertDatabaseMissing('users', ['email' => 'uji.digit@temji.id']);
    }

    /**
     * Test 4: Validasi menolak pendaftaran dengan NIK atau email yang sudah terdaftar.
     */
    public function test_validasi_menolak_nik_dan_email_duplikat(): void
    {
        Storage::fake('public');
        $this->seed(WargaSeeder::class);

        $sukapura = Wilayah::where('nama', 'Sukapura')->firstOrFail();

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Duplikat Budi',
            'nik' => '3204081205850001', // sudah dipakai Budi Santoso
            'no_kk' => '3204089999990001',
            'email' => 'budi.santoso@temji.id', // sudah dipakai Budi Santoso
            'no_hp' => '081200001111',
            'wilayah_id' => $sukapura->id,
            'alamat' => 'Jl. Mengger Girang No. 15',
            'foto_identitas' => $this->buatFotoKtpTiruan('ktp_duplikat.png'),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['nik', 'email']);
    }

    /**
     * Test 5: Validasi wajib mengunggah berkas foto identitas berformat gambar maksimal 2 MB.
     */
    public function test_validasi_wajib_unggah_foto_identitas_berformat_gambar_maksimal_2mb(): void
    {
        Storage::fake('public');
        $sukabirus = Wilayah::where('nama', 'Sukabirus')->firstOrFail();

        // Kasus A: Tanpa mengunggah berkas
        $responseTanpaFoto = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Tanpa Foto',
            'nik' => '3204081111110001',
            'no_kk' => '3204081111110002',
            'email' => 'tanpafoto@temji.id',
            'no_hp' => '081234567891',
            'wilayah_id' => $sukabirus->id,
            'alamat' => 'Jl. Sukabirus No. 10',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseTanpaFoto->assertSessionHasErrors(['foto_identitas']);

        // Kasus B: Berkas bukan gambar (PDF)
        $berkasPdf = UploadedFile::fake()->create('dokumen_ktp.pdf', 200, 'application/pdf');
        $responsePdf = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Berkas PDF',
            'nik' => '3204081111110007',
            'no_kk' => '3204081111110008',
            'email' => 'berkaspdf@temji.id',
            'no_hp' => '081234567893',
            'wilayah_id' => $sukabirus->id,
            'alamat' => 'Jl. Sukabirus No. 12',
            'foto_identitas' => $berkasPdf,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responsePdf->assertSessionHasErrors(['foto_identitas']);

        // Kasus C: Ukuran berkas melebihi 2048 KB (2 MB)
        $fotoTerlaluBesar = UploadedFile::fake()->create('ktp_besar.png', 3000, 'image/png');
        $responseBesar = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Foto Besar',
            'nik' => '3204081111110003',
            'no_kk' => '3204081111110004',
            'email' => 'fotobesar@temji.id',
            'no_hp' => '081234567892',
            'wilayah_id' => $sukabirus->id,
            'alamat' => 'Jl. Sukabirus No. 11',
            'foto_identitas' => $fotoTerlaluBesar,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $responseBesar->assertSessionHasErrors(['foto_identitas']);
    }

    /**
     * Test 6: Validasi menolak pilihan wilayah yang berstatus nonaktif.
     */
    public function test_validasi_menolak_pilihan_wilayah_nonaktif(): void
    {
        Storage::fake('public');

        $wilayahNonaktif = Wilayah::create([
            'nama' => 'Bojongsoang Luar',
            'jumlah_kk' => 0,
            'is_aktif' => false,
        ]);

        $response = $this->post(route('warga.registrasi.store'), [
            'nama_lengkap' => 'Dewi Lestari',
            'nik' => '3204082222220005',
            'no_kk' => '3204082222220006',
            'email' => 'dewi.lestari@temji.id',
            'no_hp' => '081277776666',
            'wilayah_id' => $wilayahNonaktif->id,
            'alamat' => 'Jl. Raya Bojongsoang No. 5',
            'foto_identitas' => $this->buatFotoKtpTiruan('ktp_dewi.png'),
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['wilayah_id']);
    }

    /**
     * Test 7: Warga dapat memeriksa status verifikasi berdasarkan pencarian 16 digit NIK.
     */
    public function test_warga_dapat_mengecek_status_pendaftaran_berdasarkan_nik(): void
    {
        $this->seed(WargaSeeder::class);

        $responseVerified = $this->get(route('warga.registrasi.status', ['nik' => '3204081205850001']));
        $responseVerified->assertStatus(200);
        $responseVerified->assertSee('Budi Santoso');
        $responseVerified->assertSee('Akun Warga Terverifikasi Aktif');

        $responseRejected = $this->get(route('warga.registrasi.status', ['nik' => '3204082111980003']));
        $responseRejected->assertStatus(200);
        $responseRejected->assertSee('Rian Pratama');
        $responseRejected->assertSee('Foto KTP buram');
        $responseRejected->assertSee('Unggah Ulang Bukti Identitas');
    }

    /**
     * Test 8: Warga dengan status ditolak dapat mengunggah ulang foto identitas perbaikan.
     */
    public function test_warga_yang_ditolak_dapat_mengunggah_ulang_dokumen_perbaikan(): void
    {
        Storage::fake('public');
        $this->seed(WargaSeeder::class);

        $rian = Warga::where('nik', '3204082111980003')->firstOrFail();
        $this->assertTrue($rian->isRejected());

        $fotoPerbaikan = $this->buatFotoKtpTiruan('ktp_rian_jelas.png');

        $response = $this->post(route('warga.registrasi.resubmit', $rian->id), [
            'foto_identitas' => $fotoPerbaikan,
            'alamat' => 'Gang PGA Blok C No. 21A, RT 01/RW 02, Desa Sukapura',
        ]);

        $response->assertRedirect(route('warga.registrasi.status', ['warga' => $rian->id]));

        $rian->refresh();
        $this->assertEquals(Warga::STATUS_MENUNGGU, $rian->status_verifikasi);
        $this->assertNull($rian->catatan_verifikasi);
        $this->assertNull($rian->diverifikasi_oleh);
        $this->assertNull($rian->diverifikasi_pada);
        Storage::disk('public')->assertExists($rian->foto_identitas);
    }
}
