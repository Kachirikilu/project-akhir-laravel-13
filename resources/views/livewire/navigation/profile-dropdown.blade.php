<flux:dropdown class="block" align="start">

    {{-- @if (Auth()->user()->profile_photo_path)
        <flux:profile 
            name="{{ Auth()->user()->name }}" 
            avatar="{{ Auth()->user()->profile_photo_url }}" 
            icon:trailing="chevron-up" 
            class="[&_span]:!text-white [&_svg]:!text-white"
        />
    @else
        <flux:profile 
            name="{{ Auth()->user()->name }}" 
            initials="{{ Auth()->user()->initials }}" 
            icon:trailing="chevron-up" 
            class="[&_span]:!text-white [&_svg]:!text-white"
        />
    @endif --}}

    <!-- Trigger -->

    <div x-data="{ open: false }" class="relative">

        <button @click="open = !open" @click.outside="open = false"
            class="cursor-pointer w-full flex items-center gap-4 p-1 rounded-lg hover:bg-white/10 active:bg-white/20 transition">

            <!-- Avatar -->
            @if (Auth()->user()->profile_photo_path)
                <img src="{{ Auth()->user()->profile_photo_url }}" alt="avatar"
                    class="w-8 h-8 rounded-lg object-cover shrink-0">
            @else
                <div
                    class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-xs font-semibold text-white shrink-0">
                    {{ Auth()->user()->initials() }}
                </div>
            @endif

            <!-- Name -->
            <span x-show="expanded" x-cloak x-transition:enter="transition-all duration-300 ease-out"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition-all duration-200 ease-in"
                x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-4"
                class="w-full text-white text-left text-sm whitespace-nowrap overflow-hidden text-ellipsis block">
                {{ Auth()->user()->name }}
            </span>

            <svg class="mr-2 w-5 h-5 text-white transition-all duration-300" :class="!open ? 'rotate-180' : ''"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" x-cloak class="absolute bottom-full mb-2 w-full bg-slate-800 rounded-lg shadow-xl">
        </div>
    </div>


    <flux:menu
        class="w-[224px] !bg-[var(--main-table-color)] !table-border !text-[var(--contrast-main-text)]">
        <flux:menu.radio.group>
            <div class="p-0 text-sm font-normal">
                <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                    <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                        @if (Auth()->user()->profile_photo_path)
                            <img src="{{ Auth()->user()->profile_photo_url }}" alt="{{ Auth()->user()->name }}"
                                class="h-full w-full object-cover">
                        @else
                            <span
                                class="flex h-full w-full items-center justify-center rounded-lg 
                                bg-[var(--main-color)] text-[var(--main-text)]
                                border border-neutral-300 dark:border-neutral-700">
                                {{ Auth()->user()->initials() }}
                            </span>
                        @endif
                    </span>

                    <div class="grid flex-1 text-start text-sm leading-tight">
                        <span class="truncate font-semibold text-[var(--contrast-main-text)]">{{ Auth()->user()->name }}</span>
                        <span class="truncate text-xs text-[var(--contrast-second-text)]">{{ Auth()->user()->email }}</span>
                    </div>
                </div>
            </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
            </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full cursor-pointer"
                data-test="logout-button">
                {{ __('Log Out') }}
            </flux:menu.item>
        </form>
    </flux:menu>

</flux:dropdown>
