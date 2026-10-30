<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::orderByDesc('is_active')->orderBy('name')->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('users.create', [
            'user' => new User(['role' => Role::Irakaslea, 'is_active' => true]),
            'roles' => $this->roleOptions(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('users.index')->with('status', 'Erabiltzailea sortuta.');
    }

    public function edit(User $user): View
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roleOptions(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->validated();

        if (blank($attributes['password'])) {
            unset($attributes['password']);
        }

        $user->update($attributes);

        return redirect()->route('users.index')->with('status', 'Erabiltzailea eguneratuta.');
    }

    /**
     * @return array<string, string>
     */
    private function roleOptions(): array
    {
        return collect(Role::cases())->mapWithKeys(fn (Role $role) => [$role->value => $role->label()])->all();
    }
}
