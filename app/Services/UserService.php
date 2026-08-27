<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    public function listAll(): Collection
    {
        return User::query()->orderBy('id')->get();
    }

    /**
     * @param  array{name: string, email: string, password: string, role: string}  $data
     */
    public function create(array $data): User
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);
    }

    /**
     * @param  array{name: string, email: string, password?: ?string, role: string}  $data
     */
    public function update(User $user, array $data): User
    {
        if ($user->role === 'admin' && $data['role'] === 'customer' && $this->adminCount() <= 1) {
            throw ValidationException::withMessages([
                'role' => 'At least one admin account must remain in the system.',
            ]);
        }

        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return $user->fresh();
    }

    public function delete(User $user, ?int $actingUserId): void
    {
        if ($actingUserId !== null && $actingUserId === $user->id) {
            throw ValidationException::withMessages([
                'user' => 'You cannot delete your own account while logged in.',
            ]);
        }

        if ($user->role === 'admin' && $this->adminCount() <= 1) {
            throw ValidationException::withMessages([
                'user' => 'At least one admin account must remain in the system.',
            ]);
        }

        $user->delete();
    }

    public function adminCount(): int
    {
        return User::query()->where('role', 'admin')->count();
    }
}
