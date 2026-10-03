<?php

namespace App\Http\Services;

use App\Models\Auth\Admin;
use App\Models\Auth\Dosen;
use App\Models\Auth\Mahasiswa;
use App\Models\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UserService
{
    public $selected_id_user;

    public function parseSingleExcelFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            return [];
        }

        $spreadsheet = IOFactory::load($filePath);
        $allSheets = $spreadsheet->getAllSheets();
        $parsedRows = [];

        foreach ($allSheets as $worksheet) {
            $allData = $worksheet->toArray();
            if (empty($allData)) {
                continue;
            }

            /** ===============================
             * CARI HEADER & DUA BARIS HEADER (Robust Search)
             * =============================== */
            $headerRowIndex = null;
            $secondHeaderRowIndex = null;

            // Scan hingga 10 baris pertama untuk menemukan baris header utama
            foreach ($allData as $i => $row) {
                if ($i > 10) {
                    break;
                }

                $rowValues = collect($row)->map(fn ($v) => Str::lower(trim((string) $v)))->filter()->values();

                if ($rowValues->contains('email') || $rowValues->contains('role') || $rowValues->contains('nama') || $rowValues->contains('tingkat')) {
                    $headerRowIndex = $i;
                    if (isset($allData[$i + 1])) {
                        $nextRow = collect($allData[$i + 1])->filter(fn ($v) => trim((string) $v) !== '');
                        if ($nextRow->count() > 0) {
                            $secondHeaderRowIndex = $i + 1;
                        }
                    }
                    break;
                }
            }

            if ($headerRowIndex === null) {
                continue;
            }

            $rawHeader1 = $allData[$headerRowIndex];
            $rawHeader2 = $secondHeaderRowIndex !== null ? $allData[$secondHeaderRowIndex] : [];

            // Ambil semua indeks kolom yang tersedia di baris 1 maupun baris 2
            $maxCols = max(count($rawHeader1), count($rawHeader2));
            $headers = [];

            for ($idx = 0; $idx < $maxCols; $idx++) {
                $val1 = isset($rawHeader1[$idx]) ? Str::lower(trim((string) $rawHeader1[$idx])) : '';
                $val2 = isset($rawHeader2[$idx]) ? Str::lower(trim((string) $rawHeader2[$idx])) : '';

                $finalHeader = '';

                // 1. Jika baris 2 punya nilai khusus, prioritaskan baris 2
                if (in_array($val2, ['role', 'tingkat', 'tingkat role', 'tingkat_role', 'level', 'email', 'nama', 'name', 'nip', 'nim', 'nik'])) {
                    $finalHeader = $val2;
                }
                // 2. Jika baris 1 kosong atau berupa header parent/group, gunakan baris 2
                elseif ($val1 === '' || $val1 === 'identitas (id)' || str_contains($val1, 'pendidikan') || str_contains($val1, 'pangkat')) {
                    $finalHeader = $val2 !== '' ? $val2 : $val1;
                }
                // 3. Utama baris 2 jika lebih spesifik, atau gunakan baris 1
                else {
                    $finalHeader = $val2 !== '' ? $val2 : $val1;
                }

                if ($finalHeader !== '') {
                    $headers[$idx] = $finalHeader;
                }
            }

            /** ===============================
             * PARSE DATA KE ARRAY
             * =============================== */
            $startDataIndex = ($secondHeaderRowIndex ?? $headerRowIndex) + 1;
            $dataRows = array_slice($allData, $startDataIndex);

            foreach ($dataRows as $row) {
                if (collect($row)->filter(fn ($v) => trim((string) $v) !== '')->count() === 0) {
                    continue;
                }

                $data = [];
                foreach ($headers as $col => $header) {
                    $data[$header] = trim((string) ($row[$col] ?? ''));
                }

                $rawGender = $data['jenis kelamin'] ?? $data['jenis_kelamin'] ?? $data['gender'] ?? '';
                $gender = trim((string) $rawGender);
                $genderFormatted = match (strtoupper($gender)) {
                    'L', 'LAKI', 'LAKI-LAKI' => 'Laki-laki',
                    'P', 'PEREMPUAN' => 'Perempuan',
                    default => ''
                };

                $rawTingkat = $data['tingkat role'] ?? $data['tingkat_role'] ?? $data['tingkat'] ?? $data['level'] ?? null;

                $parsedRows[] = [
                    'email' => $data['email'] ?? '',
                    'role' => ucfirst(! empty($data['role']) ? $data['role'] : 'None'),
                    'tingkat' => $this->parseTingkatValue($rawTingkat, $data['tingkat'] ?? null),
                    'tingkat_target' => $this->parseTingkatValue($rawTingkat, $data['tingkat'] ?? null),
                    'password' => $data['password'] ?? '',
                    'name' => $data['name'] ?? $data['nama'] ?? '',
                    'status' => $data['status'] ?? $data['kabar'] ?? '',
                    'nip' => $data['nip'] ?? '',
                    'nitk' => $data['nitk'] ?? '',
                    'nidn' => $data['nidn'] ?? '',
                    'nidk' => $data['nidk'] ?? '',
                    'nim' => $data['nim'] ?? '',
                    'nik' => $data['nik'] ?? '',
                    'no_hp' => $data['no. hp'] ?? $data['no hp'] ?? $data['no telepon'] ?? $data['telepon'] ?? $data['wa'] ?? $data['no wa'] ?? '',
                    'agama' => $data['agama'] ?? $data['kepercayaan'] ?? '',
                    'jenis_kelamin' => $genderFormatted,
                    'tempat_lahir' => $data['tempat lahir'] ?? $data['tmt lahir'] ?? '',
                    'tanggal_lahir' => $data['tanggal lahir'] ?? $data['tgl lahir'] ?? '',
                    'kode_wilayah' => strtoupper(($data['kode wilayah'] ?? $data['kode kampus'] ?? '')),
                    'angkatan' => $data['tahun angkatan'] ?? $data['angkatan'] ?? '',
                ];
            }
        }

        return $parsedRows;
    }

    public function parseTingkatValue($value, ?string $role = null): int
    {
        $role = strtolower(trim($role ?? ''));

        // 1. Tentukan nilai awal (secara default/fallback)
        $tingkat = null;

        if ($value !== null && $value !== '') {
            if (is_numeric($value)) {
                // Jika numeric (int/numeric-string), paksa cast ke integer
                $tingkat = (int) $value;
            } else {
                // Jika string teks, lakukan lookup di mapping
                $mapTingkat = [
                    // Admin Mapping
                    'admin program studi' => 4,
                    'admin departemen' => 3,
                    'admin fakultas' => 2,
                    'admin '.strtolower(config('app.univ')) => 1,
                    'admin univ' => 1,
                    'admin universitas' => 1,
                    'admin' => 4,
                    'super admin' => 1,
                    'admin uni' => 1,
                    'admin fk' => 2,
                    'admin dp' => 3,
                    'admin pr' => 4,

                    // Dosen Mapping
                    'dosen umum' => 5,
                    'dosen program studi' => 4,
                    'dosen departemen' => 3,
                    'dosen fakultas' => 2,
                    'dosen '.strtolower(config('app.univ')) => 1,
                    'dosen univ' => 1,
                    'dosen universitas' => 1,
                    'super dosen' => 1,
                    'dosen uni' => 1,
                    'dosen fk' => 2,
                    'dosen dp' => 3,
                    'dosen pr' => 4,
                    'dosen' => 5,

                    // Mahasiswa Mapping
                    'mahasiswa' => 5,

                    'univ' => 1,
                    'uni' => 1,
                    'fk' => 2,
                    'dp' => 3,
                    'pr' => 4,
                    'umum' => 5,

                    'program studi' => 4,
                    'departemen' => 3,
                    'fakultas' => 2,
                    'universitas' => 1,
                    'universitas sriwijaya' => 1,
                    'unsri' => 1,
                    strtolower(config('app.univ')) => 1,
                    strtolower(config('app.universitas')) => 1,
                ];

                $key = strtolower(trim((string) $value));
                if (isset($mapTingkat[$key])) {
                    $tingkat = (int) $mapTingkat[$key];
                }
            }
        }

        // 2. Terapkan aturan batas (Constraint) berdasarkan Role
        return match ($role) {
            'admin' => in_array($tingkat, [1, 2, 3, 4], true) ? $tingkat : 4,
            'dosen' => in_array($tingkat, [1, 2, 3, 4, 5], true) ? $tingkat : 5,
            'mahasiswa' => 5,
            default => ($tingkat !== null) ? $tingkat : 5,
        };
    }

    public function resolveExistingUserId(array $row, string $role, string $mode = 'identity1'): ?int
    {
        $lowerRole = strtolower($role);

        if ($mode === 'email') {
            $email = trim((string) ($row['email'] ?? ''));
            if (! empty($email)) {
                $user = User::where('email', $email)->first();

                return $user ? $user->id : null;
            }

            return null;
        }

        $modelClass = match ($lowerRole) {
            'admin' => Admin::class,
            'dosen' => Dosen::class,
            'mahasiswa' => Mahasiswa::class,
            default => null,
        };

        if (! $modelClass) {
            return null;
        }

        $query = $modelClass::query();

        if ($mode === 'identity1') {
            $identityColumn = ($lowerRole === 'mahasiswa') ? 'nim' : 'nip';
            $searchValue = trim((string) ($row[$identityColumn] ?? ''));

            if ($searchValue !== '') {
                $record = $query->where($identityColumn, $searchValue)->first();

                return $record ? $record->user_id : null;
            }
        } elseif ($mode === 'nik') {
            $searchValue = trim((string) ($row['nik'] ?? ''));

            if ($searchValue !== '') {
                $record = $query->where('nik', $searchValue)->first();

                return $record ? $record->user_id : null;
            }
        }

        return null;
    }

    /**
     * Menyimpan pengguna baru (Insert Mode)
     */
    public function saveUserFromExcel(array $validated, string $role): void
    {
        DB::transaction(function () use ($validated, $role) {
            $user = User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            if ($role === 'admin') {
                Admin::create([
                    'user_id' => $user->id,
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
                    'nip' => $validated['nip'],
                    'nitk' => $validated['nitk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'no_hp' => $validated['no_hp'],
                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                ]);
            } elseif ($role === 'dosen') {
                Dosen::create([
                    'user_id' => $user->id,
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
                    'nip' => $validated['nip'],
                    'nidn' => $validated['nidn'] ?? null,
                    'nidk' => $validated['nidk'] ?? null,
                    'nik' => $validated['nik'],
                    'pr_id' => $validated['pr_id'],
                    'no_hp' => $validated['no_hp'],
                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                ]);
            } elseif ($role === 'mahasiswa') {
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'tingkat_user' => (string) $validated['tingkat'],
                    'name' => $validated['name'],
                    'status' => $validated['status'],
                    'nim' => $validated['nim'],
                    'nik' => $validated['nik'],
                    'angkatan' => $validated['angkatan'],
                    'pr_id' => $validated['pr_id'],
                    'kode_wilayah' => $validated['kode_wilayah'],
                    'no_hp' => $validated['no_hp'],
                    'agama' => $validated['agama'],
                    'jenis_kelamin' => $validated['jenis_kelamin'],
                    'tanggal_lahir' => $validated['tanggal_lahir'],
                    'tempat_lahir' => $validated['tempat_lahir'],
                ]);
            }
        });
    }

    /**
     * Update atau Buat Pengguna (UpdateOrCreate Mode)
     */
    public function saveUserFromExcelUpdateOrCreate(array $validated, string $role, ?int $selectedUserId = null, string $mode = 'identity1'): void
    {
        DB::transaction(function () use ($validated, $role, $selectedUserId, $mode) {
            $userMatchAttributes = $selectedUserId
                ? ['id' => $selectedUserId]
                : ['email' => $validated['email']];

            $userData = [
                'email' => $validated['email'],
            ];
            if (! empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            } else {
                if (! $selectedUserId) {
                    $defaultPass = $validated['nip'] ?? $validated['nim'] ?? $validated['nik'] ?? $validated['email'] ?? 'defaultpassword';
                    $userData['password'] = Hash::make($defaultPass);
                }
            }

            $user = User::updateOrCreate(
                $userMatchAttributes,
                $userData
            );

            if ($role === 'admin') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nip',
                };

                $adminMatchAttributes = $selectedUserId
                    ? ['user_id' => $selectedUserId]
                    : [$matchField => $validated[$matchField]];

                Admin::updateOrCreate(
                    $adminMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
                        'nip' => $validated['nip'],
                        'nitk' => $validated['nitk'] ?? null,
                        'nik' => $validated['nik'],
                        'pr_id' => $validated['pr_id'],
                        'kode_wilayah' => $validated['kode_wilayah'],
                        'no_hp' => $validated['no_hp'],
                        'agama' => $validated['agama'],
                        'jenis_kelamin' => $validated['jenis_kelamin'],
                        'tanggal_lahir' => $validated['tanggal_lahir'],
                        'tempat_lahir' => $validated['tempat_lahir'],
                    ]
                );
            } elseif ($role === 'dosen') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nip',
                };

                $dosenMatchAttributes = $selectedUserId
                    ? ['user_id' => $selectedUserId]
                    : [$matchField => $validated[$matchField]];

                Dosen::updateOrCreate(
                    $dosenMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
                        'nip' => $validated['nip'],
                        'nidn' => $validated['nidn'] ?? null,
                        'nidk' => $validated['nidk'] ?? null,
                        'nik' => $validated['nik'],
                        'pr_id' => $validated['pr_id'],
                        'no_hp' => $validated['no_hp'],
                        'agama' => $validated['agama'],
                        'jenis_kelamin' => $validated['jenis_kelamin'],
                        'tanggal_lahir' => $validated['tanggal_lahir'],
                        'tempat_lahir' => $validated['tempat_lahir'],
                    ]
                );
            } elseif ($role === 'mahasiswa') {
                $matchField = match ($mode) {
                    'nik' => 'nik',
                    'email' => 'email',
                    default => 'nim',
                };

                $mahasiswaMatchAttributes = $selectedUserId
                    ? ['user_id' => $selectedUserId]
                    : [$matchField => $validated[$matchField]];

                Mahasiswa::updateOrCreate(
                    $mahasiswaMatchAttributes,
                    [
                        'user_id' => $user->id,
                        'tingkat_user' => (string) $validated['tingkat'],
                        'name' => $validated['name'],
                        'status' => $validated['status'],
                        'nim' => $validated['nim'],
                        'nik' => $validated['nik'],
                        'angkatan' => $validated['angkatan'],
                        'pr_id' => $validated['pr_id'],
                        'kode_wilayah' => $validated['kode_wilayah'],
                        'no_hp' => $validated['no_hp'],
                        'agama' => $validated['agama'],
                        'jenis_kelamin' => $validated['jenis_kelamin'],
                        'tanggal_lahir' => $validated['tanggal_lahir'],
                        'tempat_lahir' => $validated['tempat_lahir'],
                    ]
                );
            }
        });
    }

    private function uniqueRule(string $table, string $column)
    {
        return $this->selected_id_user
            ? Rule::unique($table, $column)->ignore($this->selected_id_user, 'user_id')
            : Rule::unique($table, $column);
    }

    public function inputModalUser(
        $isEditingUser,
        $data,
        $role,
        ?int $selectedUserId = null,
        $tingkatType = null,
        ?int $authUserId = null,
        ?int $authUserTingkat = null
    ) {
        // $this->resetErrorBag();
        // $this->resetValidation();
        $this->selected_id_user = $selectedUserId;

        $data['tingkat'] = $this->parseTingkatValue($data['tingkat'] ?? null, $role);

        if (empty($data['status'])) {
            $data['status'] = 'Aktif';
        }

        // dd($data['no_hp_back']);
        $kode = $data['kode_no_hp'] ?? null;
        $noBack = $data['no_hp_back'] ?? null;
        $noHp = $data['no_hp'] ?? null;

        if ($data['email'] == null || $data['email'] == '' || empty($data['email'])) {
            if (strtolower($role) == 'admin') {
                $data['email'] = $data['nip'].'@staff.unsri.ac.id';
            } elseif (strtolower($role) == 'dosen') {
                $data['email'] = $data['nip'].'@lecturer.unsri.ac.id';
            } elseif (strtolower($role) == 'mahasiswa') {
                $data['email'] = $data['nim'].'@student.unsri.ac.id';
            }
        }

        if ($data['jenis_kelamin'] == 'L') {
            $data['jenis_kelamin'] = 'Laki-laki';
        } elseif ($data['jenis_kelamin'] == 'P') {
            $data['jenis_kelamin'] = 'Perempuan';
        }

        if (empty($noBack) && ! empty($noHp)) {
            $data['kode_no_hp'] = null;
            $noHPLengkap = $noHp;
        } elseif (! empty($kode) && ! empty($noBack)) {
            $gabungan = $kode.$noBack;
            $data['no_hp'] = str_replace([' ', '-', '+', '/'], '', $gabungan);
        } else {
            $data['no_hp'] = null;
        }
        if (empty($data['password']) && ! $isEditingUser) {
            if ($role === 'admin' || $role === 'dosen') {
                $data['password'] = $data['nip'];
            } elseif ($role === 'mahasiswa') {
                $data['password'] = $data['nim'];

            }
        }

        if (empty($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = null;
        }

        $rules = [
            'password' => $isEditingUser ? 'nullable|min:8' : 'required|min:8',
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|min:8|max:20',
            'nitk' => 'nullable|string|min:8|max:20',
            'nidn' => 'nullable|string|min:8|max:20',
            'nidk' => 'nullable|string|min:8|max:20',
            'nim' => 'nullable|string|min:8|max:20',
            'nik' => 'required|string|min:12|max:16',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($this->selected_id_user),
            ],
        ];

        $allowedStatus = config("status.{$role}", config('status.all'));

        /* ===================== ADMIN ===================== */
        if ($role === 'admin') {

            $rules['tingkat'] = ['required', 'integer', 'in:1,2,3,4'];

            $rules['nip'] = [
                'required',
                $this->uniqueRule('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nitk'] = [
                'nullable',
                $this->uniqueRule('admins', 'nitk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',
                $this->uniqueRule('admins', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['kode_wilayah'] = [
                'required',
                Rule::in(['IDL', 'PLG']),
            ];

            $rules['status'] = [
                'required',
                Rule::in($allowedStatus),
            ];

        }

        /* ===================== DOSEN ===================== */
        elseif ($role === 'dosen') {

            $rules['tingkat'] = ['required', 'integer', 'in:1,2,3,4,5'];

            $rules['nip'] = [
                'required',
                $this->uniqueRule('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nidn'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidn'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nidk'] = [
                'nullable',
                $this->uniqueRule('dosens', 'nidk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',
                $this->uniqueRule('dosens', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['status'] = [
                'required',
                Rule::in($allowedStatus),
            ];
        }

        /* ===================== MAHASISWA ===================== */
        elseif ($role === 'mahasiswa') {

            $rules['tingkat'] = ['required', 'integer', 'in:5'];

            $rules['nim'] = [
                'required',
                $this->uniqueRule('mahasiswas', 'nim'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
                Rule::unique('mahasiswas', 'nik'),
            ];

            $rules['nik'] = [
                'required',
                'min:14', 'max:16',

                $this->uniqueRule('mahasiswas', 'nik'),
                Rule::unique('admins', 'nip'),
                Rule::unique('admins', 'nitk'),
                Rule::unique('dosens', 'nip'),
                Rule::unique('dosens', 'nidn'),
                Rule::unique('dosens', 'nidk'),
                Rule::unique('mahasiswas', 'nim'),
                Rule::unique('admins', 'nik'),
                Rule::unique('dosens', 'nik'),
            ];

            $rules['kode_wilayah'] = [
                'required',
                Rule::in(['IDL', 'PLG']),
            ];

            $rules['angkatan'] =
                'required|integer|min:1960|max:'.date('Y');

            $rules['status'] = [
                'required',
                Rule::in($allowedStatus),
            ];
        }

        $rules['no_hp'] = [
            'nullable',
            'min:11',
            'max:15',
        ];

        $rules['jenis_kelamin'] = [
            'required',
            Rule::in([
                'Laki-laki',
                'Perempuan',
            ]),
        ];

        $rules['agama'] = [
            'required',
            Rule::in([
                'Islam', 'Kristen', 'Hindu', 'Buddha', 'Katolik', 'Khonghucu', 'Lainnya',
            ]),
        ];

        $rules['tempat_lahir'] = [
            'nullable',
            'max:255',
        ];

        $rules['tanggal_lahir'] = [
            'nullable',
            'date',
        ];

        $rules['pr_id'] = 'required|exists:prodis,id';
        $validator = Validator::make($data, $rules, $this->validationMessagesUser());
        $validator->after(function ($validator) use ($data, $role, $isEditingUser, $selectedUserId, $tingkatType, $authUserId, $authUserTingkat) {
            /* ===================== LOGIKA VALIDASI TINGKAT (ROLE/LEVEL) ===================== */
            $currentUserId = Auth::id() ?? $authUserId;
            $currentUserAdminTingkat = Auth::check()
                ? (int) (Auth::user()?->admin?->tingkat ?? 4)
                : (int) ($authUserTingkat ?? 4);

            $targetTingkatType = (int) ($tingkatType ?? ($role === 'admin' ? 4 : 5));

            if ($isEditingUser && $currentUserId) {
                // 1. Admin tidak bisa mengubah tingkatnya sendiri (diubah ke $currentUserId dan $selectedUserId)
                if ((int) $currentUserId === (int) $selectedUserId && isset($data['tingkat'])) {
                    if ((int) $data['tingkat'] !== $targetTingkatType) {
                        $validator->errors()->add(
                            'tingkat',
                            'Anda tidak diizinkan untuk mengubah tingkat akun Anda sendiri!'
                        );
                    }
                }

                // 2. Admin di tingkat lebih rendah tidak bisa mengubah tingkatan Admin yang lebih tinggi
                if ($role === 'admin') {
                    if ($currentUserAdminTingkat > $targetTingkatType) {
                        if (isset($data['tingkat']) && (int) $data['tingkat'] !== $targetTingkatType) {
                            $validator->errors()->add(
                                'tingkat',
                                'Anda tidak memiliki otoritas untuk mengubah tingkat Admin yang berkedudukan lebih tinggi dari Anda!'
                            );
                        }
                    }
                }

                // 4. Memastikan minimal ada 1 Admin dengan tingkat 1 tersisa di sistem
                if ($role === 'admin' && $targetTingkatType === 1) {
                    if (isset($data['tingkat']) && (int) $data['tingkat'] !== 1) {
                        $totalAdminTingkatSatu = Admin::where('tingkat', 1)->count();

                        if ($totalAdminTingkatSatu <= 1) {
                            $validator->errors()->add(
                                'tingkat',
                                'Perubahan gagal! Sistem wajib memiliki minimal 1 pengguna dengan Admin Tingkat 1.'
                            );
                        }
                    }
                }
            }

            /* ===================== LOGIKA DUPLIKASI IDENTITAS ===================== */
            if ($role === 'admin') {
                if (! empty($data['nip']) && ! empty($data['nitk']) && $data['nip'] === $data['nitk'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nitk',
                        'NITK tidak boleh memiliki nilai yang sama dengan NIP!'
                    );
                }

            } elseif ($role === 'dosen') {
                if (! empty($data['nip']) && ! empty($data['nidn']) && $data['nip'] === $data['nidn'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidn',
                        'NIDN tidak boleh memiliki nilai yang sama dengan NIP dan NIDK!'
                    );
                }

                if (! empty($data['nip']) && ! empty($data['nidk']) && $data['nip'] === $data['nidk'] && $data['nip'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );
                }

                if (! empty($data['nidn']) && ! empty($data['nidk']) && $data['nidn'] === $data['nidk'] && $data['nidn'] === $data['nik']) {
                    $validator->errors()->add(
                        'nidk',
                        'NIDK tidak boleh memiliki nilai yang sama dengan NIP dan NIDN!'
                    );
                }
            }

        });

        return $validator->validate();
    }

    public function validationMessagesUser()
    {
        return [
            'email.required' => 'Alamat Email wajib diisi!',
            'email.email' => 'Format email tidak valid!',
            'email.unique' => 'Email ini sudah terdaftar di sistem!',

            'tingkat.required' => 'Tingkat Role wajib diisi!',
            'tingkat.integer' => 'Format Tingkat Role tidak valid!',
            'tingkat.in' => 'Tingkat Role yang dipilih tidak sesuai dengan kategori yang diizinkan!',

            'password.required' => 'Password wajib diisi!',
            'password.min' => 'Password minimal harus 8 karakter!',
            'name.required' => 'Nama lengkap wajib diisi!',
            'name.max' => 'Nama tidak boleh lebih dari 255 karakter!',
            'nip.required' => 'NIP wajib diisi untuk Admin dan Dosen!',
            'nip.unique' => 'NIP ini sudah terdaftar!',
            'nip.min' => 'NIP minimal 8 karakter!',
            'nip.max' => 'NIP maksimal 20 karakter!',
            'nitk.unique' => 'NITK ini sudah terdaftar!',
            'nitk.min' => 'NITK minimal 8 karakter!',
            'nitk.max' => 'NITK maksimal 20 karakter!',
            'nidn.unique' => 'NIDN ini sudah terdaftar!',
            'nidn.min' => 'NIDN minimal 8 karakter!',
            'nidn.max' => 'NIDN maksimal 20 karakter!',
            'nidk.unique' => 'NIDK ini sudah terdaftar!',
            'nidk.min' => 'NIDK minimal 8 karakter!',
            'nidk.max' => 'NIDK maksimal 20 karakter!',
            'nim.required' => 'NIM wajib diisi untuk Mahasiswa!',
            'nim.unique' => 'NIM ini sudah terdaftar!',
            'nim.min' => 'NIM minimal 8 karakter!',
            'nim.max' => 'NIM maksimal 20 karakter!',
            'nik.required' => 'NIK wajib diisi!',
            'nik.unique' => 'NIK ini sudah terdaftar!',
            'nik.min' => 'NIK minimal harus 12 karakter!',
            'nik.max' => 'NIK maksimal 16 karakter!',
            'no_hp.min' => 'Nomor Telepon minimal harus 11 digit!',
            'no_hp.max' => 'Nomor Telepon maksimal 15 digit!',
            'angkatan.required' => 'Tahun angkatan wajib diisi!',
            'angkatan.integer' => 'Tahun angkatan harus berupa angka!',
            'angkatan.min' => 'Tahun angkatan tidak boleh kurang dari tahun 1960!',
            'angkatan.max' => 'Tahun angkatan tidak boleh melebihi tahun sekarang!',
            'pr_id.required' => 'Program Studi wajib dipilih!',
            'pr_id.integer' => 'ID Program Studi harus berupa angka!',
            'pr_id.exists' => 'Program Studi yang dipilih tidak tersedia!',
            'excel_user_file.required' => 'File Excel Data Pengguna wajib diunggah!',
            'excel_user_file.array' => 'Format unggahan tidak valid!',
            'excel_user_file.*.file' => 'Salah satu file Excel Data Pengguna harus berupa file yang valid!',
            'excel_user_file.*.mimes' => 'Setiap file Excel Data Pengguna harus berformat .xlsx atau .xls!',
            'excel_user_file.*.max' => 'Ukuran masing-masing file tidak boleh lebih dari 27 MB!',
            'excel_user_file.uploaded' => 'File Excel Data Pengguna gagal diunggah!',
            'excel_user_file.*.uploaded' => 'File Excel Data Pengguna gagal diunggah!',
            'kode_wilayah.required' => 'Kode Wilayah untuk Admin & Mahasiswa wajib dipilih!',
            'kode_wilayah.in' => "Kode Wilayah hanya boleh 'IDL' & 'PLG'!",
            'status.required' => 'Status pengguna wajib dipilih!',
            'status.in' => 'Status yang dipilih tidak sesuai dengan kategori yang diizinkan!',

            'tempat_lahir.max' => 'Tempat Lahir tidak boleh lebih dari 255 karakter!',
            'tanggal_lahir.date' => 'Format Tanggal Lahir tidak valid!',
            'agama.required' => 'Agama wajib diisi!',
            'agama.in' => 'Agama yang dipilih tidak sesuai dengan kategori yang diizinkan!',
            'jenis_kelamin.required' => 'Gender wajib diisi!',
            'jenis_kelamin.in' => 'Gender hanya boleh Laki-laki atau Perempuan!',

            'user_input.role.in' => 'Role utama harus berupa Admin, Dosen, atau Mahasiswa!',
            'user_input.update_or_create.required_if' => 'Pilih acuan pembaruan data jika mode Update or Create diaktifkan!',
            'user_input.update_or_create.in' => 'Pilihan acuan pembaruan data tidak valid!',
        ];
    }
}
