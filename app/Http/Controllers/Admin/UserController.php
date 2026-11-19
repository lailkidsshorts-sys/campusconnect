<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('department')->withCount('complaints');

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('roll_no', 'like', "%{$s}%");
            });
        }

        $users = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', Rule::in(['student', 'dept_admin', 'super_admin'])],
            'department_id' => ['nullable', 'required_if:role,dept_admin', 'exists:departments,id'],
            'roll_no' => ['nullable', 'required_if:role,student', 'string', 'max:50', 'unique:users,roll_no'],
        ]);

        User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department_id' => $request->role === 'dept_admin' ? $request->department_id : null,
            'roll_no' => $request->role === 'student' ? strtoupper(trim($request->roll_no)) : null,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User account created successfully!');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', Rule::in(['student', 'dept_admin', 'super_admin'])],
            'department_id' => ['nullable', 'required_if:role,dept_admin', 'exists:departments,id'],
            'roll_no' => ['nullable', 'string', 'max:50', 'unique:users,roll_no,' . $user->id],
        ]);

        $data = [
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'role' => $request->role,
            'department_id' => $request->role === 'dept_admin' ? $request->department_id : null,
            'roll_no' => $request->role === 'student' ? strtoupper(trim($request->roll_no)) : null,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own active administrator account.');
        }

        if ($user->complaints()->exists()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot delete user: Active complaints are linked to this student.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
