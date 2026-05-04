<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        // Original code preserved as requested:
        // return User::create([
        //     'name' => $input['name'],
        //     'email' => $input['email'],
        //     'password' => $input['password'],
        // ]);

        // Laravel/Eloquent adaptation for the new User model fields:
        return User::create([
            'fullname' => $input['fullname'] ?? null,
            'firstname' => $input['firstname'] ?? null,
            'lastname' => $input['lastname'] ?? null,
            'extname' => $input['extname'] ?? null,
            'email' => $input['email'],
            'password' => $input['password'],
            'job_title' => $input['job_title'] ?? null,
            'role' => $input['role'] ?? 'user', // Set default or input role
        ]);
    }
}
