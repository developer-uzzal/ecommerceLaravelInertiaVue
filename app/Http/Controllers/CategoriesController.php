<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\FileUploadMedia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function allCategories()
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Category/Category', compact('categories'));
    }
    public function index()
    {
        $categories = Cache::remember('all_categories', 60 * 24, function () {
            return Category::all();
        });

        return response()->json([
            'status' => 'success',
            'message' => $categories
        ], 200);
    }

    public function indexHome()
    {
        $categories = Cache::remember('home_categories', 60 * 24, function () {
            return Category::where('is_active', 1)->limit(10)->get();
        });

        return response()->json([
            'status' => 'success',
            'message' => $categories
        ], 200);
    }



    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'required|image',
                'status' => 'required'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $request->name, 'categories', 'public');
            }

            Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'is_active' => $request->status ? 1 : 0,
                'image' => $imagePath,
            ]);

            return back()->with('success', ['status' => 'success', 'message' => 'Category created successfully']);
        } catch (Exception $e) {

            return back()->with('error', ['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 404);
        } else {

            return response()->json([
                'status' => 'success',
                'message' => $category
            ], 200);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found'
                ], 200);
            }

            $imagePath = $category->image;

            if ($request->hasFile('image')) {
                // if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                //     Storage::disk('public')->delete($imagePath);
                // }

                $imagePath1 = FileUploadMedia::upload($request->file('image'), $request->name, 'categories', 'public', $category->image);

                if ($imagePath1) {
                    $imagePath = $imagePath1;
                }
            }

            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'is_active' => $request->status ? 1 : 0,
                'image' => $imagePath,
            ]);

            return back()->with('success', ['status' => 'success', 'message' => 'Category updated successfully']);
        } catch (Exception $e) {

            return back()->with('error', ['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {

            $category = Category::find($id);

            if (!$category) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Category not found'
                ]);
            } else {

                $imagePath = $category->image;
                $category->delete();
                if ($imagePath) {
                    $imagePath = str_replace('storage/', '', $imagePath);
                    Storage::disk('public')->delete($imagePath);
                }

                return back()->with('success', ['status' => 'success', 'message' => 'Category deleted.']);
            }
        } catch (Exception $e) {

            return back()->with('error', ['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
