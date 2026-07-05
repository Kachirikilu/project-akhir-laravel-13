<?php

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;

new #[Title('Profile Settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    #[Rule(['nullable', 'image', 'max:1024'])]
    public $photo;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $user = Auth::user();
        $this->email = $user->email;
        
        $roleModel = $user->admin ?: ($user->dosen ?: $user->mahasiswa);
        $this->name = $roleModel ? $roleModel->name : $user->name;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        $validated = $this->validate($this->profileRules($user->id));

        $user->email = $validated['email'];
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        $roleModel = $user->admin ?: ($user->dosen ?: $user->mahasiswa);

        if ($roleModel) {
            if ($roleModel->name !== $validated['name']) {
                $roleModel->name = $validated['name'];
                $roleModel->save();
            }
        } else {
            $user->name = $validated['name'];
            $user->save();
        }

        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->forceFill(['profile_photo_path' => $path])->save();
            $this->photo = null;
        }

        Flux::toast(variant: 'success', text: __('Profile updated!'));
        
        $this->dispatch('profile-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function deletePhoto(): void
    {
        $user = Auth::user();
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }
        Flux::toast(variant: 'success', text: __('Photo removed!'));
        $this->dispatch('profile-updated');
    }

    public function updatedPhoto()
    {
        $this->validateOnly('photo');
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Flux::toast(text: __('A new verification link has been sent to your email address!'));
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your name and email address')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">

            <div class="space-y-4">
                <flux:label :label="__('Profile Photo')" for="photo" />

                <div class="flex items-center space-x-4">
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('New Profile Photo') }}"
                            class="h-20 w-20 rounded-full object-cover">
                    @elseif (Auth()->user()->profile_photo_path)
                        <img src="{{ Auth()->user()->profile_photo_url }}" alt="{{ $name }}"
                            class="h-20 w-20 rounded-full object-cover">
                    @else
                        <div class="h-20 w-20 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center text-xl font-semibold text-black dark:text-white">
                            {{ Auth()->user()->initials() }}
                        </div>
                    @endif

                    <div class="grid gap-2">
                        <input type="file" wire:model="photo" id="photo" accept="image/*"
                            class="block w-full text-sm text-gray-500 file:me-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-zinc-100 file:text-zinc-700 hover:file:bg-zinc-200 dark:file:bg-zinc-800 dark:file:text-zinc-300 dark:hover:file:bg-zinc-700" />

                        @error('photo')
                            <span class="text-sm text-red-500">{{ $message }}</span>
                        @enderror

                        @if (Auth()->user()->profile_photo_path)
                            <flux:button type="button" variant="danger" size="sm" wire:click="deletePhoto"
                                wire:confirm="{{ __('Are you sure you want to delete your profile photo?') }}">
                                {{ __('Remove Photo') }}
                            </flux:button>
                        @endif
                    </div>
                </div>
            </div>

            <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus
                autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div>
                        <flux:text class="mt-4">
                            {{ __('Your email address is unverified!') }}

                            <flux:link class="text-sm cursor-pointer"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email!') }}
                            </flux:link>
                        </flux:text>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>