<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[Signature('app:create-admin')]
#[Description('Administratzaile kontu berri bat sortzen du')]
class CreateAdmin extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text(label: 'Izena', required: true);

        $email = text(
            label: 'Posta elektronikoa',
            required: true,
            validate: ['email' => ['email', 'unique:users,email']],
        );

        $password = password(
            label: 'Pasahitza',
            required: true,
            validate: ['password' => Password::min(10)],
        );

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => Role::Admin,
        ]);

        $this->components->info("Administratzailea sortuta: {$email}");

        return self::SUCCESS;
    }
}
