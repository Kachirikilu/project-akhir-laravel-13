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
        ::class="localSwitch ? '{{ $colorCheckTrue ?? $colorTrue }}' : '{{ $colorCheckFalse ?? $colorFalse }}'" />

    <!-- 1. Diberi p-0.5 (padding) & items-center agar bola berada di tengah vertikal secara otomatis -->
    <button type="button" role="switch" @click="localSwitch = !localSwitch"
        class="relative inline-flex h-5 w-9 sm:h-6 sm:w-11 p-0.5 items-center flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-300 ease-in-out focus:outline-none"
        :class="localSwitch ? '{{ $colorSwitchTrue ?? 'bg-[var(--focus-color)]' }}' : '{{ $colorSwitchFalse ?? 'bg-gray-500' }}'">

        <!-- 2. Ukuran bola h-full aspect-square menyesuaikan padding tombol. Geser menggunakan translate-x-full (100%) -->
        <span aria-hidden="true"
            class="pointer-events-none inline-block h-full aspect-square transform rounded-full bg-white shadow ring-0 transition duration-300 ease-in-out"
            :class="localSwitch ? 'translate-x-[calc(100%+0.25rem)]' : 'translate-x-0'">
        </span>
    </button>

    <flux:icon name="{{ $icon }}" class="h-4 w-4 transition-colors duration-200"
        ::class="localSwitch ? '{{ $colorMainTrue ?? $colorTrue }}' : '{{ $colorMainFalse ?? $colorFalse }}'" />
</div>