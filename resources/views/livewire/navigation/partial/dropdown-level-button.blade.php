@props(['subMenus', 'title' => 'Management', 'triggerRef' => 'dropdownTrigger', 'isActive' => false, 'isSubMenu' => []])

<flux:dropdown position="right" align="start">
    <button x-ref="{{ $triggerRef }}" type="button" tabindex="-1"
        class="absolute inset-0 opacity-0 pointer-events-none">
    </button>
    <flux:menu 
        x-data="{ 
            currentTable: (new URL(window.location.href)).searchParams.get('switchTable') || '{{ request()->route('switchTable') ?? 'rps' }}'
        }"
        x-on:table-switched.window="currentTable = $event.detail.switchTable === '' ? 'rps' : $event.detail.switchTable"
        x-on:livewire:navigated.window="currentTable = (new URL(window.location.href)).searchParams.get('switchTable') || '{{ request()->route('switchTable') ?? 'rps' }}'"
        class="min-w-48 !bg-[var(--second-pop-up-color)] !border-[var(--border-table-color)] !text-[var(--contrast-main-text)]">

        <flux:menu.heading>{{ $title }}</flux:menu.heading>

        <flux:separator class="my-1" />

        @foreach ($subMenus as $sub)
            @php
                $param = $sub['param'] ?? '';
                $isOBEMenu = in_array($param, $isSubMenu);
                $isRouteOBE = $isActive;
                $isBtnActive = $sub['active'] ?? false;
                $isBtnActiveSub = isset($sub['active-sub']) && $sub['active-sub'];
            @endphp

            <flux:menu.item :href="$sub['url']" wire:navigate
                class="group overflow-hidden rounded-md cursor-pointer !shadow-none !border-none hover:!bg-transparent focus:!bg-transparent active:!bg-transparent">
                
                <span :class="(
                        ({{ $isOBEMenu ? 'true' : 'false' }} && {{ $isRouteOBE ? 'true' : 'false' }} && currentTable === '{{ $param }}') ||
                        (!{{ $isOBEMenu ? 'true' : 'false' }} && {{ $isBtnActive ? 'true' : 'false' }})
                    )
                    ? 'bg-[var(--main-table-color)] dark:bg-white/10 text-[var(--contrast-main-text)] font-semibold border-l-2 border-[var(--border-main-color)] shadow-sm' 
                    : (
                        {{ $isBtnActiveSub ? 'true' : 'false' }}
                        ? 'border-l-2 border-[var(--border-main-color)] text-[var(--contrast-main-text)] bg-[var(--main-pop-up-color)]/30 dark:bg-white/5' 
                        : 'border-l-2 border-transparent text-[var(--contrast-main-text)] group-hover:text-[var(--contrast-third-text)] group-hover:bg-[var(--main-pop-up-color)] dark:group-hover:bg-white/5'
                    )"
                    style="margin-left: {{ ($sub['level'] ?? false) == 1 ? 18 : (($sub['level'] ?? false) == 2 ? 48 : '') }}px;"
                    class="pr-7 flex items-center rounded-md w-full h-full text-xs px-3 py-1.5 transition-all duration-300 ease-in-out min-w-0">

                    <flux:icon :name="$sub['icon']" class="{{ $sub['color'] ?? '' }} mr-2 h-4 w-4 shrink-0" />

                    <span class="truncate block flex-1 text-left">
                        {{ $sub['label'] }}
                    </span>
                </span>
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>