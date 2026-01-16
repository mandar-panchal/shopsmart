<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DataTables;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Notifications\RoleUpdateNotification;
use Illuminate\Support\Facades\DB;


class UserController extends Controller
{    
    public function getUsers(Request $request)
    {
        $query = User::query();

        // Apply search term to multiple columns
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('extension', 'like', '%' . $searchTerm . '%')
                    ->orWhere('lead_target', 'like', '%' . $searchTerm . '%')
                    ->orWhere('incoming_visit_target', 'like', '%' . $searchTerm . '%')
                    ->orWhere('popup_visit_target', 'like', '%' . $searchTerm . '%')
                    ->orWhere('telecalling_target', 'like', '%' . $searchTerm . '%');
                
            });
        }

        return DataTables::of($query)
            ->addColumn('roles', function (User $user) {
                return $user->getRoleNames()->implode(', '); 
            })
            ->addColumn('status', function (User $user) {
                return $user->status == 1 ? 'Active' : 'Inactive';
            })
            ->make(true);
    }


    public function updateRole(Request $request, $userId)
    {
        try {
            // Validate the request data
            $request->validate([
                'role_id' => 'required|exists:roles,id',
            ]);

            // Find the user
            $user = User::findOrFail($userId);

            // Find the role
            $role = Role::findOrFail($request->input('role_id'));

            DB::transaction(function () use ($request, $userId) {
                // Find the user
                $user = User::findOrFail($userId);

                // Find the role
                $role = Role::findOrFail($request->input('role_id'));

                // Sync the user's roles using Spatie
                $user->syncRoles([$role->name]);

                // Notify the user
                $user->notify(new RoleUpdateNotification($role->name));
            });

            return response()->json(['success' => true, 'message' => 'User role updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // public function updateTheme(Request $request)
    // {
    //     $user = Auth::user();
    //     $user->theme = $request->input('theme');
    //     $user->save();    
    //     return response()->json(['status' => 'success']);
    // }
    public function updateThemeMode(Request $request)
    {
        $themeMode = $request->input('themeMode');
        $allowedThemes = ['light-layout', 'dark-layout', 'semi-dark-layout'];
         if (!in_array($themeMode, $allowedThemes)) {
             return response()->json(['status' => 'error', 'message' => 'Invalid theme mode']);
         }
         Auth::user()->update(['theme' => $themeMode]);
         return response()->json(['status' => 'success', 'message' => 'Theme updated successfully', 'reload' => true]);
    }

    public function getCurrentTheme()
    {
        $currentTheme = Auth::user()->theme;
        return response()->json(['currentTheme' => $currentTheme]);
    }

    public function createuser(Request $request)
    {
        $input = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username'=>['required', 'string','max:255','unique:users','regex:/^\S*$/u'],
            'password' => ['required','min:8','max:25'],
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'username'=> $request['username'],
            'password' => Hash::make($request['password']),
            'extension'=> $request['extension'],
            'lead_target'=> $request['lead_target'],
            'incoming_visit_target'=> $request['incoming_visit_target'],
            'popup_visit_target'=> $request['popup_visit_target'],
            'telecalling_target'=> $request['telecalling_target'],
        ]);

        $user->assignRole($request['role']);

        return response()->json(['message' => 'User created successfully.']);

    }
    

    }
