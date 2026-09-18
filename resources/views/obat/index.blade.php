@extends('layouts.app')

@section('title', 'Kelola Obat')

@section('content')
    <div class="px-4 py-6 mx-auto max-w-8xl sm:px-6 lg:px-8" x-data="{
        showCreateModal: false,
        showEditModal: false,
        editObat: {},
        openEditModal(obat) {
            this.editObat = obat;
            this.showEditModal = true;
        }
    }">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div
                        class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-500 shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Kelola Obat</h1>
                        <p class="text-sm text-gray-600">Manajemen data dan stok obat</p>
                    </div>
                </div>
                <button @click="showCreateModal = true"
                    class="inline-flex items-center px-6 py-3 space-x-2 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Obat Baru</span>
                </button>
            </div>
        </div>

        <!-- Notifikasi Stok Perhatian -->
        @php
            $obatHabis = $obats->filter(fn($o) => $o->isStokHabis());
            $obatKritis = $obats->filter(fn($o) => $o->isStokKritis());
            $obatRendah = $obats->filter(fn($o) => $o->isStokRendah());
            $totalPerhatian = $obatHabis->count() + $obatKritis->count() + $obatRendah->count();
        @endphp
        @if ($totalPerhatian > 0)
            <div class="mb-6 overflow-hidden rounded-lg shadow-sm border-2
                {{ $obatHabis->count() || $obatKritis->count() ? 'bg-red-50 border-red-300' : 'bg-yellow-50 border-yellow-300' }}"
                x-data="{ show: true }" x-show="show" x-transition>
                <div class="px-4 py-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 flex-1">
                            <svg class="w-6 h-6 flex-shrink-0 mt-0.5
                                {{ $obatHabis->count() || $obatKritis->count() ? 'text-red-600 animate-pulse' : 'text-yellow-600' }}"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold {{ $obatHabis->count() || $obatKritis->count() ? 'text-red-900' : 'text-yellow-900' }}">
                                    Perhatian Stok: {{ $totalPerhatian }} obat membutuhkan tindakan
                                </p>
                                <div class="mt-1.5 flex flex-wrap gap-x-4 gap-y-1 text-xs">
                                    @if ($obatHabis->count())
                                        <span class="text-red-800">
                                            <span class="inline-block w-2 h-2 bg-red-600 rounded-full mr-1"></span>
                                            <strong>{{ $obatHabis->count() }}</strong> habis
                                        </span>
                                    @endif
                                    @if ($obatKritis->count())
                                        <span class="text-red-800">
                                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                            <strong>{{ $obatKritis->count() }}</strong> kritis (&lt; {{ \App\Models\Obat::AMBANG_KRITIS }})
                                        </span>
                                    @endif
                                    @if ($obatRendah->count())
                                        <span class="text-yellow-800">
                                            <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-1"></span>
                                            <strong>{{ $obatRendah->count() }}</strong> rendah ({{ \App\Models\Obat::AMBANG_KRITIS }}&ndash;{{ \App\Models\Obat::AMBANG_RENDAH }})
                                        </span>
                                    @endif
                                </div>
                                @if ($obatHabis->count() || $obatKritis->count())
                                    <p class="mt-1.5 text-xs text-red-700">
                                        Segera lakukan restok melalui menu Pembelian.
                                    </p>
                                @endif
                            </div>
                        </div>
                        <button @click="show = false" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Flash success/error ditampilkan otomatis lewat <x-flash-toast /> di layouts.app --}}

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Obat</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $obats->count() }}</p>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Tersedia</p>
                            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $obats->sum('stok') }}</p>
                        </div>
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden bg-white rounded-lg shadow-md">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Stok Perlu Perhatian</p>
                            <p class="mt-2 text-3xl font-bold {{ $totalPerhatian > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $totalPerhatian }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">Habis + kritis + rendah</p>
                        </div>
                        <div class="p-3 bg-red-100 rounded-lg">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="overflow-hidden bg-white rounded-lg shadow-md">
            <!-- Search and Filter -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="relative flex-1 max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchInput"
                            class="block w-full py-2 pl-10 pr-3 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                            placeholder="Cari obat...">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full divide-y divide-gray-200" id="obatTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                No
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Kode Obat
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Nama Obat
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Jenis
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Satuan
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Harga Jual
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Stok
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">
                                Supplier
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-xs font-medium tracking-wider text-center text-gray-500 uppercase">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($obats as $index => $obat)
                            <tr class="transition-colors
                                @if ($obat->isStokHabis()) bg-red-50 hover:bg-red-100 border-l-4 border-red-600
                                @elseif ($obat->isStokKritis()) bg-red-50/50 hover:bg-red-50 border-l-4 border-red-500
                                @elseif ($obat->isStokRendah()) bg-yellow-50/60 hover:bg-yellow-50 border-l-4 border-yellow-400
                                @else hover:bg-gray-50 @endif">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-semibold text-teal-800 bg-teal-100 rounded-full">
                                        {{ $obat->kd_obat }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if ($obat->gambar)
                                            <img src="{{ asset('storage/'.$obat->gambar) }}"
                                                class="object-cover w-10 h-10 mr-3 rounded-lg">
                                        @else
                                            <img src="{{ asset('images/no-image.svg') }}"
                                                class="object-cover w-10 h-10 mr-3 bg-gray-100 rounded-lg">
                                        @endif
                                        <span class="text-sm font-medium text-gray-900">{{ $obat->nm_obat }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $obat->jenis }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $obat->satuan }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 whitespace-nowrap">
                                    Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-stok-badge :obat="$obat" />
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    {{ $obat->supplier->nm_supplier ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('karyawan.obat.show', $obat->kd_obat) }}"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-teal-700 transition-colors bg-teal-100 rounded-lg hover:bg-teal-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Detail
                                        </a>
                                        <button @click="openEditModal({{ json_encode($obat) }})"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-700 transition-colors bg-blue-100 rounded-lg hover:bg-blue-200">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <form action="{{ route('karyawan.obat.destroy', $obat->kd_obat) }}"
                                            method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus obat ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-700 transition-colors bg-red-100 rounded-lg hover:bg-red-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="mt-4 text-lg font-medium text-gray-900">Belum ada data obat</p>
                                        <p class="mt-1 text-sm text-gray-500">Mulai dengan menambahkan obat baru</p>
                                        <button @click="showCreateModal = true"
                                            class="inline-flex items-center px-4 py-2 mt-4 text-sm font-medium text-white transition-colors rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Tambah Obat
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCreateModal" @click="showCreateModal = false"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showCreateModal" x-transition
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form action="{{ route('karyawan.obat.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="px-8 py-6 bg-gradient-to-r from-teal-50 to-emerald-50">
                            <div class="flex items-center justify-between">
                                <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                                    Tambah Obat Baru
                                </h3>
                                <button type="button" @click="showCreateModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-8 py-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Kode Obat -->
                                <div>
                                    <label for="create_kd_obat" class="block text-sm font-medium text-gray-700">
                                        Kode Obat <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="kd_obat" id="create_kd_obat" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="Contoh: OBT001">
                                </div>

                                <!-- Nama Obat -->
                                <div>
                                    <label for="create_nm_obat" class="block text-sm font-medium text-gray-700">
                                        Nama Obat <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nm_obat" id="create_nm_obat" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="Contoh: Paracetamol">
                                </div>

                                <!-- Jenis -->
                                <div>
                                    <label for="create_jenis" class="block text-sm font-medium text-gray-700">
                                        Jenis Obat <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis" id="create_jenis" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Kapsul">Kapsul</option>
                                        <option value="Sirup">Sirup</option>
                                        <option value="Salep">Salep</option>
                                        <option value="Injeksi">Injeksi</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Satuan -->
                                <div>
                                    <label for="create_satuan" class="block text-sm font-medium text-gray-700">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <select name="satuan" id="create_satuan" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Satuan --</option>
                                        <option value="Strip">Strip</option>
                                        <option value="Box">Box</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Tube">Tube</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Pack">Pack</option>
                                    </select>
                                </div>

                                <!-- Harga Beli -->
                                <div>
                                    <label for="create_harga_beli" class="block text-sm font-medium text-gray-700">
                                        Harga Beli <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_beli" id="create_harga_beli" min="0"
                                            step="0.01" required
                                            class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>

                                <!-- Harga Jual -->
                                <div>
                                    <label for="create_harga_jual" class="block text-sm font-medium text-gray-700">
                                        Harga Jual <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_jual" id="create_harga_jual" min="0"
                                            step="0.01" required
                                            class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                            placeholder="0">
                                    </div>
                                </div>

                                <!-- Stok -->
                                <div>
                                    <label for="create_stok" class="block text-sm font-medium text-gray-700">
                                        Stok Awal <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stok" id="create_stok" value="0" min="0"
                                        required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                        placeholder="0">
                                </div>

                                <!-- Gambar -->
                                <div class="md:col-span-2">
                                    <label for="create_gambar" class="block text-sm font-medium text-gray-700">
                                        Gambar Obat
                                    </label>
                                    <input type="file" name="gambar" id="create_gambar" accept="image/jpeg,image/png,image/jpg"
                                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                    <p class="mt-1 text-xs text-gray-500">Format: jpeg, png, jpg · Maksimal 2 MB</p>
                                </div>

                                <!-- Supplier -->
                                <div>
                                    <label for="create_kd_supplier" class="block text-sm font-medium text-gray-700">
                                        Supplier <span class="text-red-500">*</span>
                                    </label>
                                    <select name="kd_supplier" id="create_kd_supplier" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->kd_supplier }}">{{ $supplier->nm_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-4 bg-gray-50 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="inline-flex justify-center w-full px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl sm:ml-3 sm:w-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Data
                            </button>
                            <button type="button" @click="showCreateModal = false"
                                class="inline-flex justify-center w-full px-6 py-2.5 mt-3 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" @click="showEditModal = false"
                    class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditModal" x-transition
                    class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <form :action="`{{ route('karyawan.obat.index') }}/${editObat.kd_obat}`" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="px-8 py-6 bg-gradient-to-r from-teal-50 to-emerald-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-900" id="modal-title">
                                        Edit Obat
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600" x-text="editObat.nm_obat"></p>
                                </div>
                                <button type="button" @click="showEditModal = false"
                                    class="text-gray-400 hover:text-gray-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="px-8 py-6">
                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <!-- Kode Obat (Read Only) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Kode Obat
                                    </label>
                                    <input type="text" :value="editObat.kd_obat" readonly
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                                    <p class="mt-1 text-xs text-gray-500">Kode obat tidak dapat diubah</p>
                                </div>

                                <!-- Nama Obat -->
                                <div>
                                    <label for="edit_nm_obat" class="block text-sm font-medium text-gray-700">
                                        Nama Obat <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nm_obat" id="edit_nm_obat" :value="editObat.nm_obat"
                                        required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                </div>

                                <!-- Jenis -->
                                <div>
                                    <label for="edit_jenis" class="block text-sm font-medium text-gray-700">
                                        Jenis Obat <span class="text-red-500">*</span>
                                    </label>
                                    <select name="jenis" id="edit_jenis" x-model="editObat.jenis" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Jenis --</option>
                                        <option value="Tablet">Tablet</option>
                                        <option value="Kapsul">Kapsul</option>
                                        <option value="Sirup">Sirup</option>
                                        <option value="Salep">Salep</option>
                                        <option value="Injeksi">Injeksi</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>

                                <!-- Satuan -->
                                <div>
                                    <label for="edit_satuan" class="block text-sm font-medium text-gray-700">
                                        Satuan <span class="text-red-500">*</span>
                                    </label>
                                    <select name="satuan" id="edit_satuan" x-model="editObat.satuan" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Satuan --</option>
                                        <option value="Strip">Strip</option>
                                        <option value="Box">Box</option>
                                        <option value="Botol">Botol</option>
                                        <option value="Tube">Tube</option>
                                        <option value="Pcs">Pcs</option>
                                        <option value="Pack">Pack</option>
                                    </select>
                                </div>

                                <!-- Harga Beli -->
                                <div>
                                    <label for="edit_harga_beli" class="block text-sm font-medium text-gray-700">
                                        Harga Beli <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_beli" id="edit_harga_beli"
                                            :value="editObat.harga_beli" min="0" step="0.01" required
                                            class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Harga Jual -->
                                <div>
                                    <label for="edit_harga_jual" class="block text-sm font-medium text-gray-700">
                                        Harga Jual <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                            <span class="text-gray-500">Rp</span>
                                        </div>
                                        <input type="number" name="harga_jual" id="edit_harga_jual"
                                            :value="editObat.harga_jual" min="0" step="0.01" required
                                            class="block w-full pl-12 pr-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                    </div>
                                </div>

                                <!-- Stok -->
                                <div>
                                    <label for="edit_stok" class="block text-sm font-medium text-gray-700">
                                        Stok <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stok" id="edit_stok" :value="editObat.stok"
                                        min="0" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                </div>

                                <!-- Gambar -->
                                <div class="md:col-span-2">
                                    <label for="edit_gambar" class="block text-sm font-medium text-gray-700">
                                        Gambar Obat
                                    </label>
                                    <div class="flex items-center mt-1 space-x-3">
                                        <img :src="editObat.gambar ? '/storage/' + editObat.gambar : '/images/no-image.svg'"
                                            class="object-cover w-14 h-14 bg-gray-100 rounded-lg">
                                        <input type="file" name="gambar" id="edit_gambar"
                                            accept="image/jpeg,image/png,image/jpg"
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak mengubah gambar · Format: jpeg, png, jpg · Maksimal 2 MB</p>
                                </div>

                                <!-- Supplier -->
                                <div>
                                    <label for="edit_kd_supplier" class="block text-sm font-medium text-gray-700">
                                        Supplier <span class="text-red-500">*</span>
                                    </label>
                                    <select name="kd_supplier" id="edit_kd_supplier" x-model="editObat.kd_supplier"
                                        required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->kd_supplier }}">{{ $supplier->nm_supplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-8 py-4 bg-gray-50 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="inline-flex justify-center w-full px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 rounded-lg bg-gradient-to-r from-teal-600 to-emerald-600 hover:from-teal-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 shadow-lg hover:shadow-xl sm:ml-3 sm:w-auto">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Update Data
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="inline-flex justify-center w-full px-6 py-2.5 mt-3 text-sm font-medium text-gray-700 transition-colors bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 sm:mt-0 sm:w-auto">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Simple search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#obatTable tbody tr');

            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    </script>
@endpush
