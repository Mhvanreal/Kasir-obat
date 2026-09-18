<?php

namespace Tests\Feature;

use App\Models\Pengeluaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengeluaranTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;
    protected User $admin;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip CSRF supaya test POST/PUT/DELETE bisa berjalan tanpa token.
        // Auth + role middleware tetap aktif.
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->karyawan = User::factory()->create(['role' => 'karyawan']);
    }

    // ===== Access control =====

    public function test_owner_dapat_mengakses_halaman_pengeluaran(): void
    {
        $this->actingAs($this->owner)
            ->get(route('owner.pengeluaran.index'))
            ->assertStatus(200)
            ->assertViewIs('owner.pengeluaran.index');
    }

    public function test_admin_tidak_dapat_mengakses_halaman_pengeluaran(): void
    {
        $this->actingAs($this->admin)
            ->get(route('owner.pengeluaran.index'))
            ->assertStatus(403);
    }

    public function test_karyawan_tidak_dapat_mengakses_halaman_pengeluaran(): void
    {
        $this->actingAs($this->karyawan)
            ->get(route('owner.pengeluaran.index'))
            ->assertStatus(403);
    }

    public function test_guest_diarahkan_ke_login_saat_akses_pengeluaran(): void
    {
        $this->get(route('owner.pengeluaran.index'))
            ->assertRedirect(route('login'));
    }

    // ===== Store =====

    public function test_owner_dapat_mencatat_pengeluaran_operasional(): void
    {
        $response = $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Tagihan listrik September',
            'jumlah'    => 750000,
            'catatan'   => 'PLN pra-bayar',
        ]);

        $response->assertRedirect(route('owner.pengeluaran.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pengeluarans', [
            'tanggal'     => '2026-09-17',
            'jenis'       => 'operasional',
            'deskripsi'   => 'Tagihan listrik September',
            'jumlah'      => 750000,
            'karyawan_id' => null,
            'user_id'     => $this->owner->id,
        ]);
    }

    public function test_owner_dapat_mencatat_gaji_karyawan(): void
    {
        $response = $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'     => '2026-09-30',
            'jenis'       => 'gaji',
            'karyawan_id' => $this->karyawan->id,
            'deskripsi'   => 'Gaji Kasir September 2026',
            'jumlah'      => 3000000,
        ]);

        $response->assertRedirect(route('owner.pengeluaran.index'));

        $this->assertDatabaseHas('pengeluarans', [
            'jenis'       => 'gaji',
            'karyawan_id' => $this->karyawan->id,
            'jumlah'      => 3000000,
            'user_id'     => $this->owner->id,
        ]);
    }

    public function test_admin_tidak_dapat_mencatat_pengeluaran(): void
    {
        $this->actingAs($this->admin)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Percobaan',
            'jumlah'    => 100000,
        ])->assertStatus(403);

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_karyawan_tidak_dapat_mencatat_pengeluaran(): void
    {
        $this->actingAs($this->karyawan)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Percobaan',
            'jumlah'    => 100000,
        ])->assertStatus(403);

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    // ===== Validation =====

    public function test_validasi_gagal_jika_field_wajib_kosong(): void
    {
        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [])
            ->assertSessionHasErrors(['tanggal', 'jenis', 'deskripsi', 'jumlah']);

        $this->assertDatabaseCount('pengeluarans', 0);
    }

    public function test_validasi_gagal_jika_jumlah_negatif_atau_nol(): void
    {
        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Test negatif',
            'jumlah'    => -100,
        ])->assertSessionHasErrors('jumlah');

        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Test nol',
            'jumlah'    => 0,
        ])->assertSessionHasErrors('jumlah');
    }

    public function test_validasi_gaji_wajib_menyertakan_karyawan(): void
    {
        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'   => '2026-09-17',
            'jenis'     => 'gaji',
            // karyawan_id sengaja tidak diisi
            'deskripsi' => 'Gaji tanpa karyawan',
            'jumlah'    => 3000000,
        ])->assertSessionHasErrors('karyawan_id');
    }

    public function test_karyawan_id_harus_akun_dengan_role_karyawan(): void
    {
        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'     => '2026-09-17',
            'jenis'       => 'gaji',
            'karyawan_id' => $this->owner->id, // ini akun owner, bukan karyawan
            'deskripsi'   => 'Gaji tapi target owner',
            'jumlah'      => 3000000,
        ])->assertSessionHasErrors('karyawan_id');
    }

    public function test_karyawan_id_diabaikan_saat_jenis_operasional(): void
    {
        // Meskipun karyawan_id dikirim, kalau jenis=operasional harus di-null-kan
        $this->actingAs($this->owner)->post(route('owner.pengeluaran.store'), [
            'tanggal'     => '2026-09-17',
            'jenis'       => 'operasional',
            'karyawan_id' => $this->karyawan->id,
            'deskripsi'   => 'Beli ATK',
            'jumlah'      => 50000,
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pengeluarans', [
            'jenis'       => 'operasional',
            'karyawan_id' => null,
        ]);
    }

    // ===== Update =====

    public function test_owner_dapat_mengupdate_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Awal',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->owner)->put(route('owner.pengeluaran.update', $p), [
            'tanggal'   => '2026-09-18',
            'jenis'     => 'operasional',
            'deskripsi' => 'Diperbarui',
            'jumlah'    => 250000,
        ])->assertRedirect(route('owner.pengeluaran.index'));

        $this->assertDatabaseHas('pengeluarans', [
            'id'        => $p->id,
            'tanggal'   => '2026-09-18',
            'deskripsi' => 'Diperbarui',
            'jumlah'    => 250000,
        ]);
    }

    public function test_admin_tidak_dapat_mengupdate_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Asli',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->admin)->put(route('owner.pengeluaran.update', $p), [
            'tanggal'   => '2026-09-18',
            'jenis'     => 'operasional',
            'deskripsi' => 'Hack',
            'jumlah'    => 1,
        ])->assertStatus(403);

        $this->assertDatabaseHas('pengeluarans', [
            'id'        => $p->id,
            'deskripsi' => 'Asli',
        ]);
    }

    // ===== Destroy =====

    public function test_owner_dapat_menghapus_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Akan dihapus',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->owner)->delete(route('owner.pengeluaran.destroy', $p))
            ->assertRedirect(route('owner.pengeluaran.index'));

        $this->assertDatabaseMissing('pengeluarans', ['id' => $p->id]);
    }

    public function test_karyawan_tidak_dapat_menghapus_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Data owner',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->karyawan)->delete(route('owner.pengeluaran.destroy', $p))
            ->assertStatus(403);

        $this->assertDatabaseHas('pengeluarans', ['id' => $p->id]);
    }

    // ===== Filter + KPI =====

    public function test_index_menghitung_kpi_bulan_ini(): void
    {
        // Bulan ini
        Pengeluaran::create([
            'tanggal' => now(), 'jenis' => 'operasional',
            'deskripsi' => 'Listrik', 'jumlah' => 500000, 'user_id' => $this->owner->id,
        ]);
        Pengeluaran::create([
            'tanggal' => now(), 'jenis' => 'gaji', 'karyawan_id' => $this->karyawan->id,
            'deskripsi' => 'Gaji bulan ini', 'jumlah' => 3000000, 'user_id' => $this->owner->id,
        ]);
        // Bulan lalu (harus tidak dihitung)
        Pengeluaran::create([
            'tanggal' => now()->subMonthNoOverflow(), 'jenis' => 'operasional',
            'deskripsi' => 'Bulan lalu', 'jumlah' => 999999, 'user_id' => $this->owner->id,
        ]);

        $this->actingAs($this->owner)->get(route('owner.pengeluaran.index'))
            ->assertStatus(200)
            ->assertViewHas('totalOperasional', function ($v) { return (float) $v === 500000.0; })
            ->assertViewHas('totalGaji', function ($v) { return (float) $v === 3000000.0; })
            ->assertViewHas('totalPengeluaran', function ($v) { return (float) $v === 3500000.0; });
    }

    public function test_index_filter_by_jenis(): void
    {
        Pengeluaran::create([
            'tanggal' => now(), 'jenis' => 'operasional',
            'deskripsi' => 'Op1', 'jumlah' => 100000, 'user_id' => $this->owner->id,
        ]);
        Pengeluaran::create([
            'tanggal' => now(), 'jenis' => 'gaji', 'karyawan_id' => $this->karyawan->id,
            'deskripsi' => 'Gaji1', 'jumlah' => 2000000, 'user_id' => $this->owner->id,
        ]);

        $response = $this->actingAs($this->owner)->get(route('owner.pengeluaran.index', ['jenis' => 'gaji']));
        $response->assertStatus(200);

        $pengeluarans = $response->viewData('pengeluarans');
        $this->assertCount(1, $pengeluarans->items());
        $this->assertSame('gaji', $pengeluarans->first()->jenis);
    }
}
