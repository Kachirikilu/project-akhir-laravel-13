<div class="flex flex-col gap-2 rounded-[16px] border border-[var(--border-table-color)] bg-[var(--second-table-color)] p-4">

    <div class="flex h-9 w-9 items-center justify-center rounded-lg" style="background: {{ $accentSoft }};">
        <flux:icon name="{{ $icon }}" class="w-[17px] h-[17px]" style="color: {{ $accent }};" />
    </div>

    <div class="flex flex-col gap-0.5">
        <span class="text-[11px] sm:text-xs font-medium text-[var(--contrast-third-text)]">
            {{ $label }}
        </span>

        <div class="flex items-baseline gap-1">
            <span class="my-1 text-md sm:text-lg font-black leading-none text-[var(--contrast-main-text)]">
                {{ $value }}
            </span>
            @if (!empty($unit))
                <span class="text-[10px] sm:text-xs font-semibold text-[var(--contrast-second-text)]">
                    {{ $unit }}
                </span>
            @endif
        </div>

        @if (!empty($sub))
            <span class="text-[10px] sm:text-xs font-semibold mt-0.5" style="color: {{ $accent }};">
                {{ $sub }}
            </span>
        @endif
    </div>
</div>