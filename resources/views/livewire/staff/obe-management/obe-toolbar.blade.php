<div class="flex flex-wrap items-center gap-2 mb-4">
    @if ($typeXString == 'all')
        <h2 class="text-xl sm:text-2xl mr-4 font-bold mb-4 text-[var(--contrast-second-text)]">Manajemen Outcome-Based Education</h2>
    @endif
    @include('livewire.staff.obe-management.obe-toolbar-partial', ['typeXString' => $typeXString])
</div>
