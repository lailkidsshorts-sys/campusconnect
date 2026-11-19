<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\EmailNotificationService;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected EmailNotificationService $emailService,
        protected NotificationService $notificationService
    ) {}

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

        // Send welcome email & trigger in-app welcome notification
        $this->emailService->sendWelcomeEmail($user);
        $this->notificationService->notify(
            $user,
            'welcome',
            'Welcome to CampusConnect',
            'Your student account is active. You can now submit complaints and track resolutions.',
            route('student.dashboard')
        );

        return redirect()->route('student.dashboard')->with('success', 'Registration successful! Welcome to CampusConnect.');
    }
}
