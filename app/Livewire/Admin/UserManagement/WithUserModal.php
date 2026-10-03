<?php

namespace App\Livewire\Admin\UserManagement;

use App\Http\Services\UserService;

use App\Livewire\Global\HasErrorCount;
use App\Livewire\Global\HasToast;
use App\Livewire\Global\WithDosenSearchFilters;
use App\Models\Akademik\RPS;
use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
// use App\Models\Auth\Membership;
// use App\Models\Auth\Team;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

trait WithUserModal
{
    use HasErrorCount;
    use HasToast;
    use WithDosenSearchFilters;

    public $showUserModal = false;

    public $showUserRPSModal = false;

    public $showUserExcelModal = false;

    public $isEditingUser = false;

    public $roleType;

    public $tingkatType;

    public $selected_id_user;

    // public $pr_id_2;

    public $user_rps_items_list = [];

    public $user_rps_modal_page = 3;

    public $user_rps_id;

    protected $user_rps_modal_paginator;

    // public $isFlyoutUser = false;

    public $user_input = [
        'role' => '',
        // 'email' => '',
        // 'password' => '',
        'name' => '',
        'nip' => '',
        'nitk' => '',
        'nidn' => '',
        'nidk' => '',
        'nim' => '',
        'nik' => '',
        'status' => '',
        'angkatan' => '',
        'kode_wilayah' => '',
        'jenis_kelamin' => '',
        'agama' => '',
        'kode_no_hp' => '+62',
        'no_hp_back' => '',
        'no_hp' => '',
        'tanggal_lahir' => '',
        'tempat_lahir' => '',
        'role' => '',
        'update_or_create' => 'identity1',
        'tingkat_target' => '',
        // 'excel_user_file' => null,
    ];

    // protected $rules = [
    //     'email' => 'required|email',
    //     'password' => 'nullable|min:8',
    //     'name' => 'required|string|max:255',
    //     'nip' => 'nullable|string|min:8|max:20',
    //     'nitk' => 'nullable|string|min:8|max:20',
    //     'nidn' => 'nullable|string|min:8|max:20',
    //     'nidk' => 'nullable|string|min:8|max:20',
    //     'nim' => 'required|string|max:20',
    //     'angkatan' => 'required|integer',
    //     'pr_id' => 'required|integer|exists:prodis,id',
    // ];

    // public function updatedShowUserModal($value)
    // {
    //     if (! $value) {
    //         $this->isEditingUser = false;
    //     }
    //     $this->syncFlyoutPreStates();
    // }

    public function addUser($role)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $this->resetValidation();
        $this->resetErrorBag();
        $this->isEditingUser = false;
        $this->roleType = $role;

        if (Auth::user()->tingkat == 2) {
            $this->fk_id = Auth::user()->fk_id;
            $this->prLevel = 3;
        } elseif (Auth::user()->tingkat == 3) {
            $this->dp_id = Auth::user()->dp_id;
            $this->prLevel = 2;
        } elseif (Auth::user()->tingkat == 4) {
            $this->pr_id = Auth::user()->pr_id;
        }
        if ($role == 'excel') {
            $this->showUserExcelModal = true;
            $this->showUserModal = false;
        } else {
            $this->showUserModal = true;
            $this->showUserExcelModal = false;
        }

        $colors = [
            'admin' => 'text-red-700 dark:text-red-400',
            'dosen' => 'text-lime-700 dark:text-lime-400',
            'mahasiswa' => 'text-cyan-700 dark:text-cyan-400',
        ];
        $color = $colors[$role] ?? 'text-gray-700 dark:text-gray-400';
        $this->dispatch('prepare-add-user-modal', type: $role, color: $color);
        if (Auth::user()->tingkat < 4) {
            $this->updatedPrNameSearch($this->prNameSearch);
        }
    }

    public function editUser($id, $withRPS = false, $isRPS = false)
    {
        if ($isRPS) {
            if (! $this->AuthCheck('staff')) {
                return;
            }
        } else {
            if (! $this->AuthCheck()) {
                return;
            }
        }

        $this->resetInputUser();
        $this->resetValidation();

        if ($isRPS) {
            $this->showUserRPSModal = true;
            $this->showUserModal = false;
        } else {
            $this->showUserModal = true;
            $this->showUserRPSModal = false;
        }
        $this->showUserExcelModal = false;
        $this->isEditingUser = true;

        try {
            $user = User::with(['admin', 'dosen', 'mahasiswa', 'admin.pr_rel', 'dosen.pr_rel', 'mahasiswa.pr_rel'])->findOrFail($id);
            $this->selected_id_user = $user->id;

            $this->user_input = array_merge($this->user_input, [
                'name' => $user->name,
                'nik' => $user->nik,
                'status' => $user->status,
                'tempat_lahir' => $user->tmt_lahir,
                'tanggal_lahir' => $user->tanggal_lahir,
                'agama' => $user->agama,
                'jenis_kelamin' => $user->gender,
                'role' => $user->role,
            ]);

            $phone = $this->formatNomorHP($user->no_hp);
            $this->user_input['no_hp_back'] = $phone['no_hp_back'];
            $this->user_input['kode_no_hp'] = $phone['kode_no_hp'];

            $relasi = match (strtolower($user->role)) {
                'admin' => ['nip', 'nitk', 'kode_wilayah'],
                'dosen' => ['nip', 'nidn', 'nidk'],
                'mahasiswa' => ['nim', 'angkatan', 'kode_wilayah'],
                default => []
            };

            foreach ($relasi as $field) {
                $this->user_input[$field] = $user->{strtolower($user->role)}->$field ?? null;
            }

            if (Auth::user()->tingkat == 2) {
                $this->fk_id = Auth::user()->fk_id;
                $this->prLevel = 3;
            } elseif (Auth::user()->tingkat == 3) {
                $this->dp_id = Auth::user()->dp_id;
                $this->prLevel = 2;
            } elseif (Auth::user()->tingkat == 4) {
                $this->pr_id = Auth::user()->pr_id;
            }
            if (Auth::user()->tingkat < 4) {
                $this->pr_id = $user->pr_id;
                if (! $isRPS) {
                    $this->fetchPr();
                }
            }
            $this->roleType = strtolower($user->role);
            $this->tingkatType = $user->tingkat;

            if ($withRPS) {
                $this->user_rps_id = $user->role_id;
                $this->resetPage('user_rps_modal_page');
                $this->loadUserRPSPagination();
            }

            // dump($this->pr_id);
            // $this->syncUserStore($user, $formattedPhone['no_hp_back'] ?? '');
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Mengambil Data: '.$e->getMessage(), variant: 'danger');
        }
    }

    //    public function syncUserStore($user, $noHpBack = '')
    //     {
    //         $this->dispatch('sync-user-store',
    //             email: $user->email ?? '',
    //         );
    //     }

    // public function syncUserStore($user, $noHpBack = '')
    // {
    //     $detail = $user->admin ?? ($user->dosen ?? $user->mahasiswa);
    //     $type = strtolower($user->role);
    //     $colors = [
    //         'admin' => 'text-red-700 dark:text-red-400',
    //         'dosen' => 'text-lime-700 dark:text-lime-400',
    //         'mahasiswa' => 'text-cyan-700 dark:text-cyan-400',
    //     ];
    //     $color = $colors[$type] ?? 'text-gray-700 dark:text-gray-400';

    //     $this->dispatch('sync-user-store',
    //         type: $type,
    //         color: $color,
    //         email: $user->email ?? '',
    //         password: '',
    //         name: $user->name ?? '',
    //         nip: $user->admin->nip ?? ($user->dosen->nip ?? ''),
    //         nitk: $user->admin->nitk ?? '',
    //         nidn: $user->dosen->nidn ?? '',
    //         nidk: $user->dosen->nidk ?? '',
    //         nim: $user->mahasiswa->nim ?? '',
    //         nik: $user->nik ?? '',
    //         angkatan: $user->mahasiswa->angkatan ?? '',
    //         status: $user->status ?? '',
    //         prId: $user->pr_id ?? '',
    //         kodePr: $user->kode_pr ?? '',
    //         prodi: $user->prodi ?? '',
    //         departemen: $detail?->pr_rel?->departemen_dp ?? '',
    //         fakultas: $detail?->pr_rel?->fakultas_fk ?? '',
    //         wilayah: $user->kode_wilayah ?? '',
    //         rps: $user->mahasiswa?->count_rps ?? 0,
    //         sks: $user->mahasiswa?->total_sks ?? 0,
    //         rekap: $user->mahasiswa?->rekap_mhs ?? 0.00,
    //         index: $user->mahasiswa?->index_mhs ?? 0.00,
    //         mutu: $user->mahasiswa?->mutu_mhs ?? 'E',
    //         jk: $user->gender ?? '',
    //         agama: $user->agama ?? '',
    //         tmtLahir: $user->tmt_lahir ?? '',
    //         tglLahir: $user->tanggal_lahir ?? '',
    //         noHP: $noHpBack ?? ''
    //     );
    // }

    private function loadUserRPSPagination()
    {
        if (empty($this->user_rps_id)) {
            return;
        }

        $role = strtolower($this->roleType);
        $rpsQuery = RPS::query();

        if ($role === 'dosen') {
            $dosen = Dosen::find($this->user_rps_id);
            if (! $dosen) {
                return;
            }

            $rpsQuery->whereHas('tim_dosens.dosens', function ($query) use ($dosen) {
                $query->where('dosens.id', $dosen->id);
            });

        } elseif ($role === 'mahasiswa') {
            $mahasiswa = Mahasiswa::find($this->user_rps_id);
            if (! $mahasiswa) {
                return;
            }

            $rpsQuery->whereIn('id', function ($query) use ($mahasiswa) {
                $query->select('rps_id')
                    ->from('nilai_mahasiswa')
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->whereNotNull('rps_id')
                    ->distinct();
            });
        } else {
            // Handle jika role tidak valid
            return;
        }

        // Eksekusi pagination
        $rps = $rpsQuery->paginate(
            $this->user_rps_modal_page,
            ['*'],
            'user_rps_modal_page'
        );

        // Update state komponen
        $this->user_rps_items_list = $this->mapRPS($rps);
        $this->user_rps_modal_paginator = $rps;
    }

    private function loadDosenRPSPagination()
    {
        if (empty($this->user_rps_id)) {
            return;
        }

        $dosen = Dosen::find($this->user_rps_id);

        if (! $dosen) {
            return;
        }

        $rps = RPS::whereHas('tim_dosens.dosens', function ($query) use ($dosen) {
            $query->where('dosens.id', $dosen->id);
        })->paginate($this->user_rps_modal_page, ['*'], 'user_rps_modal_page');

        $this->user_rps_items_list = $this->mapRPS($rps);
        $this->user_rps_modal_paginator = $rps;
    }

    // private function loadMahasiswaRPSPagination()
    // {
    //     if (empty($this->user_rps_id)) {
    //         return;
    //     }

    //     $mahasiswa = Mahasiswa::find($this->user_rps_id);

    //     if (! $mahasiswa) {
    //         return;
    //     }

    //     $rps = RPS::query()
    //         ->whereIn('id', function ($query) use ($mahasiswa) {
    //             $query->select('rps_id')
    //                 ->from('nilai_mahasiswa')
    //                 ->where('mahasiswa_id', $mahasiswa->id)
    //                 ->whereNotNull('rps_id')
    //                 ->distinct();
    //         })
    //         ->paginate(
    //             $this->user_rps_modal_page,
    //             ['*'],
    //             'user_rps_modal_page'
    //         );

    //     $this->user_rps_items_list = $this->mapRPS($rps);
    //     $this->user_rps_modal_paginator = $rps;
    // }

    public function updatedUserRPSModalPage($page)
    {
        $this->loadUserRPSPagination();
        // $this->loadDosenRPSPagination();
        // $this->loadMahasiswaRPSPagination();
    }

    private function inputModalUser($isEditingUser, $data, $role)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $authUser = auth()->user();
        $authUserId = $authUser?->id;
        $authUserTingkat = $authUser?->admin?->tingkat ?? 4;

        return app(UserService::class)->inputModalUser(
            isEditingUser: $isEditingUser,
            data: $data,
            role: $role,
            selectedUserId: $this->selected_id_user,
            tingkatType: $this->tingkatType ?? null,
            authUserId: $authUserId,
            authUserTingkat: $authUserTingkat
        );
    }

    public function formatNomorHP($noHP)
    {
        $result = [
            'kode_no_hp' => '+62',
            'no_hp_back' => '',
        ];

        if (blank($noHP)) {
            return $result;
        }
        $noHP = trim($noHP);
        $countryCodes = [
            '62',
            '65',
            '60',
            '1',
            '44',
            '81',
            '82',
            '86',
        ];
        if (str_starts_with($noHP, '+')) {
            $digits = preg_replace('/\D/', '', $noHP);
            foreach ($countryCodes as $code) {
                if (str_starts_with($digits, $code)) {
                    $result['kode_no_hp'] = '+'.$code;
                    $cleaned = substr($digits, strlen($code));
                    break;
                }
            }
            $cleaned ??= $digits;
        } else {
            $cleaned = preg_replace('/\D/', '', $noHP);
            if (str_starts_with($cleaned, '0')) {
                $cleaned = substr($cleaned, 1);
            } elseif (str_starts_with($cleaned, '62')) {
                $cleaned = substr($cleaned, 2);
            }
        }
        if (preg_match('/^(\d{1,3})(\d{1,4})?(\d{1,5})?(\d+)?$/', $cleaned, $matches)) {
            array_shift($matches);
            $result['no_hp_back'] = implode(' - ', array_filter($matches));
        } else {
            $result['no_hp_back'] = $cleaned;
        }

        return $result;
    }

    public function saveUser($dataAlpine)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $data = array_merge($this->user_input, $dataAlpine);
        $data['pr_id'] = $this->pr_id;

        $role = strtolower($this->roleType);
        try {
            $validated = $this->inputModalUser(false, $data, $role);

            DB::transaction(function () use ($validated, $role) {

                // 1. Buat User Baru
                $user = User::create([
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                if ($role !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($role == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($role !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'] ?? null;
                }

                $dosen = null;

                $data = [
                    'user_id' => $user->id,
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'] ?? null,

                    'agama' => $validated['agama'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($role === 'admin') {
                    Admin::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ]));
                } elseif ($role === 'dosen') {
                    $dosen = Dosen::create(array_merge($data, [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ]));
                } elseif ($role === 'mahasiswa') {
                    Mahasiswa::create(array_merge($data, [
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'kode_wilayah' => $kodeWly,
                    ]));
                }

                // $team = Team::forceCreate([
                //     'name' => explode(' ', $validated['name'])[0]."'s Team",
                //     'is_personal' => true,
                // ]);

                // $user->forceFill(['current_team_id' => $team->id])->save();

                // Membership::create([
                //     'team_id' => $team->id,
                //     'user_id' => $user->id,
                //     'role' => 'owner',
                // ]);

                // if (! empty($this->showTimDosenModal) && $dosen) {
                //     if (! isset($this->dosen_id_array) || ! is_array($this->dosen_id_array)) {
                //         $this->dosen_id_array = [];
                //     }
                //     if (! isset($this->dosen_items_array) || ! is_array($this->dosen_items_array)) {
                //         $this->dosen_items_array = [];
                //     }
                //     if (! in_array($dosen->id, $this->dosen_id_array)) {
                //         $this->dosen_id_array[] = $dosen->id;
                //         $this->dosen_items_array[] = $this->itemsDosen($dosen);
                //     }

                //     $isKetua = collect($this->dosen_items_array)
                //         ->contains(fn ($item) => $item['is_ketua'] === true);
                //     if (! $isKetua && count($this->dosen_items_array) > 0) {
                //         $lastIndex = array_key_last($this->dosen_items_array);
                //         $this->dosen_items_array[$lastIndex]['is_ketua'] = true;
                //         $this->dosen_items_array[$lastIndex]['peran'] = 'Koordinator';
                //     }
                // }

                if (($this->parent == 'tim-dosen' || $this->parent == 'tim_dosen') && $dosen) {
                    $this->dispatch('dosen-created-tim-dosen', id: $dosen->id);
                }

            });

            $this->toast(message: ucfirst($this->roleType), isAkun: true);
            $this->resetInputUser();

            $this->dispatch('refresh-data-user');
            $this->dispatch('refresh-stats-user');
            $this->showUserModal = false;
            $this->showUserRPSModal = false;
        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Menambahkan: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserModal = false;
            $this->showUserRPSModal = false;
        }
    }

    public function updateUser($dataAlpine)
    {
        if (! $this->AuthCheck()) {
            return;
        }
        $data = array_merge($this->user_input, $dataAlpine);
        $data['pr_id'] = $this->pr_id;
        $role = strtolower($this->roleType);

        try {
            $validated = $this->inputModalUser(true, $data, $role);

            DB::transaction(function () use ($validated, $role) {

                $user = User::findOrFail($this->selected_id_user);
                $user->update(['email' => $validated['email']]);

                if ($validated['password']) {
                    $user->update(['password' => Hash::make($validated['password'])]);
                }

                if ($role !== 'mahasiswa') {
                    $identity1Input = $validated['nip'];
                    if ($role == 'admin') {
                        $identity2Input = ($validated['nitk'] ?? null) ?: null;
                    } else {
                        $identity2Input = ($validated['nidn'] ?? null) ?: null;
                    }
                } else {
                    $identity1Input = $validated['nim'];
                }

                if ($role !== 'dosen') {
                    $kodeWly = $validated['kode_wilayah'];
                }

                $model = match ($role) {
                    'admin' => $user->admin,
                    'dosen' => $user->dosen,
                    'mahasiswa' => $user->mahasiswa,
                };

                $data = [
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'status' => $validated['status'],
                    'no_hp' => $validated['no_hp'],

                    'agama' => $validated['agama'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                ];

                if ($role === 'admin') {
                    $data += [
                        'nip' => $identity1Input,
                        'nitk' => $identity2Input,
                        'kode_wilayah' => $kodeWly,
                    ];
                } elseif ($role === 'dosen') {
                    $data += [
                        'nip' => $identity1Input,
                        'nidn' => $identity2Input,
                        'nidk' => ($validated['nidk'] ?? null) ?: null,
                    ];
                } elseif ($role === 'mahasiswa') {
                    $data += [
                        'nim' => $identity1Input,
                        'angkatan' => $validated['angkatan'],
                        'kode_wilayah' => $kodeWly,
                    ];
                }

                $model->update($data);
            });
            $roleType = ucfirst($role);

            if ($role == 'admin' || $role == 'dosen') {
                $labelIdentity1 = "NIP {$validated['nip']}";
            } elseif ($role == 'mahasiswa') {
                $labelIdentity1 = "NIM {$validated['nim']}";
            }

            $this->toast(message: "{$roleType} {$labelIdentity1} dengan Email {$validated['email']}", type: 'update', isAkun: true);
            $this->dispatch('refresh-data-user');
            $this->dispatch('refresh-stats-user');

            $this->showUserModal = false;
            $this->showUserRPSModal = false;
            if (Auth::id() === $this->selected_id_user) {
                $this->dispatch('profile-updated');
            }
            $this->resetInputUser();

        } catch (ValidationException $e) {
            $this->toast(text: 'Validasi Gagal: '.collect($e->errors())->first()[0], variant: 'danger');
            throw $e;
        } catch (\Exception $e) {
            $this->toast(text: 'Gagal Memperbarui: '.$e->getMessage(), variant: 'danger');
            $this->dispatch('refresh-data-user');
            $this->showUserDelete = false;
        }
    }

    public function validationMessagesUser(): array
    {
        return app(UserService::class)->validationMessagesUser();
    }

    public function getUserErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'email',
                'password',
            ]),
            2 => $this->getErrorCount([
                'name',
                'nik',
                'jenis_kelamin',
                'agama',
            ]),
            3 => $this->getErrorCount([
                'nip',
                'nitk',
                'nidn',
                'nidk',
                'nim',
            ]),
            4 => $this->getErrorCount([
                'kode_wilayah',
                'pr_id',
                'angkatan',
                'status',
            ]),
            5 => $this->getErrorCount([
                'tempat_lahir',
                'tanggal_lahir',
                'no_hp',
            ]),
            6 => $this->getErrorCount([]),
        ];
    }

    public function getUserAntreanErrorSections()
    {
        return [
            1 => $this->getErrorCount([
                'excel_user_file',
                'pr_id',
                'role',
                'update_or_create',
            ]),
            2 => 0,
        ];
    }

    public function resetInputUser(
        // $keepProdi = false
    ) {
        $fields = [
            'user_input',
            'selected_id_user',
            'pr_id', 'prNameSearch',
            // 'pr_id', 'pr_id_2', 'prNameSearch',
            // 'email', 'password', 'name', 'nip', 'nitk',
            // 'nidn', 'nidk', 'nim', 'angkatan',
            'roleType',
        ];

        $this->reset($fields);
        $this->resetErrorBag();
    }
}
