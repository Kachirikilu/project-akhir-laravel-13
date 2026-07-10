<?php

namespace App\Livewire\AllRole;

use App\Livewire\Global\HasToast;
use App\Models\Auth\Wallpaper;
// use Illuminate\Support\Facades\Auth;
// use Jenssegers\Agent\Agent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

// use Illuminate\Support\Facades\DB;

class WallpaperManagement extends Component
{
    // use HasSortir;
    // use HasStats;
    use HasToast;
    use WithFileUploads;
    use WithPagination;

    public $wallpaper;

    protected $listeners = [
        'refresh-table' => 'refreshWallpapersList',
        'refresh-data-wallpaper' => 'refreshWallpapersList',
        'loadDraft' => 'loadDraft',
        'saveToDraft' => 'saveToDraft',
    ];

    #[On('refresh-data-wallpaper')]
    #[On('refresh-table')]
    public function refreshWallpapersList()
    {
        $this->resetPage();
    }

    public function updatedWallpaper()
    {
        $this->resetErrorBag();
        $validator = Validator::make(
            ['wallpaper' => $this->wallpaper],
            [
                'wallpaper' => 'image|max:5120',
            ],
            [
                'wallpaper.image' => 'Wallpaper yang diunggah harus berupa gambar!',
                'wallpaper.max' => 'Ukuran foto maksimal adalah 5 MB!',
            ],
        );

        if ($validator->fails()) {
            $this->toast(text: $validator->errors()->first(), type: 'error', variant: 'danger');
            $this->reset('wallpaper');

            return;
        }

        $path = $this->wallpaper->store('wallpapers/'.auth()->id(), 'public');
        auth()
            ->user()
            ->wallpapers()
            ->create([
                'path' => '/storage/'.$path,
            ]);
        $this->reset('wallpaper');
        $this->dispatch('refresh-wallpaper-list');
        $this->toast(text: 'Wallpaper ditambahkan!');
    }

    public function deleteWallpaper($id)
    {
        $wallpaper = Wallpaper::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        if ($wallpaper) {
            $path = str_replace('/storage/', '', $wallpaper->path);
            Storage::disk('public')->delete($path);
            $wallpaper->delete();
            $this->toast(text: 'Wallpaper dihapus!', type: 'update');
        }
    }

    public function loadingDefaultWallpaper() {}
    public function loadingCustomWallpaper() {}

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function placeholder()
    {
        return view('livewire.global.livewire-skeletons.wallpaper-skeleton');
    }

    public function render()
    {
        $perPage = 8;

        $groups = [
            'unsri-w'      => 18,
            'danau-unsri-w' => 7,
            'sky-w'        => 16,
            'musi-w'       => 4,
            'night-w'      => 7,
            'macro-w'      => 4,
            'my-alya-w'      => 7,
            'my-masha-w'      => 7,
            'my-yuki-w'      => 7,
            'my-waguri-w'      => 7,
            'my-yuzuhara-w'      => 7,

        ];

        $defaultsArray = [];

        foreach ($groups as $prefix => $count) {
            for ($i = 1; $i <= $count; $i++) {
                $defaultsArray[] = [
                    'id' => $prefix . $i,
                    'path' => '/wallpapers/' . $prefix . $i . '.png',
                    'is_custom' => false,
                ];
            }
        }
        $currentPage = Paginator::resolveCurrentPage('defaultPage');
        $defaultCollection = collect($defaultsArray);

        $defaults = new LengthAwarePaginator(
            $defaultCollection->forPage($currentPage, $perPage),
            $defaultCollection->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'pageName' => 'defaultPage']
        );

        // 2. Logika Custom Wallpaper (Database)
        $custom = Wallpaper::where('user_id', auth()->id())
            ->latest() 
            ->paginate($perPage, ['*'], 'customPage');

        if ($custom->lastPage() > 0 && $custom->currentPage() > $custom->lastPage()) {
            $this->gotoPage($custom->lastPage(), 'customPage');
            $custom = Wallpaper::where('user_id', auth()->id())
                ->latest()
                ->paginate($perPage, ['*'], 'customPage');
        }

        return view('livewire.all-role.wallpaper-management', [
            'defaultWallpapers' => $defaults,
            'customWallpapers' => $custom,
        ]);
    }
}
