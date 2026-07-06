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
use App\Livewire\Global\HasToast;

new #[Title('Profile Settings')] class extends Component {
    use ProfileValidationRules;
    use WithFileUploads;
    use HasToast;

    #[
        Rule(
            ['nullable', 'image', 'max:2048'],
            message: [
                'photo.image' => 'File yang diunggah harus berupa gambar!',
                'photo.max' => 'Ukuran foto maksimal adalah 2 MB!',
            ],
        ),
    ]
    public $photo;

    protected $listeners = ['validate-photo' => 'updatedPhoto'];

    public function updateProfileInformation(): void
    {
        $this->validate();
        $user = Auth::user();
        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');

            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $user->forceFill(['profile_photo_path' => $path])->save();
            $this->photo = null;
            $this->resetErrorBag();
        }

        // Flux::toast(variant: 'success', text: __('Foto Profil diperbarui!'));
        $this->toast(text: 'Foto Profil diperbarui!', type: 'update');
        $this->dispatch('profile-updated');
    }

    public function deletePhoto(): void
    {
        $user = Auth::user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->forceFill(['profile_photo_path' => null])->save();
        }

        // Menutup modal via JavaScript agar mulus
        // Flux::toast(variant: 'success', text: __('Foto Profil berhasil dihapus!'));
        $this->dispatch('close-modal-delete-photo');
        $this->toast(text: 'Foto Profil diperbarui!', type: 'update');
        $this->dispatch('profile-updated');
    }

    public function updatedPhoto()
    {
        $this->validateOnly('photo');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Perbarui Foto Profil Anda')">
        <form wire:submit.prevent="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="space-y-4 mb-9">
                <flux:label :label="__('Profile Photo')" for="photo" />

                <div class="flex items-center space-x-6 mb-4">
                    {{-- Foto Profil --}}
                    @if ($photo)
                        <img src="{{ $photo->temporaryUrl() }}" alt="{{ __('New Profile Photo') }}"
                            class="h-20 w-20 aspect-square rounded-full object-cover border-2 border-[var(--border-table-color)] shrink-0">
                    @elseif (Auth()->user()->profile_photo_path)
                        <img src="{{ Auth()->user()->profile_photo_url }}" alt="{{ Auth::user()->name }}"
                            class="h-20 w-20 aspect-square rounded-full object-cover border-2 border-[var(--border-table-color)] shrink-0">
                    @else
                        <div
                            class="h-20 w-20 aspect-square rounded-full bg-[var(--sub-table-color)] border border-[var(--border-table-color)] flex items-center justify-center text-xl font-semibold text-[var(--contrast-main-text)] shrink-0">
                            {{ Auth()->user()->initials() }}
                        </div>
                    @endif

                    <div class="flex flex-col items-start gap-3 w-full">
                        {{-- Wrapper untuk Input dan Tombol Hapus --}}
                        <div class="relative w-full flex items-center gap-2">

                            {{-- Input File --}}
                            <input type="file" wire:model="photo" wire:change="$dispatch('validate-photo')"
                                id="photo" accept="image/*"
                                class="block w-full text-sm text-[var(--contrast-third-text)] 
                                            file:me-3 file:py-1.5 file:px-3 file:rounded-lg file:border file:border-[var(--border-table-color)] 
                                            file:text-xs file:font-semibold file:bg-[var(--sub-table-color)] file:text-[var(--contrast-main-text)] 
                                            hover:file:bg-[var(--hover-table-color)] transition-all cursor-pointer" />

                            {{-- Tombol Remove (Bulat Kecil) --}}
                            @if (Auth()->user()->profile_photo_path && !$photo)
                                <button type="button" x-on:click="$flux.modal('confirm-delete-photo').show()"
                                    class="flex items-center justify-center w-6 h-6 aspect-square rounded-full 
                                            bg-[var(--sub-table-color)] border border-[var(--border-table-color)] 
                                            hover:bg-red-500/20 hover:border-red-500 text-[var(--contrast-main-text)] 
                                            hover:text-red-500 transition-all cursor-pointer flex-shrink-0 p-0">

                                    {{-- Ikon dipaksa memiliki ukuran tetap --}}
                                    <flux:icon name="trash" variant="micro" class="w-3 h-3 !block" />
                                </button>
                            @endif
                        </div>

                        {{-- Row Tombol Save --}}
                        <div class="flex items-center gap-2">
                            @if ($photo)
                                <flux:button variant="primary" type="submit" size="sm"
                                    wire:loading.attr="disabled" wire:loading.class="opacity-50"
                                    class="cursor-pointer bg-[var(--focus-color)] hover:bg-[var(--hover-focus-color)]">
                                    <span wire:loading.remove
                                        wire:target="updateProfileInformation">{{ __('Save Photo') }}</span>
                                    <span wire:loading
                                        wire:target="updateProfileInformation">{{ __('Saving...') }}</span>
                                </flux:button>
                            @endif

                            @error('photo')
                                <span class="text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="my-6 w-full space-y-6">

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
            @include('livewire.global.modal-form.input-form', [
                'alpine' => 'user',
                'value' => Auth::user()->prodi . ' / ' . Auth::user()->kode_pr,
                'isLivewire' => 1,
                'noEntangle' => 1,
                'modelString' => 'program_studi',
                'iconString' => 'academic-cap',
                'isRequired' => 0,
                'isReadonly' => 1,
            ])
        </div>

        {{-- @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif --}}


        <flux:modal name="confirm-delete-photo"
            x-on:close-modal-delete-photo.window="$flux.modal('confirm-delete-photo').close()"
            class="min-w-[20rem] max-w-md !bg-[var(--second-pop-up-color)] !table-border !text-[var(--contrast-main-text)] text-xs sm:text-sm">

            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Hapus Foto Profil?</flux:heading>
                    <flux:subheading>
                        Apakah Anda yakin ingin menghapus foto profil ini?
                        <span class="text-red-700 dark:text-red-400 font-medium">Tindakan ini tidak dapat
                            dibatalkan!</span>
                    </flux:subheading>
                </div>

                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost"
                            class="cursor-pointer w-full sm:w-auto 
                            bg-[var(--sub-table-color)] hover:bg-[var(--main-table-color)]
                            text-[var(--contrast-second-text)]
                            transition-colors duration-200">
                            Batal
                        </flux:button>
                    </flux:modal.close>

                    <flux:button wire:click="deletePhoto" wire:loading.attr="disabled" variant="primary"
                        class="text-white cursor-pointer w-full sm:w-auto bg-red-600 hover:bg-red-700 border-none transition-colors duration-200">

                        <span wire:loading.remove wire:target="deletePhoto">Ya, Hapus Foto</span>
                        <span wire:loading wire:target="deletePhoto">Menghapus...</span>
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    </x-pages::settings.layout>
</section>
