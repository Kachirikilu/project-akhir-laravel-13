<div class="flex items-center flex-wrap text-xs text-[var(--contrast-second-text)] gap-y-1">
    -<span class="ml-1 font-bold text-[var(--hover-focus-color)]" x-text="'ID: ' + id"></span>

    @if ($typeX2String ?? null)
        <span class="mx-1.5 opacity-50">|</span>
        <span>{{ $x2HeadString ?? '' }}</span>
        <span x-text="itemsAll[index]?.slot2"></span>
    @endif

    @if ($typeX3String ?? null)
        <span class="mx-1.5 opacity-50">|</span>
        <span>{{ $x3HeadString ?? '' }}</span>
        <span x-text="itemsAll[index]?.slot3"></span>
    @endif

    @if ($typeX4String ?? null)
        <span class="mx-1.5 opacity-50">|</span>
        <span>{{ $x4HeadString ?? '' }}</span>
        <span x-text="itemsAll[index]?.slot4"></span>
    @endif

    @if ($typeX5String ?? null)
        <span class="mx-1.5 opacity-50">|</span>
        <span>{{ $x5HeadString ?? '' }}</span>
        <span x-text="itemsAll[index]?.slot5"></span>
    @endif

    @if ($typeLinkString ?? null)
        <span class="mx-1.5 opacity-50">|</span>
        <template x-if="itemsAll[index]?.link && itemsAll[index]?.link.trim() !== ''">
            <a :href="itemsAll[index]?.link" target="_blank"
                class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 hover:underline active:underline text-xs font-bold">
                <flux:icon.link variant="micro" />
                <span x-text="itemsAll[index]?.link"></span>
            </a>
        </template>

        <template x-if="!itemsAll[index]?.link || itemsAll[index]?.link.trim() === ''">
            <div
                class="flex items-center gap-1 text-gray-400 dark:text-gray-600 text-xs font-medium cursor-not-allowed select-none">
                <flux:icon.link variant="micro" class="opacity-50" />
                <span>Tidak ada tautan</span>
            </div>
        </template>
    @endif
</div>
