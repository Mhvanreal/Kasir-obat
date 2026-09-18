<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip CSRF saja. Auth, VerifyRole, dan ShareErrorsFromSession harus
        // tetap aktif supaya test guest_tidak_dapat_akses_* dan login page ($errors)
        // berperilaku benar.
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function login_page_dapat_diakses()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_dapat_login_dengan_kredensial_yang_benar()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function user_tidak_dapat_login_dengan_password_salah()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function user_dapat_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        // Logout diarahkan langsung ke halaman login, bukan ke landing bawaan Laravel.
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_dapat_mengakses_halaman_admin()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.obat.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_admin()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get(route('admin.obat.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function karyawan_tidak_dapat_mengakses_halaman_admin()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)->get(route('admin.pembelian'));

        $response->assertStatus(403);
    }

    /** @test */
    public function karyawan_dapat_mengakses_halaman_transaksi()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)->get(route('karyawan.transaksi'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_dapat_mengakses_halaman_transaksi()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('karyawan.transaksi'));

        $response->assertStatus(200);
    }

    /** @test */
    public function owner_dapat_mengakses_halaman_transaksi()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get(route('karyawan.transaksi'));

        $response->assertStatus(200);
    }

    /** @test */
    public function hanya_admin_dan_owner_dapat_akses_user_management()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)->get(route('admin.users.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_dapat_akses_user_management()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function hanya_admin_dan_owner_dapat_akses_pembelian()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)->get(route('admin.pembelian'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_dapat_akses_pembelian()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.pembelian'));

        $response->assertStatus(200);
    }

    /** @test */
    public function owner_dapat_akses_pembelian()
    {
        $owner = User::factory()->create(['role' => 'owner']);

        $response = $this->actingAs($owner)->get(route('admin.pembelian'));

        $response->assertStatus(200);
    }

    /** @test */
    public function hanya_admin_dan_owner_dapat_akses_pengaturan()
    {
        $karyawan = User::factory()->create(['role' => 'karyawan']);

        $response = $this->actingAs($karyawan)->get(route('admin.pengaturan.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_dapat_akses_pengaturan()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.pengaturan.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function semua_role_dapat_akses_riwayat_transaksi()
    {
        $roles = ['admin', 'owner', 'karyawan'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);

            $response = $this->actingAs($user)->get(route('karyawan.riwayat-transaksi.index'));

            $response->assertStatus(200);
        }
    }

    /** @test */
    public function guest_tidak_dapat_akses_dashboard()
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function root_url_mengarahkan_guest_ke_login()
    {
        // Sebelumnya root menampilkan welcome bawaan Laravel. Sekarang selalu redirect.
        $this->get('/')->assertRedirect(route('login'));
    }

    /** @test */
    public function root_url_mengarahkan_user_login_ke_dashboard()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/')->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function guest_tidak_dapat_akses_halaman_admin()
    {
        $response = $this->get(route('admin.obat.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_tidak_dapat_akses_halaman_transaksi()
    {
        $response = $this->get(route('karyawan.transaksi'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_dapat_akses_profile()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_dapat_update_profile()
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'New Name',
            'email' => 'new@example.com',
        ]);

        $response->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new@example.com', $user->email);
    }

    /** @test */
    public function authenticated_user_dapat_delete_account()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function user_tidak_dapat_delete_account_dengan_password_salah()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ]);

        // ProfileController::destroy pakai validateWithBag('userDeletion', ...),
        // jadi error masuk ke bag "userDeletion", bukan default bag.
        $response->assertSessionHasErrors(['password'], null, 'userDeletion');
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function login_dengan_remember_me()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'remember' => true,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard'));
    }

    /** @test */
    public function throttling_login_setelah_banyak_attempt_gagal()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Simulate 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // Next attempt should be throttled
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
