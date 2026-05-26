<?php

namespace App\Actions\Fortify;

use App\Actions\Teams\CreateTeam;
use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\Auth\Admin;
use App\Models\Auth\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(private CreateTeam $createTeam)
    {
        //
    }

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    // public function create(array $input): User
    // {
    //     Validator::make($input, [
    //         ...$this->profileRules(),
    //         'password' => $this->passwordRules(),
    //     ])->validate();

    //     return DB::transaction(function () use ($input) {
    //         $user = User::create([
    //             'name' => $input['name'],
    //             'email' => $input['email'],
    //             'password' => $input['password'],
    //         ]);

    //         $this->createTeam->handle($user, $user->name."'s Team", isPersonal: true);

    //         return $user;
    //     });
    // }

    public function create(array $input): User
    {
        $adminKey = env('ADMIN_KEY', 'nrgKnSD$ZJP9sUh');

        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'admin_key' => ['nullable', 'string'],
        ])->after(function ($validator) use ($input, $adminKey) {
            if (! empty($input['admin_key']) && $input['admin_key'] !== $adminKey) {
                $validator->errors()->add(
                    'admin_key',
                    'Kunci admin tidak valid.'
                );
            }
        })->validate();

        $isAdmin = (! empty($input['admin_key']) && $input['admin_key'] === $adminKey);

        return DB::transaction(function () use ($input, $isAdmin) {
            $user = User::create([
                'email' => $input['email'],
                'password' => Hash::make($input['password']),
            ]);

            if ($isAdmin) {
                Admin::create([
                    'user_id' => $user->id,
                    'name' => $input['name'],
                    'pr_id' => null,
                    'nip' => '03041282227063',
                    'nik' => '03041282227063'
                ]);
            }

            $this->createTeam->handle($user, $input['name']."'s Team", isPersonal: true);

            return $user;
        });
    }
}
