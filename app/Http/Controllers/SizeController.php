<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    function index()
    {
        $sizes = Size::get();

        return response()->json([
            'status' => 'success',
            'message' => $sizes
        ]);
    }

    function show($id)
    {
        $size = Size::find($id);
        return response()->json([
            'status' => 'success',
            'message' => $size
        ]);
    }

    function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $status = $request->status;

            $size = Size::where('name', $request->name)->first();
            if ($size) {
                return response()->json([
                    'status' => 'error',
                    'message' => "Size already exists"
                ],200);
            }

            $size = Size::create([
                'name' => $request->name,
                'status' => $status ?? 0
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Size created successfully"
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }

    function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $name = $request->name;
            $status = $request->status;

            $size = Size::find($id);
            $size->name = $name;
            $size->status = $status ?? 0;
            $size->save();

            return response()->json([
                'status' => 'success',
                'message' => "Size updated successfully"
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }

    function destroy($id)
    {
        try {
            $size = Size::find($id);
            $size->delete();
            return response()->json([
                'status' => 'success',
                'message' => "Size deleted successfully"
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }

}
