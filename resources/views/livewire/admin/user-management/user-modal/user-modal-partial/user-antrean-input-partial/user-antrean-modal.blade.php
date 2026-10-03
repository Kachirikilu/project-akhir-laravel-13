<flux:modal name="error-detail-modal" class="md:min-w-xl md:max-w-2xl">
    <div class="space-y-4">
<div>
    <div class="flex items-center gap-2">
        <flux:heading size="lg" class="text-red-600 flex items-center gap-2">
            <flux:icon name="exclamation-triangle" class="w-5 h-5 text-red-500" />
            Detail Error Import Excel
        </flux:heading>

        {{-- Badge Jumlah Error --}}
        @if (!empty($selectedRowErrors))
            <flux:badge color="red" size="sm" class="font-semibold">
                {{ count($selectedRowErrors) }} Error
            </flux:badge>
        @endif
    </div>

    <flux:subheading size="sm" class="text-[var(--contrast-second-text)] mt-1">
        @if (!empty($selectedRowErrors))
            Ditemukan <b class="text-red-600 dark:text-red-400">{{ count($selectedRowErrors) }}</b> baris data yang memerlukan perbaikan.
        @else
            Berikut adalah daftar rincian kesalahan saat memproses file Excel.
        @endif
    </flux:subheading>
</div>

<flux:separator />

        {{-- Container List Error --}}
        <div class="max-h-[60vh] overflow-y-auto space-y-3 pr-1 scrollbar-medium">
            @if (!empty($selectedRowErrors))
                @foreach ($selectedRowErrors as $index => $error)
                    <div
                        class="p-3.5 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 rounded-lg text-xs space-y-2.5">

                        {{-- Header Baris: Baris Excel (Kiri) & Email/Nama Bersusun (Samping Kiri/Kanan) --}}
                        <div
                            class="flex items-start justify-between border-b border-red-200/60 dark:border-red-800/40 pb-2">
                            <div class="font-semibold text-red-800 dark:text-red-300 text-sm">
                                @if (is_array($error) && isset($error['baris_excel']))
                                    Baris Excel #{{ $error['baris_excel'] }}
                                @else
                                    Kategori Error: <span class="uppercase">{{ $index }}</span>
                                @endif
                            </div>

                            {{-- Posisi Email & Nama bersusun di samping --}}
                            @if (is_array($error) && (isset($error['email']) || isset($error['name'])))
                                <div
                                    class="text-[11px] text-right text-gray-700 dark:text-gray-300 font-medium leading-tight">
                                    <div><span class="text-gray-500 dark:text-gray-400">Email:</span>
                                        {{ $error['email'] ?? '-' }}</div>
                                    <div><span class="text-gray-500 dark:text-gray-400">Nama:</span>
                                        {{ $error['name'] ?? '-' }}</div>
                                </div>
                            @endif
                        </div>

                        {{-- Body Pesan Error: Field (Bold) Lalu Keterangan Error di Bawahnya --}}
                        <div class="space-y-2 text-red-700 dark:text-red-300">
                            @if (is_array($error))
                                {{-- 1. Format Validasi Per Baris ($error['errors']) --}}
                                @if (isset($error['errors']) && is_array($error['errors']))
                                    @foreach ($error['errors'] as $field => $messages)
                                        @php
                                            $inputValue = $error[$field] ?? ($error['row_data'][$field] ?? null);
                                            $formattedField = str_replace('_', ' ', $field);
                                        @endphp
                                        <div
                                            class="bg-white/60 dark:bg-black/20 p-2 rounded border border-red-100 dark:border-red-900/40">
                                            <div
                                                class="font-bold text-red-900 dark:text-red-200 uppercase tracking-wide text-[10px]">
                                                {{ $formattedField }}:
                                                <br>{{ $inputValue ?? '-' }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-red-800 dark:text-red-300">
                                                @foreach ((array) $messages as $msg)
                                                    <p>{{ $msg }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- 2. Format Error Umum/Sistem ($error['general'] / $error['file']) --}}
                                @elseif (isset($error['general']) || isset($error['file']))
                                    @foreach ($error as $key => $messages)
                                        @if (in_array($key, ['baris_excel', 'email', 'name']))
                                            @continue
                                        @endif
                                        <div
                                            class="bg-white/60 dark:bg-black/20 p-2 rounded border border-red-100 dark:border-red-900/40">
                                            <div
                                                class="font-bold text-red-900 dark:text-red-200 uppercase tracking-wide text-[10px]">
                                                {{ $key }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-red-800 dark:text-red-300">
                                                @foreach ((array) $messages as $msg)
                                                    <p>{{ $msg }}</p>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach

                                    {{-- 3. Fallback Array Lainnya --}}
                                @else
                                    @foreach ($error as $key => $val)
                                        @if (in_array($key, ['baris_excel', 'email', 'name']))
                                            @continue
                                        @endif
                                        <div
                                            class="bg-white/60 dark:bg-black/20 p-2 rounded border border-red-100 dark:border-red-900/40">
                                            <div
                                                class="font-bold text-red-900 dark:text-red-200 uppercase tracking-wide text-[10px]">
                                                {{ $key }}
                                            </div>
                                            <div class="mt-0.5 text-xs text-red-800 dark:text-red-300">
                                                @if (is_array($val))
                                                    @foreach ($val as $subVal)
                                                        <p>{{ $subVal }}</p>
                                                    @endforeach
                                                @else
                                                    <p>{{ $val }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            @else
                                {{-- String Tunggal --}}
                                <div
                                    class="bg-white/60 dark:bg-black/20 p-2 rounded border border-red-100 dark:border-red-900/40">
                                    <div
                                        class="font-bold text-red-900 dark:text-red-200 uppercase tracking-wide text-[10px]">
                                        General Error
                                    </div>
                                    <div class="mt-0.5 text-xs text-red-800 dark:text-red-300">
                                        <p>{{ $error }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                    </div>
                @endforeach
            @else
                <div class="text-center py-6 text-gray-400">
                    <flux:icon name="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-500" />
                    <p class="text-sm font-medium">Tidak ada rincian pesan error yang dapat ditampilkan.</p>
                </div>
            @endif
        </div>

        <flux:separator />

        <div class="flex justify-end">
            <flux:modal.close>
                <flux:button variant="ghost" class="cursor-pointer">Tutup</flux:button>
            </flux:modal.close>

        </div>

    </div>
</flux:modal>
