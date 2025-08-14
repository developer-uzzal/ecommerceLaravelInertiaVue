<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Inertia\Inertia;
use App\FileUploadMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $user = User::get();

        return $user;
    }

    function loginPage()
    {
        return Inertia::render('Admin/Login');
    }
    public function login(Request $request)
    {

        try {
            
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $request->email)
            ->where('type', 'admin')
            ->orWhere('type', 'user')
            ->first();

            
            if(!$user){
                return back()->with('error', ['status' => 'error', 'message' => 'User not found'], 200);
            }else{

                if(Hash::check($request->password, $user->password)){

                    $request->session()->put('userId', $user->id);

                    $user = $user->makeHidden(['password']);

                    $request->session()->put('user', $user);

                    return redirect()->route('dashboard')->with('success', ['status' => 'success', 'message' => 'Login successfully']);
                }else{
                    return back()->with('error', ['status' => 'error', 'message' => 'Password not match'], 200);
                }
               
            }
        } catch (Exception $e) {

            return back()->with('error', ['status' => 'error', 'message' => $e->getMessage()],200);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function userCreate(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6|same:password',
                'image' => 'nullable',
            ]);

            $slug = str_replace(' ', '-', strtolower($request->name));

            if($user = User::where('username', $slug)->first()){
                $slug = $slug.'-'.time();
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $slug, 'users', 'public');
            }


            User::create([
                'name' => $request->name,
                'username' => $slug,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $imagePath??null
            ]);

            return response()->json([
                'status' => 'success',
                'message' => "User created successfully"
            ], 201);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 201);
        }
    }

    function userLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 200);
            }

            if (Hash::check($request->password, $user->password)) {

                $token = $user->createToken('auth_token')->plainTextToken;
                $user = $user->makeHidden(['password']);
                return response()->json([
                    'status' => 'success',
                    'message' => 'User logged in successfully',
                    'user' => $user,
                    'token' => $token
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid password'
                ]);
            }

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
    }


    public function userUpdate(Request $request, $id)
    {

        try {

            $request->validate([
                'password' => 'required|string|min:6',
                'password_confirmation' => 'required|string|min:6|same:password',
                'image' => 'nullable',

            ]);

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 200);
            }

            $slug = str_replace(' ', '-', strtolower($request->name));

            $imagePath = null;
            if (!$request->hasFile('image')) {
              $imagePath = $request->oldImage ?? null;  
            }
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $slug, 'users', 'public', $request->oldImage);
            }


            $user = User::where('id', $id)->update([

                'password' => Hash::make($request->password),
                'image' => $imagePath
            ]);

            

            return response()->json([
                'status' => 'success',
                'message' => "profile updated successfully"
            ], 201);

        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
        
    }

    function userById($username)
    {
        $user = User::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => $user
        ]);
        
    }

     public function update(Request $request, $id)
    {

        try {

            $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email,' . $id,

                'password' => 'required|string|min:6',
                'image' => 'nullable',

            ]);

            $slug = str_replace(' ', '-', strtolower($request->name));

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 200);
            }

            $imagePath = null;
            if (!$request->hasFile('image')) {
              $imagePath = $request->oldImage;  
            }
            if ($request->hasFile('image')) {
                $imagePath = FileUploadMedia::upload($request->file('image'), $slug, 'users', 'public', $user->image);
            }


            $user = User::where('id', $id)->update([
                'name' => $request->name,
                'username' => $slug,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'image' => $imagePath
            ]);

            return response()->json([
                'status' => 'success',
                'message' => $user
            ], 201);
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 200);
        }
        
    }

    
    public function destroy(Request $request, $id)
    {

        try {

            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }else{

                if ($user->image) {
                    Storage::disk('public')->delete($user->image);
                }

                $user->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => $user
                ], 201);
            }

            
        } catch (Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);            
        }
        
    }

    function logout(Request $request)
    {
        $request->session()->forget('userId');
        return redirect()->route('login');
    }
}
