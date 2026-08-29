<div class="px-6 my-3 flex items-center justify-end gap-3 p-2 bg-[var(--second-pop-up-color)] border table-border rounded-xl shadow-sm"
    x-data="{ localSwitch: @entangle($xString).live }">
    <span class="text-xs sm:text-sm font-medium text-[var(--contrast-main-text)]">
        <template x-if="localSwitch">
            <span>{{ $textTrue }}</span>
        </template>
        <template x-if="!localSwitch">
            <span>{{ $textFalse }}</span>
        </template>
    </span>
    <flux:icon name="check-circle" class="h-4 w-4 transition-colors duration-200"
        ::class="localSwitch ? '{{ $colorCheckTrue ?? $colorTrue }}' : '{{ $colorCheckFalse ??$colorFalse }}'" />

    <button type="button" role="switch" @click="localSwitch = !localSwitch"
        class="relative inline-flex h-[17px] w-[35px] sm:h-[21px] sm:w-[50px] flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none"
        :class="localSwitch ? '{{ $colorSwitchTrue ?? 'bg-[var(--focus-color)]' }}' : '{{ $colorSwitchFalse ?? 'bg-gray-500' }}'">

        <span aria-hidden="true"
            class="pointer-events-none inline-block h-[14px] w-[14px] sm:h-[18px] sm:w-[18px] transform rounded-full bg-white shadow ring-0 transition duration-300 ease-in-out"
            :class="localSwitch ? 'translate-x-[18.5px] sm:translate-x-[29.5px]' : 'translate-x-0'">
        </span>
    </button>

    <flux:icon name="{{ $icon }}" class="h-4 w-4 transition-colors duration-200"
        ::class="localSwitch ? '{{ $colorMainTrue ?? $colorTrue }}' : '{{ $colorMainFalse ??$colorFalse }}'" />
</div>
