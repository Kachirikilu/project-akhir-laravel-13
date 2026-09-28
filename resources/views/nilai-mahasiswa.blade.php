@php
    $pageTitle = __('Manajemen Periode Nilai Saya');
    
    if (request()->routeIs('rps-mahasiswa')) {
        $pageTitle = __('Manajemen Nilai Saya');
    }
@endphp


<x-layouts::app :title="$pageTitle">
    <div class="flex h-full max-w-[4600px] flex-1 flex-col rounded-xl">
        <div class="relative h-full flex-1 mb-96 rounded-xl sm:border-2 sm:border-[var(--border-wadah-color)]">
            @if(request()->routeIs('nilai-mahasiswa'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management :isNilaiMhs="true" :nim="Auth::user()->mahasiswa->nim" />
            @elseif (request()->routeIs('rps-mahasiswa'))
                <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management :isNilaiMhs="true" :nim="Auth::user()->mahasiswa->nim" :ganjil_genap="request()->route('ganjil_genap')" :akademik="request()->route('akademik')" />

            @endif
        </div>
    </div>
    @if (request()->routeIs('rps-mahasiswa'))
        <livewire:staff.nilai-management.nilai-mahasiswa-management.rps-mahasiswa-management.modal-rps-mahasiswa-management />
        <livewire:staff.obe-management.rps-management.show-rps-management />
    @endif
</x-layouts::app>