<?php

declare(strict_types=1);

namespace App\Application\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\JWTGuard;

class AuthService
{
    public function register(array $payload): array
    {
        return DB::transaction(function () use ($payload): array {
            $user = new User();
            $user->name = $payload['name'];
            $user->email = $payload['email'];
            $user->password = Hash::make($payload['password']);
            $user->role = 'user';
            $user->save();

            $token = $this->guard()->login($user);
            if (! is_string($token)) {
                abort(500);
            }

            return $this->tokenPayload($token, $user);
        });
    }

    public function login(array $credentials): ?array
    {
        $token = $this->guard()->attempt($credentials);

        if ($token === false) {
            return null;
        }

        $user = $this->guard()->user();
        if (! $user instanceof User) {
            return null;
        }

        if ($user->suspended_at !== null) {
            $this->guard()->logout();
            abort(403, 'Account suspended.');
        }

        return $this->tokenPayload((string) $token, $user);
    }

    public function refresh(): array
    {
        $token = $this->guard()->refresh();

        $user = $this->guard()->user();
        if (! $user instanceof User) {
            $user = User::query()->findOrFail($this->guard()->id());
        }

        return $this->tokenPayload($token, $user);
    }

    public function logout(): void
    {
        $this->guard()->logout();
    }

    public function me(): User
    {
        $user = $this->guard()->user();

        if (! $user instanceof User) {
            abort(401);
        }

        return $user;
    }

    private function tokenPayload(string $token, User $user): array
    {
        $ttlMinutes = (int) $this->guard()->factory()->getTTL();

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttlMinutes * 60,
            'user' => $user,
        ];
    }

    private function guard(): JWTGuard
    {
        $guard = Auth::guard('api');

        if (! $guard instanceof JWTGuard) {
            abort(500);
        }

        return $guard;
    }
}
