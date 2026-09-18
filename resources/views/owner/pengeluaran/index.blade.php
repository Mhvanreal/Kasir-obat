@extends('layouts.app')

@section('title', 'Pengeluaran Apotek')

@php
    $karyawansJson = $karyawans->map(fn($k) => ['id' => $k->id, 'name' => $k->name, 'email' => $k->email])->values()->toJson();
    $errorsJson = json_encode($errors->messages());
    $openCreateInit = ($errors->any() && old('_action') === 'create') ? 'true' : 'false';
    $oldInputJson = json_encode(session()->getOldInput() ?: (object) []);
@endphp

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8"
        x-data='pengeluaranPage({{ $karyawansJson }}, {{ $errorsJson }}, {{ $openCreateInit }}, {{ $oldInputJson }})'>

        <!-- Header -->
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-rose-500 to-orange-500 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pengeluaran Apotek</h1>
                    <p class="text-sm text-gray-600">Kelola pengeluaran operasional dan gaji karyawan</p>
                </div>
            </div>
            <button type="button" @click="openCreate = true; resetForm()"
                class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Catat Pengeluaran
            </button>
        </div>

        {{-- Flash success/error ditampilkan otomatis lewat <x-flash-toast /> di layouts.app --}}

        <!-- KPI -->
        <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-red-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Pengeluaran</p>
                <p class="mt-2 text-2xl font-bold text-red-600">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</p>
                @if ($dari && $sampai)
                    <p class="mt-1 text-xs text-gray-500">{{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}</p>
                @else
                    <p class="mt-1 text-xs text-gray-500">{{ \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y') }}</p>
                @endif
            </div>
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-orange-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Operasional</p>
                <p class="mt-2 text-2xl font-bold text-orange-600">Rp {{ number_format($totalOperasional, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500">Listrik, sewa, dan biaya lain</p>
            </div>
            <div class="p-5 bg-white rounded-lg shadow-sm border-l-4 border-blue-500">
                <p class="text-xs font-semibold text-gray-500 uppercase">Gaji Karyawan</p>
                <p class="mt-2 text-2xl font-bold text-blue-600">Rp {{ number_format($totalGaji, 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500">Total gaji bulan ini</p>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('owner.pengeluaran.index') }}"
            class="p-4 mb-6 bg-white rounded-lg shadow-sm">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="jenis" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua</option>
                        <option value="operasional" @selected($jenis === 'operasional')>Operasional</option>
                        <option value="gaji" @selected($jenis === 'gaji')>Gaji Karyawan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Bulan</label>
                    <select name="bulan" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" @selected($bulan === $m)>{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ $tahun }}" min="2000" max="2100"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Rentang Custom</label>
                    <div class="flex gap-1">
                        <input type="date" name="tanggal_dari" value="{{ $dari }}" class="w-1/2 px-2 py-2 text-xs border border-gray-300 rounded-lg">
                        <input type="date" name="tanggal_sampai" value="{{ $sampai }}" class="w-1/2 px-2 py-2 text-xs border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="flex-1 inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700">
                        Filter
                    </button>
                    <a href="{{ route('owner.pengeluaran.index') }}"
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        <!-- Tabel -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-xs text-gray-600 uppercase bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Jenis</th>
                            <th class="px-4 py-3 text-left">Deskripsi</th>
                            <th class="px-4 py-3 text-left">Karyawan</th>
                            <th class="px-4 py-3 text-right">Jumlah</th>
                            <th class="px-4 py-3 text-left">Dicatat oleh</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pengeluarans as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700 whitespace-nowrap">{{ $p->tanggal->translatedFormat('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    @if ($p->jenis === 'gaji')
                                        <span class="px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Gaji</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs font-medium bg-orange-100 text-orange-800 rounded-full">Operasional</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-900">{{ $p->deskripsi }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $p->karyawan->name ?? '-' }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-red-600 whitespace-nowrap">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $p->pencatat->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        @php
                                            $editPayload = json_encode([
                                                'id' => $p->id,
                                                'tanggal' => $p->tanggal?->format('Y-m-d'),
                                                'jenis' => $p->jenis,
                                                'karyawan_id' => $p->karyawan_id,
                                                'deskripsi' => $p->deskripsi,
                                                'jumlah' => (float) $p->jumlah,
                                                'catatan' => $p->catatan,
                                            ]);
                                        @endphp
                                        <button type="button"
                                            @click="openEdit({{ $editPayload }})"
                                            class="inline-flex items-center px-2 py-1 text-xs font-medium text-teal-700 bg-teal-50 rounded hover:bg-teal-100">
                                            Edit
                                        </button>
                                        <form action="{{ route('owner.pengeluaran.destroy', $p->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus pengeluaran {{ $p->deskripsi }}?')" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada pengeluaran pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($pengeluarans->hasPages())
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $pengeluarans->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Create/Edit -->
        <div x-show="openCreate || openEditFlag" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div @click="closeModal()" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-transition
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form :action="submitAction" method="POST">
                        @csrf
                        <template x-if="openEditFlag">
                            <input type="hidden" name="_method" value="PUT">
                        </template>
                        <input type="hidden" name="_action" :value="openEditFlag ? 'edit' : 'create'">

                        <div class="px-6 py-4 bg-gradient-to-r from-teal-50 to-emerald-50 border-b border-teal-100">
                            <h3 class="text-lg font-bold text-gray-900" x-text="openEditFlag ? 'Edit Pengeluaran' : 'Catat Pengeluaran'"></h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal" x-model="form.tanggal" required
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <p x-show="fieldError('tanggal')" x-text="fieldError('tanggal')" class="mt-1 text-xs text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jenis <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-2">
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                        :class="form.jenis === 'operasional' ? 'border-orange-500 bg-orange-50' : 'border-gray-300 hover:bg-gray-50'">
                                        <input type="radio" name="jenis" value="operasional" x-model="form.jenis" class="mr-2">
                                        <span class="text-sm font-medium">Operasional</span>
                                    </label>
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors"
                                        :class="form.jenis === 'gaji' ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:bg-gray-50'">
                                        <input type="radio" name="jenis" value="gaji" x-model="form.jenis" class="mr-2">
                                        <span class="text-sm font-medium">Gaji Karyawan</span>
                                    </label>
                                </div>
                                <p x-show="fieldError('jenis')" x-text="fieldError('jenis')" class="mt-1 text-xs text-red-600"></p>
                            </div>

                            <div x-show="form.jenis === 'gaji'" x-transition>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Karyawan <span class="text-red-500">*</span></label>
                                <select name="karyawan_id" x-model="form.karyawan_id"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                    <option value="">-- Pilih Karyawan --</option>
                                    <template x-for="k in karyawans" :key="k.id">
                                        <option :value="k.id" x-text="k.name + ' (' + k.email + ')'"></option>
                                    </template>
                                </select>
                                <p x-show="fieldError('karyawan_id')" x-text="fieldError('karyawan_id')" class="mt-1 text-xs text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
                                <input type="text" name="deskripsi" x-model="form.deskripsi" required maxlength="255"
                                    :placeholder="form.jenis === 'gaji' ? 'Contoh: Gaji Kasir September 2026' : 'Contoh: Tagihan listrik September'"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <p x-show="fieldError('deskripsi')" x-text="fieldError('deskripsi')" class="mt-1 text-xs text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="jumlah" x-model="form.jumlah" required min="0.01" step="0.01"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                <p x-show="fieldError('jumlah')" x-text="fieldError('jumlah')" class="mt-1 text-xs text-red-600"></p>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Catatan</label>
                                <textarea name="catatan" x-model="form.catatan" rows="2" maxlength="1000"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500"></textarea>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex justify-end gap-2">
                            <button type="button" @click="closeModal()"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-sm font-semibold text-white rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700">
                                <span x-text="openEditFlag ? 'Simpan Perubahan' : 'Simpan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function pengeluaranPage(karyawans, errors, openCreate, oldInput) {
            const today = new Date().toISOString().slice(0, 10);
            return {
                karyawans,
                serverErrors: errors,
                openCreate,
                openEditFlag: false,
                submitAction: '',
                form: {
                    id: null,
                    tanggal: today,
                    jenis: 'operasional',
                    karyawan_id: '',
                    deskripsi: '',
                    jumlah: '',
                    catatan: '',
                },
                init() {
                    // Re-populate form kalau ada validation error saat create.
                    if (oldInput && typeof oldInput === 'object' && Object.keys(oldInput).length && this.openCreate) {
                        this.form.tanggal = oldInput.tanggal ?? today;
                        this.form.jenis = oldInput.jenis ?? 'operasional';
                        this.form.karyawan_id = oldInput.karyawan_id ?? '';
                        this.form.deskripsi = oldInput.deskripsi ?? '';
                        this.form.jumlah = oldInput.jumlah ?? '';
                        this.form.catatan = oldInput.catatan ?? '';
                    }
                    this.submitAction = '{{ route('owner.pengeluaran.store') }}';
                },
                resetForm() {
                    this.form = { id: null, tanggal: today, jenis: 'operasional', karyawan_id: '', deskripsi: '', jumlah: '', catatan: '' };
                    this.openEditFlag = false;
                    this.submitAction = '{{ route('owner.pengeluaran.store') }}';
                    this.serverErrors = {};
                },
                openEdit(data) {
                    this.form = {
                        id: data.id,
                        tanggal: (data.tanggal || '').slice(0, 10),
                        jenis: data.jenis,
                        karyawan_id: data.karyawan_id ?? '',
                        deskripsi: data.deskripsi,
                        jumlah: data.jumlah,
                        catatan: data.catatan ?? '',
                    };
                    this.openEditFlag = true;
                    this.openCreate = false;
                    this.submitAction = `{{ url('owner/pengeluaran') }}/${data.id}`;
                },
                closeModal() {
                    this.openCreate = false;
                    this.openEditFlag = false;
                },
                fieldError(name) {
                    return this.serverErrors && this.serverErrors[name] ? this.serverErrors[name][0] : null;
                },
            };
        }
    </script>
@endpush
