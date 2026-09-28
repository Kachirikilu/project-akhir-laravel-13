<flux:header class="lg:hidden">
    {{-- <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" /> --}}

    <button type="button" x-cloak @click.stop="toggleExpanded()"
        class="lg:hidden fixed z-[160] top-4 left-4 p-2.5 sm:p-3 text-white rounded-xl shadow-lg border backdrop-blur-xs bg-[var(--main-color)] border-[var(--border-main-color)] hover:bg-[var(--hover-main-color)] active:scale-90 hover:scale-105 active:shadow-inner group"
        :class="{
            'translate-x-64 rotate-90 bg-[var(--hover-main-color)] shadow-xl transition-all duration-[320ms] ease-out': expanded &&
                !isDesktop,
            'translate-x-0 rotate-0 transition-all duration-[250ms] ease-in-out': !expanded || isDesktop
        }"
        aria-label="Toggle Menu">

        {{-- Ikon Hamburger (Saat Tertutup) --}}
        <flux:icon name="bars-3" variant="outline"
            class="w-6 h-6 sm:w-8 sm:h-8 transition-transform duration-300 ease-out group-hover:rotate-6"
            x-show="!expanded || isDesktop" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50 rotate-[-45deg]"
            x-transition:enter-end="opacity-100 scale-100 rotate-0" />

        {{-- Ikon Silang / Close (Saat Terbuka) --}}
        <flux:icon name="x-mark" variant="outline"
            class="w-6 h-6 sm:w-8 sm:h-8 transition-transform duration-300 ease-out" x-show="expanded && !isDesktop"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-50 rotate-[45deg]"
            x-transition:enter-end="opacity-100 scale-100 rotate-0" />
    </button>
    
    <div x-show="expanded && !isDesktop" x-cloak @click="toggleExpanded()"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 backdrop-blur-none"
        x-transition:enter-end="opacity-100 backdrop-blur-xs" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 backdrop-blur-xs" x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-[150] lg:hidden">
    </div>

    {{-- <flux:spacer />

    <flux:dropdown align="end">

        @if (Auth()->user()->profile_photo_path)
            <flux:profile avatar="{{ Auth()->user()->profile_photo_url }}" icon-trailing="chevron-down" />
        @else
            <flux:profile initials="{{ $userInitials }}" icon-trailing="chevron-down" />
        @endif

        <flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm scrollbar-medium">

            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            @if (Auth()->user()->profile_photo_path)
                                <img src="{{ Auth()->user()->profile_photo_url }}" alt="{{ Auth()->user()->name }}"
                                    class="h-full w-full object-cover">
                            @else
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ $userInitials }}
                                </span>
                            @endif
                        </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ $userName }}</span>
                            <span class="truncate text-xs">{{ $userEmail }}</span>
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
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                    data-test="logout-button">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown> --}}
</flux:header>
