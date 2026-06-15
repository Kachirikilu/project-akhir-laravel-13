@if (Auth::user()?->admin || Auth::user()?->dosen)
    <flux:menu
        class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)]">

        @php
            $isTrashed = $x->trashed();
            if ($typeXString == 'sub-cpmk') {
                $typeUpper = 'SCPMK';
            } elseif ($typeXString == 'referensi') {
                $typeUpper = 'Ref';
            } else {
                $typeUpper = strtoupper($typeXString);
            }
            $showCall = "show{$typeUpper}($x->id)";
            $editCall = "edit{$typeUpper}($x->id)";
            $deleteCall = "delete{$typeUpper}($x->id, " . ($isTrashed ? 'true' : 'false') . ')';
            $restoreCall = "restore{$typeUpper}($x->id)";

            $typeX2String = $typeXString;
            if ($typeX2String == 'sub-cpmk') {
                $typeX2String = 'Sub-CPMK';
            } elseif ($typeX2String == 'referensi') {
                $typeX2String = 'Referensi';
            }
        @endphp

        @include('livewire.global.table.text-copy', [
            'xType' => $copyText ?? $x->kode,
            'typeXString' => $copyName ?? 'Kode ' . $typeX2String,
        ])

        <flux:menu.separator />

        @if ($typeXString == 'rps')
            @include('livewire.staff.obe-management.rps-management.rps-toolbar-table')
        @elseif ($typeXString == 'cpmk')
            @include('livewire.staff.obe-management.cpmk-management.cpmk-toolbar-table')
        @elseif ($typeXString == 'sub-cpmk')
            @include('livewire.staff.obe-management.scpmk-management.scpmk-toolbar-table')
        @elseif ($typeXString == 'cpl')
            @include('livewire.staff.obe-management.cpl-management.cpl-toolbar-table')
        @elseif ($typeXString == 'referensi')
            @include('livewire.staff.obe-management.ref-management.ref-toolbar-table')
        @endif

    </flux:menu>
@endif
