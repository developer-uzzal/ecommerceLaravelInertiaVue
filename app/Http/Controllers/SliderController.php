<?php

namespace App\Http\Controllers;

use Exception;
use App\FileUploadMedia;
use Illuminate\Http\Request;
use App\Models\ProductSliders;
use Illuminate\Support\Facades\Cache;

class SliderController extends Controller
{
    public function userSlidersAll()
{
    $sliders = Cache::remember('active_product_sliders', 60 * 24, function () {
        return ProductSliders::with(['product:id,slug'])
            ->where('status', 1)
            ->get();
    });

    return response()->json([
        'status' => 'success',
        'message' => $sliders
    ]);
}

    function index()
    {
        $sliders = ProductSliders::get();
        return response()->json([
            'status' => 'success',
            'message' => $sliders
        ]);
    }


    function show($id)
    {
        $slider = ProductSliders::find($id);
        return response()->json([
            'status' => 'success',
            'message' => $slider
        ]);
    }

    function store(Request $request)
    {
        try {

            $request->validate([
                'title' => 'required|string',
                'short_des' => 'required|string',
                'price' => 'required|numeric',
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
                'product_id' => 'required|exists:products,id',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $request->title, 'sliders', 'public');
            }

            $slider = new ProductSliders();
            $slider->product_id = $request->product_id;
            $slider->title = $request->title;
            $slider->short_des = $request->short_des;
            $slider->price = $request->price;
            $slider->image = $imagePath;
            $slider->status = $request->status ? 1 : 0;
            $slider->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Slider created successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string',
                'short_des' => 'required|string',
                'price' => 'required|numeric',
                'product_id' => 'required|exists:products,id',
            ]);

            $slider = ProductSliders::findOrFail($id); // Use $id properly

            $imagePath = $slider->image;


            if ($request->hasFile('image')) {

                if ($slider->image && file_exists(public_path($slider->image))) {
                    unlink(public_path($slider->image));
                }

                $newImagePath = FileUploadMedia::upload($request->file('image'), $request->title, 'sliders', 'public');
                $imagePath = $newImagePath;
            }

            $slider->product_id = $request->product_id;
            $slider->title = $request->title;
            $slider->short_des = $request->short_des;
            $slider->price = $request->price;
            $slider->image = $imagePath;
            $slider->status = $request->status ? 1 : 0;
            $slider->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Slider updated successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }


    function destroy($id)
    {
        try {
            $slider = ProductSliders::find($id);

            if ($slider->image) {
                if ( $slider->image && file_exists(public_path($slider->image))) {
                    unlink(public_path($slider->image));
                }
            }
            $slider->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Slider deleted successfully',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
