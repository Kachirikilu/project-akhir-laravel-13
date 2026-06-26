<flux:header class="lg:hidden">
    {{-- <flux:sidebar.toggle class="lg:hidden" icon="bars-3" inset="left" /> --}}

    {{-- Tombol Toggle Mobile --}}
    <button type="button" x-cloak @click.stop="toggleExpanded()"
        class="bg-[var(--main-color)] border-[var(--border-main-color)] hover:bg-[var(--hover-main-color)] active:bg-[var(--hover-main-color)]/90 lg:hidden fixed z-90 top-4 left-4 p-2 text-white rounded-lg shadow-md border active:scale-95 transition-all duration-300 ease-out"
        aria-label="Toggle Menu">
        <flux:icon name="bars-3" variant="outline" class="w-7 h-7" />
    </button>

    {{-- Overlay Backdrop --}}
    <div x-show="expanded && !isDesktop" x-cloak @click="toggleExpanded()"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[45] lg:hidden">
    </div>

    {{-- <flux:spacer />

    <flux:dropdown align="end">

        @if (Auth()->user()->profile_photo_path)
            <flux:profile avatar="{{ Auth()->user()->profile_photo_url }}" icon-trailing="chevron-down" />
        @else
            <flux:profile initials="{{ $userInitials }}" icon-trailing="chevron-down" />
        @endif

        <flux:menu class="!bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

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
