<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->get();
        return view('settings.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            Category::create([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name)
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to create category'], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();
            
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name)
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update category'], 500);
        }
    }

    public function destroy(Category $category)
    {
        try {
            DB::beginTransaction();
            
            // Check if category has products
            if ($category->products()->count() > 0) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot delete category with associated products'
                ], 422);
            }

            $category->delete();
            
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete category'], 500);
        }
    }
} 