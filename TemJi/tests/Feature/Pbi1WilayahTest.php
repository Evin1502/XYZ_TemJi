<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Warga;
use App\Models\Wilayah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Pbi1WilayahTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => $role . '@uji.test',
            'role' => $role, 'password' => 'password123',
        ]);
    }

    private function wargaTanpaWilayah(): Warga
    {
        $u = User::create(['name' => 'Warga Uji', 'email' => 'wu@uji.test', 'role' => 'warga', 'password' => 'password123']);

        return Warga::create([
            'user_id' => $u->id, 'nik' => '3273000000009999', 'no_kk' => '3273000000008888',
            'nama_lengkap' => 'Warga Uji', 'no_hp' => '081234567890', 'alamat' => 'Jl. Uji',
            'wilayah_id' => null,
        ]);
    }

    public function test_admin_menambah_dan_mengubah_wilayah_beserta_jumlah_kk(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)
            ->post(route('admin.wilayah.store'), ['nama' => 'Sukapura', 'jumlah_kk' => 300, 'is_aktif' => 1])
            ->assertRedirect(route('admin.wilayah.index'));

        $w = Wilayah::where('nama', 'Sukapura')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.wilayah.update', $w), ['nama' => 'Sukapura', 'jumlah_kk' => 310, 'is_aktif' => 1]);

        $this->assertDatabaseHas('wilayah', ['nama' => 'Sukapura', 'jumlah_kk' => 310]);
        $this->actingAs($admin)->get(route('admin.wilayah.index'))->assertSee('Sukapura')->assertSee('310');
    }

    public function test_nama_wilayah_ganda_ditolak(): void
    {
        $admin = $this->user('admin');
        Wilayah::create(['nama' => 'Sukabirus', 'jumlah_kk' => 266, 'is_aktif' => true]);

        $this->actingAs($admin)
            ->post(route('admin.wilayah.store'), ['nama' => 'Sukabirus', 'jumlah_kk' => 10, 'is_aktif' => 1])
            ->assertSessionHasErrors('nama');
    }

    public function test_admin_melengkapi_wilayah_warga_lalu_hilang_dari_daftar_belum_lengkap(): void
    {
        $admin = $this->user('admin');
        $w = Wilayah::create(['nama' => 'Sukabirus', 'jumlah_kk' => 266, 'is_aktif' => true]);
        $warga = $this->wargaTanpaWilayah();

        $this->actingAs($admin)->get(route('admin.warga.index', ['belum_berwilayah' => 1]))->assertSee('Warga Uji');

        $this->actingAs($admin)->put(route('admin.warga.update', $warga), ['wilayah_id' => $w->id]);

        $this->assertSame($w->id, $warga->fresh()->wilayah_id);
        $this->actingAs($admin)->get(route('admin.warga.index', ['belum_berwilayah' => 1]))->assertDontSee('Warga Uji');
    }

    public function test_non_admin_dan_tamu_tidak_bisa_mengakses(): void
    {
        $this->get(route('admin.wilayah.index'))->assertRedirect(route('login'));
        $this->actingAs($this->user('warga'))->get(route('admin.wilayah.index'))->assertRedirect(route('warga.dashboard'));
    }
}
