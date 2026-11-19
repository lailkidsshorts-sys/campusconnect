<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'roll_no' => strtoupper($request->validated('roll_no')),
            'email' => strtolower($request->validated('email')),
            'password' => Hash::make($request->validated('password')),
            'role' => UserRole::STUDENT,
            'department_id' => null,
        ]);

        Auth::login($user);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome to CampusConnect.');
    }
}
