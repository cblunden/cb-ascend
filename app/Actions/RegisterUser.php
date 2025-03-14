<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * @property string $name
 * @property string $email
 * @property string $password
 * @property int    $library_id
 */
class RegisterUser extends Action
{
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'library_id' => ['required', 'int', 'exists:libraries,id']
        ];
    }

    protected function run(): User
    {
        /** @var User $user */
        $user = User::query()->create([
            'membership_number' => Str::uuid()->toString(),
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ]);

        $user->libraries()->syncWithoutDetaching(Arr::wrap($this->library_id));

        return $user;
    }
}
