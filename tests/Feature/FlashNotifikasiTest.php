<?php

namespace Tests\Feature;

use App\Models\Obat;
use App\Models\Pelanggan;
use App\Models\Pengeluaran;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Menguji setiap aksi CRUD di seluruh controller memicu flash message
 * (success untuk aksi berhasil, error untuk kegagalan yang di-catch).
 *
 * Konvensi pesan:
 *   - Tambah   → "... berhasil ditambahkan!" atau "... berhasil dicatat" / "... berhasil disimpan!"
 *   - Edit     → "... berhasil diperbarui!"
 *   - Hapus    → "... berhasil dihapus!"
 */
class FlashNotifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $owner;
    protected User $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->owner = User::factory()->create(['role' => 'owner']);
        $this->karyawan = User::factory()->create(['role' => 'karyawan']);
    }

    // ===== Supplier CRUD =====

    public function test_flash_saat_tambah_supplier(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.supplier.store'), [
                'nm_supplier' => 'PT Sehat',
                'alamat'      => 'Jakarta',
                'telpon'      => '021123',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil ditambahkan'));
    }

    public function test_flash_saat_edit_supplier(): void
    {
        $sup = Supplier::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('admin.supplier.update', $sup), [
                'nm_supplier' => 'PT Baru',
                'alamat'      => 'Bandung',
                'telpon'      => '022456',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil diperbarui'));
    }

    public function test_flash_saat_hapus_supplier(): void
    {
        $sup = Supplier::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.supplier.destroy', $sup))
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil dihapus'));
    }

    public function test_flash_error_saat_hapus_supplier_yang_masih_dipakai(): void
    {
        $sup = Supplier::factory()->create();
        Obat::factory()->create(['kd_supplier' => $sup->kd_supplier]);

        $this->actingAs($this->admin)
            ->delete(route('admin.supplier.destroy', $sup))
            ->assertSessionHas('error');
    }

    // ===== Pelanggan CRUD =====

    public function test_flash_saat_tambah_pelanggan(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), [
                'nm_pelanggan' => 'Andi',
                'telpon'       => '0812',
                'kota'         => 'Jakarta',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil ditambahkan'));
    }

    public function test_flash_saat_edit_pelanggan(): void
    {
        $p = Pelanggan::factory()->create();
        $this->actingAs($this->admin)
            ->put(route('admin.pelanggan.update', $p), [
                'nm_pelanggan' => 'Andi Update',
                'telpon'       => '0813',
                'kota'         => 'Bogor',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil diperbarui'));
    }

    public function test_flash_saat_hapus_pelanggan(): void
    {
        $p = Pelanggan::factory()->create();
        $this->actingAs($this->admin)
            ->delete(route('admin.pelanggan.destroy', $p))
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil dihapus'));
    }

    // ===== Obat CRUD =====

    public function test_flash_saat_tambah_obat(): void
    {
        Storage::fake('public');
        $sup = Supplier::factory()->create();

        $this->actingAs($this->admin)
            ->post(route('admin.obat.store'), [
                'kd_obat'     => 'OBT999',
                'nm_obat'     => 'Test Obat',
                'jenis'       => 'Tablet',
                'satuan'      => 'Strip',
                'harga_beli'  => 5000,
                'harga_jual'  => 8000,
                'stok'        => 100,
                'kd_supplier' => $sup->kd_supplier,
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil ditambahkan'));
    }

    public function test_flash_saat_edit_obat(): void
    {
        $sup = Supplier::factory()->create();
        $obat = Obat::factory()->create(['kd_supplier' => $sup->kd_supplier]);

        $this->actingAs($this->admin)
            ->put(route('admin.obat.update', $obat), [
                'kd_obat'     => $obat->kd_obat,
                'nm_obat'     => 'Nama Update',
                'jenis'       => 'Kapsul',
                'satuan'      => 'Botol',
                'harga_beli'  => 6000,
                'harga_jual'  => 9000,
                'stok'        => 50,
                'kd_supplier' => $sup->kd_supplier,
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil diperbarui'));
    }

    public function test_flash_saat_hapus_obat(): void
    {
        $sup = Supplier::factory()->create();
        $obat = Obat::factory()->create(['kd_supplier' => $sup->kd_supplier]);

        $this->actingAs($this->admin)
            ->delete(route('admin.obat.destroy', $obat))
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil dihapus'));
    }

    // ===== User Management CRUD =====

    public function test_flash_saat_tambah_user(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.store'), [
                'name'     => 'User Baru',
                'email'    => 'baru@example.com',
                'password' => 'password123',
                'role'     => 'karyawan',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil ditambahkan'));
    }

    public function test_flash_saat_edit_user(): void
    {
        $target = User::factory()->create(['role' => 'karyawan']);

        $this->actingAs($this->admin)
            ->put(route('admin.users.update', $target), [
                'name'  => 'Nama Baru',
                'email' => $target->email,
                'role'  => 'karyawan',
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil diperbarui'));
    }

    public function test_flash_saat_hapus_user(): void
    {
        $target = User::factory()->create(['role' => 'karyawan']);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil dihapus'));
    }

    public function test_flash_error_saat_user_hapus_diri_sendiri(): void
    {
        // Pakai owner karena admin sudah di-guard duluan oleh abort_if(role==admin).
        // Self-delete check hanya tercapai untuk owner/karyawan.
        $this->actingAs($this->owner)
            ->delete(route('admin.users.destroy', $this->owner))
            ->assertSessionHas('error');
    }

    // ===== Pengeluaran CRUD (owner-only) =====

    public function test_flash_saat_tambah_pengeluaran(): void
    {
        $this->actingAs($this->owner)
            ->post(route('owner.pengeluaran.store'), [
                'tanggal'   => '2026-09-17',
                'jenis'     => 'operasional',
                'deskripsi' => 'Listrik',
                'jumlah'    => 500000,
            ])
            ->assertSessionHas('success', function ($msg) {
                $lower = strtolower($msg);
                return str_contains($lower, 'berhasil') && (str_contains($lower, 'dicatat') || str_contains($lower, 'ditambahkan'));
            });
    }

    public function test_flash_saat_edit_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Awal',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->owner)
            ->put(route('owner.pengeluaran.update', $p), [
                'tanggal'   => '2026-09-18',
                'jenis'     => 'operasional',
                'deskripsi' => 'Update',
                'jumlah'    => 200000,
            ])
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil diperbarui'));
    }

    public function test_flash_saat_hapus_pengeluaran(): void
    {
        $p = Pengeluaran::create([
            'tanggal'   => '2026-09-17',
            'jenis'     => 'operasional',
            'deskripsi' => 'Untuk dihapus',
            'jumlah'    => 100000,
            'user_id'   => $this->owner->id,
        ]);

        $this->actingAs($this->owner)
            ->delete(route('owner.pengeluaran.destroy', $p))
            ->assertSessionHas('success', fn ($msg) => str_contains(strtolower($msg), 'berhasil dihapus'));
    }

    // ===== Toast component rendering =====

    public function test_toast_component_muncul_di_response_html_setelah_aksi_berhasil(): void
    {
        // Post kemudian follow redirect, verifikasi toast tampil di halaman berikutnya.
        $response = $this->actingAs($this->admin)
            ->post(route('admin.pelanggan.store'), [
                'nm_pelanggan' => 'Toast Test',
                'telpon'       => '',
                'kota'         => '',
            ])
            ->assertSessionHas('success');

        // Ikuti redirect ke halaman index
        $follow = $this->actingAs($this->admin)
            ->withSession(['success' => 'Data pelanggan "Toast Test" berhasil ditambahkan!'])
            ->get(route('admin.pelanggan.index'));

        $follow->assertStatus(200);
        // Toast pakai pesan literal + kelas warna hijau (success)
        $follow->assertSee('Toast Test');
        $follow->assertSee('bg-green-50 border-green-300', false);
    }

    public function test_toast_component_muncul_dengan_pesan_error(): void
    {
        $response = $this->actingAs($this->admin)
            ->withSession(['error' => 'Terjadi kesalahan pada sistem'])
            ->get(route('admin.pelanggan.index'));

        $response->assertStatus(200);
        $response->assertSee('Terjadi kesalahan pada sistem');
        $response->assertSee('bg-red-50 border-red-300', false);
    }

    public function test_toast_component_tidak_muncul_saat_tidak_ada_flash(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pelanggan.index'));

        $response->assertStatus(200);
        // Container toast tidak dirender kalau tidak ada flash
        $response->assertDontSee('fixed top-4 right-4 z-[100]', false);
    }
}
