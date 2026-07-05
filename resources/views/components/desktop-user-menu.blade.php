@props(['showTeam' => true])

<flux:dropdown position="bottom" align="start">
    <button type="button"
        class="group flex w-full items-center rounded-lg p-1 hover:bg-zinc-800/5 dark:hover:bg-white/10 active:bg-white/20"
        data-test="sidebar-menu-button">
        <flux:avatar :initials="Auth()->user()->initials()" size="sm" />
        <div class="in-data-flux-sidebar-collapsed-desktop:hidden mx-2 grid flex-1 text-start text-sm leading-tight">
            <span
                class="truncate font-medium text-zinc-500 group-hover:text-zinc-800 dark:text-white/80 dark:group-hover:text-white">{{ Auth()->user()->name }}</span>
            @if ($showTeam && Auth()->user()->currentTeam)
                <span
                    class="truncate text-xs text-zinc-400 dark:text-zinc-500">{{ Auth()->user()->currentTeam->name }}</span>
            @endif
        </div>
        <flux:icon name="chevrons-up-down" variant="micro"
            class="in-data-flux-sidebar-collapsed-desktop:hidden ms-auto size-4 text-zinc-400 group-hover:text-zinc-800 dark:text-white/80 dark:group-hover:text-white" />
    </button>

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar :name="Auth()->user()->name" :initials="Auth()->user()->initials()" />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ Auth()->user()->name }}</flux:heading>
                <flux:text class="truncate">{{ Auth()->user()->email }}</flux:text>
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer" data-test="logout-button">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
