<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Brand;
use App\FileUploadMedia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BrandsController extends Controller
{

    function allBrands() {
        $brands = Brand::all();
        return response()->json([
            'status' => 'success',
            'message' => $brands
        ], 200);
    }
    public function index()
{
    $brands = Cache::remember('active_brands', 60 * 24, function () {
        return Brand::where('is_active', 1)->get();
    });

    return response()->json([
        'status' => 'success',
        'message' => $brands
    ], 200);
}

    public function store(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'isActive' => 'required',
                'image' => 'required|image',
            ]);
    
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $request->name, 'brands', 'public');
            }
    
            Brand::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'image' => $imagePath,
                'is_active' => $request->isActive ? 1 : 0
            ]);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Brand created.'
            ]);

        }catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'isActive' => 'required',
            'image' => 'nullable',
        ]);

        $Brand = Brand::find($id);

        if (!$Brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ], 200);
        }

        $imagePath = $Brand->oldImage;
        if ($request->hasFile('image')) {
           $imagePath1 = FileUploadMedia::upload($request->file('image'), $request->name, 'brands', 'public', $Brand->image);

           if ($imagePath1) {
            $imagePath = $imagePath1;
           }
        }

        $Brand->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'image' => $imagePath ?? $Brand->image,
            'is_active' => $request->isActive ? 1 : 0
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Brand updated.'
        ]);
       
    }

    public function show($id)
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found'
            ], 200);
        }else{

            return response()->json([
                'status' => 'success',
                'message' => $brand
            ], 200);
        }
    }

    public function destroy($id)
    {
        try {

            $Brand = Brand::find($id);

        if (!$Brand) {
            return response()->json([
                'status' => 'error',
                'message' => 'Brand not found'
            ]);
        }else{

           $imagePath = $Brand->image;
            $Brand->delete();
           if ($imagePath) {
               $imagePath = str_replace('storage/', '', $imagePath);
               Storage::disk('public')->delete($imagePath);
           }

           return response()->json([
               'status' => 'success',
               'message' => 'Brand deleted.'
           ]);
        }

        }catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }

    }


}
