<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed users atau create via factory
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    public function test_admin_can_access_all_role_pages(): void
    {
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/laporan')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/pembelian')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/supplier')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/pelanggan')->assertStatus(200);
        $this->actingAs($admin)->get('/karyawan/obat')->assertStatus(200);
        $this->actingAs($admin)->get('/karyawan/transaksi')->assertStatus(200);
        $this->actingAs($admin)->get('/owner/laporan')->assertStatus(200);
    }

    public function test_owner_can_access_all_role_pages(): void
    {
        $owner = User::where('role', 'owner')->first();

        $this->actingAs($owner)->get('/admin/users')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/laporan')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/pembelian')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/supplier')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/pelanggan')->assertStatus(200);
        $this->actingAs($owner)->get('/karyawan/obat')->assertStatus(200);
        $this->actingAs($owner)->get('/karyawan/transaksi')->assertStatus(200);
    }

    public function test_karyawan_can_access_drug_and_transaction_pages(): void
    {
        $karyawan = User::where('role', 'karyawan')->first();

        $this->actingAs($karyawan)->get('/karyawan/obat')->assertStatus(200);
        $this->actingAs($karyawan)->get('/karyawan/transaksi')->assertStatus(200);
    }

    public function test_karyawan_cannot_access_admin_pages(): void
    {
        $karyawan = User::where('role', 'karyawan')->first();

        $this->actingAs($karyawan)->get('/admin/users')->assertStatus(403);
        $this->actingAs($karyawan)->get('/admin/laporan')->assertStatus(403);
        $this->actingAs($karyawan)->get('/admin/pembelian')->assertStatus(403);
        $this->actingAs($karyawan)->get('/admin/supplier')->assertStatus(403);
        $this->actingAs($karyawan)->get('/admin/pelanggan')->assertStatus(403);
    }

    public function test_karyawan_cannot_access_owner_page(): void
    {
        $karyawan = User::where('role', 'karyawan')->first();

        $this->actingAs($karyawan)->get('/owner/laporan')->assertStatus(403);
    }

    public function test_user_management_store_requires_valid_role(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password123',
                'role' => 'karyawan',
            ])
            ->assertRedirect('/admin/users');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'karyawan',
        ]);
    }

    public function test_user_management_rejects_invalid_role(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        
        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Bad Role',
                'email' => 'bad@example.com',
                'password' => 'password123',
                'role' => 'kasir',
            ])
            ->assertSessionHasErrors('role');
    }

    // ====== Admin role dihilangkan dari UI manajemen pengguna ======

    public function test_user_management_menolak_pembuatan_akun_admin(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $admin = User::where('role', 'admin')->first();

        $this->actingAs($admin)
            ->post('/admin/users', [
                'name' => 'Admin Baru',
                'email' => 'admin.baru@example.com',
                'password' => 'password123',
                'role' => 'admin',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'admin.baru@example.com']);
    }

    public function test_index_pengguna_tidak_menampilkan_user_admin(): void
    {
        $admin = User::where('role', 'admin')->first();

        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);

        $users = $response->viewData('users');
        $roles = $users->pluck('role')->unique()->values()->all();

        $this->assertNotContains('admin', $roles, 'User dengan role admin seharusnya tidak muncul di listing.');
        // Cek roles yang muncul hanya owner + karyawan
        foreach ($roles as $r) {
            $this->assertContains($r, ['owner', 'karyawan']);
        }
    }

    public function test_update_akun_admin_dilarang_lewat_ui(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $admin = User::where('role', 'admin')->first();
        $adminLain = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put('/admin/users/'.$adminLain->id, [
                'name' => 'Nama Baru',
                'email' => 'nama.baru@example.com',
                'role' => 'owner',
            ])
            ->assertStatus(403);
    }

    public function test_destroy_akun_admin_dilarang_lewat_ui(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $admin = User::where('role', 'admin')->first();
        $adminLain = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->delete('/admin/users/'.$adminLain->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('users', ['id' => $adminLain->id]);
    }
}
