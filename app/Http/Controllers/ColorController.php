<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    function index(){
        
        $colors = Color::get();

        return response()->json([
            'status' => 'success',
            'message' => $colors
        ]);
    }

    function show($id){
        $color = Color::find($id);
        return response()->json([
            'status' => 'success',
            'message' => $color
        ]);
    }

    function store(Request $request){
        
        try{

            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
            ]);

            $name = $request->name;
            $code = $request->code;
            $status = $request->status;

            $color = Color::where('name', $name)->first();
            if($color){
                return response()->json([
                    'status' => 'error',
                    'message' => "Color already exists"
                ],200);
            }

            Color::create([
                'name' => $name,
                'code' => $code,
                'status' => $status ?? 0,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "Color created successfully"
            ]);

        }catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }

    function update(Request $request, $id){
        try{
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
            ]);

            $name = $request->name;
            $code = $request->code;
            $status = $request->status;

            $color = Color::find($id);
            $color->name = $name;
            $color->code = $code;
            $color->status = $status ?? 0;
            $color->save();

            return response()->json([
                'status' => 'success',
                'message' => "Color updated successfully"
            ]);

        }catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }

    function destroy($id){
        try{
            $color = Color::find($id);
            $color->delete();
            return response()->json([
                'status' => 'success',
                'message' => "Color deleted successfully"
            ]);
        }catch(Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],200);
        }
    }
}
