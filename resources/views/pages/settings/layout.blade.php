<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px] space-y-1">

        @php
            $profileActive = request()->routeIs('profile.edit');
            $themeActive = request()->routeIs('theme.edit');
        @endphp

        <a href="{{ route('profile.edit') }}" wire:navigate @class(['settings-link', 'settings-link-active' => $profileActive])>
            <flux:icon name="user" class="w-4 h-4" />
            <span>Profile</span>
        </a>

        <a href="{{ route('theme.edit') }}" wire:navigate @class(['settings-link', 'settings-link-active' => $themeActive])>
            <flux:icon name="paint-brush" class="w-4 h-4" />
            <span>Theme</span>
        </a>

    </div>
    <flux:separator class="md:hidden" />
    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-xl">
            {{ $slot }}
        </div>
    </div>
</div>
