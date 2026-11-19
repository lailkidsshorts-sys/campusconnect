<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Department;
use App\Models\IssueType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['department', 'issueTypes'])
            ->withCount(['complaints', 'issueTypes'])
            ->orderBy('name')
            ->get();

        $departments = Department::orderBy('name')->get();

        return view('admin.categories.index', compact('categories', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        Category::create([
            'department_id' => $request->department_id,
            'name' => trim($request->name),
            'slug' => $slug,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'department_id' => ['required', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
        ]);

        $category->update([
            'department_id' => $request->department_id,
            'name' => trim($request->name),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->complaints()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category: Complaints exist under this category.');
        }

        $category->issueTypes()->delete();
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category and its issue types deleted successfully.');
    }

    // Issue Type quick creation & deletion under category
    public function storeIssueType(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
        ]);

        $category->issueTypes()->create([
            'name' => trim($request->name),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Issue type added to ' . $category->name . ' successfully!');
    }

    public function destroyIssueType(IssueType $issueType): RedirectResponse
    {
        if ($issueType->complaints()->exists()) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete issue type: Existing complaints reference this issue.');
        }

        $issueType->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Issue type removed successfully.');
    }
}
