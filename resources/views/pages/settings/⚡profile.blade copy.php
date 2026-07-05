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
// use Intervention\Image\Laravel\Facades\Image;

new #[Title('Profile Settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;

    public string $name = '';
    public string $email = '';

    #[
        Rule(
            ['nullable', 'image', 'max:1024'],
            message: [
                'photo.image' => 'File yang diunggah harus berupa gambar!',
                'photo.max' => 'Ukuran foto maksimal adalah 1 MB!',
            ],
        ),
    ]
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
            // $image = Image::make($this->photo->getRealPath());
            // $image->scale(width: 500);
            // $image->toJpeg(70);
            $path = $this->photo->store('profile-photos', 'public');
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->forceFill(['profile_photo_path' => $path])->save();
            $this->photo = null;
        }

        Flux::toast(variant: 'success', text: __('Foto Profil diperbarui!'));

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
        Flux::toast(variant: 'success', text: __('Foto Profil dihapus!'));
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

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Perbarui Foto Profil Anda')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-4 mb-9">
                <flux:label :label="__('Profile Photo')" for="photo" />

                <div class="flex items-center space-x-6 mb-4">
                    {{-- Foto Profil --}}
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('New Profile Photo') }}"
                            class="h-20 w-20 rounded-full object-cover border-2 border-[var(--border-table-color)]">
                    @elseif (Auth()->user()->profile_photo_path)
                        <img src="{{ Auth()->user()->profile_photo_url }}" alt="{{ $name }}"
                            class="h-20 w-20 rounded-full object-cover border-2 border-[var(--border-table-color)]">
                    @else
                        <div
                            class="h-20 w-20 rounded-full bg-[var(--sub-table-color)] border border-[var(--border-table-color)] flex items-center justify-center text-xl font-semibold text-[var(--contrast-main-text)]">
                            {{ Auth()->user()->initials() }}
                        </div>
                    @endif

                    <div class="flex flex-col items-start gap-3">
                        {{-- Input File --}}
                        <input type="file" wire:model="photo" id="photo" accept="image/*"
                            class="block w-full text-sm text-[var(--contrast-third-text)] 
                            file:me-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-[var(--border-table-color)] 
                            file:text-xs file:font-semibold file:bg-[var(--sub-table-color)] file:text-[var(--contrast-main-text)] 
                            hover:file:bg-[var(--hover-table-color)] transition-all cursor-pointer" />

                        @error('photo')
                            <span class="text-xs text-red-500 -mt-1">{{ $message }}</span>
                        @enderror

                        {{-- Row Tombol --}}
                        <div class="flex items-center gap-2">
                            {{-- Tombol Remove --}}
                            @if (Auth()->user()->profile_photo_path && !$photo)
                                <flux:button class="cursor-pointer" variant="danger" size="sm"
                                    wire:click="deletePhoto">
                                    {{ __('Remove Photo') }}
                                </flux:button>
                            @endif

                            {{-- Tombol Save (Hanya muncul jika ada file baru) --}}
                            @if ($photo)
                                <flux:button variant="primary" type="submit" size="sm"
                                    class="cursor-pointer bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]">
                                    {{ __('Save Photo') }}
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'value' => Auth::user()->name,
                'isLivewire' => 1,
                'noEntangle' => 1,
                'modelString' => 'name',
                'iconString' => 'user',
                'isRequired' => 0,
                'isReadonly' => 1,
            ])
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'value' => Auth::user()->email,
                'isLivewire' => 1,
                'noEntangle' => 1,
                'modelString' => 'email',
                'iconString' => 'envelope',
                'isRequired' => 0,
                'isReadonly' => 1,
            ])
            @if (Auth::user()->admin || Auth::user()->dosen)
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'user',
                    'value' => Auth::user()->identity1 . ' / ' . Auth::user()->identity2,
                    'isLivewire' => 1,
                    'noEntangle' => 1,
                    'modelString' => 'identity1_2',
                    'nameXString' => Auth::user()->label_id1 . ' / ' . Auth::user()->label_id2,
                    'iconString' => 'identification',
                    'isRequired' => 0,
                    'isReadonly' => 1,
                ])
            @elseif (Auth::user()->mahasiswa)
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'user',
                    'value' => Auth::user()->mahasiswa->nim,
                    'isLivewire' => 1,
                    'noEntangle' => 1,
                    'modelString' => 'identity1',
                    'nameXString' => 'NIM',
                    'iconString' => 'identification',
                    'isRequired' => 0,
                    'isReadonly' => 1,
                ])
            @endif
            @if (Auth::user()->dosen)
                @include('livewire.global.modal-form.input-form', [
                    'alpine' => 'user',
                    'value' => Auth::user()->dosen->nidk ?? '-',
                    'isLivewire' => 1,
                    'noEntangle' => 1,
                    'modelString' => 'identity3',
                    'nameXString' => 'NIDK',
                    'iconString' => 'identification',
                    'isRequired' => 0,
                    'isReadonly' => 1,
                ])
            @endif
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'value' => Auth::user()->nik,
                'isLivewire' => 1,
                'noEntangle' => 1,
                'modelString' => 'NIK',
                'nameXString' => 'NIK',
                'iconString' => 'identification',
                'isRequired' => 0,
                'isReadonly' => 1,
            ])
            {{-- <flux:input readonly wire:model="name" :label="__('Name')" type="text" required autofocus
                autocomplete="name" /> --}}
            {{-- <div>
                <flux:input readonly wire:model="email" :label="__('Email')" type="email" required
                    autocomplete="email" />

                @if ($this->hasUnverifiedEmail)
                    <div
                        class="mt-4 p-3 rounded-[10px] bg-[var(--focus-color)]/[0.1] border border-[var(--focus-color)]/20">
                        <flux:text class="text-[var(--contrast-main-text)]">
                            {{ __('Your email address is unverified!') }}

                            <flux:link
                                class="text-sm cursor-pointer text-[var(--focus-color)] hover:text-[var(--hover-focus-color)]"
                                wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email!') }}
                            </flux:link>
                        </flux:text>
                    </div>
                @endif
            </div> --}}
        </form>


        {{-- @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif --}}
    </x-pages::settings.layout>
</section>
