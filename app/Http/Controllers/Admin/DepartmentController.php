<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount(['categories', 'complaints', 'admins'])
            ->orderBy('name')
            ->get();

        return view('admin.departments.index', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name'],
            'admin_title' => ['required', 'string', 'max:100'],
        ]);

        $slug = Str::slug($request->name);
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while (Department::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        Department::create([
            'name' => trim($request->name),
            'slug' => $slug,
            'admin_title' => trim($request->admin_title),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully!');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:departments,name,' . $department->id],
            'admin_title' => ['required', 'string', 'max:100'],
        ]);

        $department->update([
            'name' => trim($request->name),
            'admin_title' => trim($request->admin_title),
        ]);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->complaints()->exists()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department: There are active or archived complaints associated with it.');
        }

        if ($department->categories()->exists()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete department: Please remove or reassign its categories first.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
