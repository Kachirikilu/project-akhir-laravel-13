<div class="form-container"
    @php $hasActiveQueue = $antreanImports->contains(fn($i) => in_array($i->status, ['pending', 'processing'])); @endphp
    @if ($hasActiveQueue) wire:poll.3s @endif>

    <div
        class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-0 mx-2 sm:mx-0 mb-6 border-b border-[var(--contrast-second-text)] pb-2">

        <h4
            class="whitespace-nowrap text-[var(--contrast-main-text)] border-[var(--contrast-second-text)] text-sm sm:text-md md:text-lg font-medium">
            List Antrean Data Excel Pengguna
        </h4>


        <div class="flex items-center justify-between xl:justify-end gap-3 w-full xl:w-auto">
            <div></div>
            @if (!empty($antreanImports) && $antreanImports->count() > 0)
                @include('livewire.admin.user-management.user-modal.user-modal-partial.user-antrean-input-partial.delete-antrean-list')
            @endif
        </div>
    </div>

    {{-- Tabel List Antrean --}}
    <div class="overflow-x-auto scrollbar-medium rounded-lg">
        <table class="w-full text-xs text-left  scrollbar-medium">
            {{-- Header Tabel --}}
            <thead
                class="text-xs uppercase bg-[var(--main-color)] text-white border-b border-[var(--border-table-color)] font-semibold">
                <tr>
                    <th class="px-3 py-3">Pengguna Upload</th>
                    <th class="px-3 py-3">File</th>
                    <th class="px-3 py-3">Parameter</th>
                    <th class="px-3 py-3">Status</th>
                    <th class="px-3 py-3">Progress</th>
                    <th class="px-3 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50 pointer-events-none" wire:target="loadingAntreanUsersList"
                class="divide-y divide-[var(--border-table-color)] bg-[var(--second-table-color)]">
                @forelse ($antreanImports as $item)
                    <tr class="hover:bg-[var(--hover-table-color)]/40 active:bg-[var(--hover-table-color)]/32 transition-colors duration-150"
                        wire:key="queue-item-{{ $item->id }}"> {{-- Nama File & Waktu --}}
                        <td class="px-3 py-2 font-medium text-[var(--contrast-main-text)] whitespace-nowrap">
                            <div class="font-semibold">{{ $item->user->name }}</div>
                            <div class="text-[10px] text-[var(--contrast-third-text)]">
                                {{ $item->user->email }}</div>
                        </td>
                        <td class="px-3 py-2 font-medium text-[var(--contrast-main-text)] whitespace-nowrap">
                            <div class="font-semibold">{{ $item->original_name }}</div>
                            <div class="text-[10px] text-[var(--contrast-third-text)]">
                                {{ $item->created_at->diffForHumans() }}</div>
                        </td>

                        {{-- Parameter Input --}}
                        <td class="px-3 py-2 text-[11px] text-[var(--contrast-second-text)]">
                            <span class="block">Role: <b
                                    class="text-[var(--contrast-main-text)]">{{ $item->parameters['role'] ?? 'Default' }}</b></span>
                            <span class="block"><b
                                    class="text-[var(--contrast-main-text)]">{{ !empty($item->parameters['update_or_create_mode']) ? 'Update or Create' : 'Insert Only' }}</b></span>
                        </td>

                        {{-- Status Badge Dinamis berdasarkan Persentase --}}
                        <td class="px-3 py-2 whitespace-nowrap">
                            @if ($item->status === 'pending')
                                <flux:badge color="yellow" size="sm" icon="clock">Menunggu</flux:badge>
                            @elseif ($item->status === 'processing')
                                <flux:badge color="blue" size="sm" class="flex items-center gap-1.5">
                                    <flux:icon name="arrow-path" class="w-3.5 h-3.5 animate-spin" />
                                    Memproses
                                </flux:badge>
                            @else
                                {{-- Hitung Persentase jika status completed / failed --}}
                                @php
                                    $total = $item->total_rows ?? 0;
                                    $success = $item->success_rows ?? 0;
                                    $percentage = $total > 0 ? round(($success / $total) * 100) : 0;
                                @endphp

                                @if ($percentage === 100)
                                    <flux:badge color="green" size="sm" icon="check-circle">
                                        Selesai (100%)
                                    </flux:badge>
                                @elseif ($percentage >= 75)
                                    <flux:badge color="lime" size="sm" icon="exclamation-triangle">
                                        Selesai {{ $percentage }}%
                                    </flux:badge>
                                @elseif ($percentage >= 50)
                                    <flux:badge color="yellow" size="sm" icon="exclamation-triangle">
                                        Selesai {{ $percentage }}%
                                    </flux:badge>
                                @elseif ($percentage > 0)
                                    <flux:badge color="orange" size="sm" icon="exclamation-triangle">
                                        Selesai {{ $percentage }}%
                                    </flux:badge>
                                @else
                                    <flux:badge color="red" size="sm" icon="x-circle">
                                        Gagal
                                    </flux:badge>
                                @endif
                            @endif

                        </td>

                        {{-- Progress Row --}}
                        <td class="px-3 py-2 text-[11px]">
                            @if ($item->total_rows > 0)
                                <span
                                    class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $item->success_rows }}
                                    Sukses</span> /
                                <span class="text-rose-600 dark:text-rose-400 font-semibold">{{ $item->failed_rows }}
                                    Gagal</span>
                                <span class="text-[var(--contrast-third-text)]">(Total: {{ $item->total_rows }})</span>
                            @else
                                <span class="text-[var(--contrast-third-text)] italic">Menyiapkan data...</span>
                            @endif
                        </td>

                        {{-- Aksi: Unduh File & Lihat Log Error --}}
                        <td class="px-3 py-2 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <div x-data="{ isWaiting: false }"
                                    @click="isWaiting = true; setTimeout(() => isWaiting = false, 1500)"
                                    @dblclick="isWaiting = false"
                                    class="inline-block transition-all duration-300 rounded-md">

                                    <flux:button
                                        class="cursor-pointer transition-all duration-300 
                                        text-gray-600 dark:text-gray-400
                                        hover:!bg-amber-100 hover:!text-amber-700 
                                        dark:hover:!bg-amber-950/50 dark:hover:!text-amber-300
                                        active:!bg-amber-200 dark:active:!bg-amber-900/60"
                                        wire:dblclick="downloadFile({{ $item->id }})" size="xs"
                                        variant="subtle" icon="arrow-down-tray"
                                        x-bind:class="isWaiting ?
                                            '!bg-amber-200 !text-amber-800 dark:!bg-amber-900 dark:!text-amber-200 ring-2 ring-amber-400 scale-105' :
                                            ''"
                                        title="Klik 2x untuk unduh berkas">
                                    </flux:button>
                                </div>

                                @if (!empty($item->row_errors))
                                    <flux:modal.trigger name="error-detail-modal">
                                        <flux:button
                                            class="cursor-pointer transition-all duration-200 
                                            text-gray-600 dark:text-gray-400
                                            hover:!bg-red-100 hover:!text-red-600 dark:hover:!bg-red-950/50 dark:hover:!text-red-400 
                                            active:!bg-red-200 dark:active:!bg-red-900/60"
                                            size="xs"
                                            @click="
                                                $flux.modal('error-user-antrean-modal').show();
                                                $dispatch('open-error-antrean-user-modal', { id: {{ $item->id }} });
                                            "
                                            variant="subtle" icon="exclamation-triangle" title="Lihat Detail Error" />
                                    </flux:modal.trigger>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-gray-400">Belum ada antrean proses import.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($antreanImports->hasPages())
            <div class="py-4" id="pagination-links-container">
                {{ $antreanImports->links('vendor.pagination.tailwind', [
                    'typeXLoading' => 'loadingAntreanUsersList()',
                    'isSmall' => 1,
                    'maxButtons' => 8,
                ]) }}
            </div>
        @endif
    </div>
</div>
