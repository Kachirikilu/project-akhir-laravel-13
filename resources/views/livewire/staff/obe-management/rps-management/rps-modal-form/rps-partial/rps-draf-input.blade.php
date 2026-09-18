@php
    $bobotMin = config('rps.bobot_min', 70);
    $bobotMax = config('rps.bobot_max', 200);
@endphp

<div x-data="{
    minBobot: {{ $bobotMin }},
    maxBobot: {{ $bobotMax }}
}"
    x-effect="
        {{-- Syarat: Count < 14 ATAU Count > 16 ATAU Bobot < MIN ATAU Bobot > MAX --}}
        const isInvalid = $store.rps.count_scpmk < 14 || $store.rps.count_scpmk > 16 || $store.rps.total_bobot < minBobot || $store.rps.total_bobot > maxBobot;
        if(isInvalid) { 
            $store.rps.is_draf = 1; 
        }
    ">
    {{-- 1. TEMPLATE KONDISI TERKUNCI (DRAF ONLY) --}}
    <template x-if="$store.rps.count_scpmk < 14 || $store.rps.count_scpmk > 16 || $store.rps.total_bobot < minBobot || $store.rps.total_bobot > maxBobot">
        <div wire:key="status-draf-only">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'rps',
                'nameXString' => 'Status RPS (Terkunci)',
                'modelString' => 'is_draf',
                'xOptions' => ['Draf'],
                'xValues' => [1],
                'iconString' => 'lock-closed',
                'disabled' => true,
                'message' => $errors->first('is_draf'),
            ])

            <div class="mt-2 space-y-1 text-xs sm:text-sm">
                {{-- Pesan Error Sub-CPMK --}}
                <template x-if="$store.rps.count_scpmk < 14">
                    <p class="text-red-500 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="information-circle" variant="mini" class="w-4 h-4" />
                        Sub-CPMK minimal 14 (Saat ini: <span x-text="$store.rps.count_scpmk + ' Sub-CPMK)!'"></span>
                    </p>
                </template>
                <template x-if="$store.rps.count_scpmk > 16">
                    <p class="text-red-500 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="information-circle" variant="mini" class="w-4 h-4" />
                        Sub-CPMK maksimal 16 (Saat ini: <span x-text="$store.rps.count_scpmk + ' Sub-CPMK)!'"></span>
                    </p>
                </template>

                {{-- Pesan Error Bobot Kurang --}}
                <template x-if="$store.rps.total_bobot < minBobot">
                    <p class="text-red-500 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="exclamation-triangle" variant="mini" class="w-4 h-4" />
                        Total bobot kurang (Min <span x-text="minBobot + '%, saat ini: ' + $store.rps.total_bobot + '%)!'"></span>
                    </p>
                </template>

                {{-- Pesan Error Bobot Lebih --}}
                <template x-if="$store.rps.total_bobot > maxBobot">
                    <p class="text-red-500 italic flex items-center gap-1 font-medium">
                        <flux:icon icon="x-circle" variant="mini" class="w-4 h-4" />
                        Total bobot melebihi batas (Max <span x-text="maxBobot + '%, saat ini: ' + $store.rps.total_bobot + '%)!'"></span>
                    </p>
                </template>
            </div>
        </div>
    </template>

    {{-- 2. TEMPLATE KONDISI NORMAL --}}
    <template x-if="$store.rps.count_scpmk >= 14 && $store.rps.count_scpmk <= 16 && $store.rps.total_bobot >= minBobot && $store.rps.total_bobot <= maxBobot">
        <div wire:key="status-normal">
            @include('livewire.global.modal-form.select-form', [
                'alpine' => 'rps',
                'nameXString' => 'Draf / Aktif',
                'modelString' => 'is_draf',
                'xOptions' => ['Draf', 'Aktif'],
                'xValues' => [1, 0],
                'iconString' => 'tag',
                'message' => $errors->first('is_draf'),
            ])
            <p class="text-xs sm:text-sm mt-2 text-green-600 flex items-center gap-1 font-medium">
                <flux:icon icon="check-circle" variant="mini" class="w-4 h-4" />
                Syarat terpenuhi (Bobot: <span x-text="$store.rps.total_bobot + '%).'"></span>
            </p>
        </div>
    </template>
</div>