{{-- Notifikasi toast global untuk pesan flash (success / error / warning / info).
     Muncul di pojok kanan atas, auto-dismiss 4 detik, bisa ditutup manual.
     Di-include sekali di layouts.app supaya semua controller yang set flash langsung tampil. --}}

@php
    $flashes = collect([
        ['type' => 'success', 'msg' => session('success'), 'color' => 'green'],
        ['type' => 'error',   'msg' => session('error'),   'color' => 'red'],
        ['type' => 'warning', 'msg' => session('warning'), 'color' => 'yellow'],
        ['type' => 'info',    'msg' => session('info'),    'color' => 'blue'],
    ])->filter(fn($f) => !empty($f['msg']))->values();
@endphp

@if ($flashes->count() > 0)
    <div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 max-w-sm w-[min(24rem,calc(100vw-2rem))]"
        x-data="{ items: {{ $flashes->toJson() }} }">
        <template x-for="(item, idx) in items" :key="idx">
            <div x-data="{
                    show: false,
                    init() {
                        this.$nextTick(() => this.show = true);
                        setTimeout(() => this.show = false, 4000);
                    }
                 }"
                x-show="show"
                x-transition:enter="transform ease-out duration-300 transition"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="{
                    'bg-green-50 border-green-300': item.type === 'success',
                    'bg-red-50 border-red-300': item.type === 'error',
                    'bg-yellow-50 border-yellow-300': item.type === 'warning',
                    'bg-blue-50 border-blue-300': item.type === 'info',
                }"
                class="pointer-events-auto flex items-start gap-3 p-4 border-2 rounded-lg shadow-lg backdrop-blur-sm"
                role="alert">
                {{-- Icon --}}
                <div :class="{
                        'bg-green-100 text-green-600': item.type === 'success',
                        'bg-red-100 text-red-600': item.type === 'error',
                        'bg-yellow-100 text-yellow-600': item.type === 'warning',
                        'bg-blue-100 text-blue-600': item.type === 'info',
                     }"
                    class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full">
                    <template x-if="item.type === 'success'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="item.type === 'error'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M12 21a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </template>
                    <template x-if="item.type === 'warning'">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </template>
                    <template x-if="item.type === 'info'">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0 pt-1">
                    <p :class="{
                            'text-green-900': item.type === 'success',
                            'text-red-900': item.type === 'error',
                            'text-yellow-900': item.type === 'warning',
                            'text-blue-900': item.type === 'info',
                       }"
                        class="text-sm font-semibold" x-text="item.msg"></p>
                </div>

                {{-- Close button --}}
                <button type="button" @click="show = false"
                    :class="{
                        'text-green-600 hover:text-green-800': item.type === 'success',
                        'text-red-600 hover:text-red-800': item.type === 'error',
                        'text-yellow-600 hover:text-yellow-800': item.type === 'warning',
                        'text-blue-600 hover:text-blue-800': item.type === 'info',
                    }"
                    class="flex-shrink-0" aria-label="Tutup notifikasi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
@endif

{{-- Server-side fallback: kalau Alpine belum ready atau JS disabled, tetap tampil sebagai <noscript>-style banner --}}
@if ($flashes->count() > 0)
    <noscript>
        <div class="fixed top-4 right-4 z-[100] flex flex-col gap-2 max-w-sm">
            @foreach ($flashes as $f)
                <div class="p-3 bg-{{ $f['color'] }}-50 border-2 border-{{ $f['color'] }}-300 rounded-lg text-sm text-{{ $f['color'] }}-900 shadow-lg">
                    <strong>{{ ucfirst($f['type']) }}:</strong> {{ $f['msg'] }}
                </div>
            @endforeach
        </div>
    </noscript>
@endif
