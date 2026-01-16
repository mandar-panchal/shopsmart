<?php

namespace App\Http\Controllers\authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use app\Models\User;



class RolesController extends Controller
{
    public function index(){
        $roles = Role::all();
        $permissions = Permission::all();
        $users = User::all();
        $pageConfigs = ['pageHeader' => false];
        return view('authorization/roles', ['roles' => $roles,'permissions' => $permissions,'users'=>$users,'pageConfigs' => $pageConfigs]);
    }
    
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name|string|regex:/^\S*$/u',
            'permissions' => 'required|array',
        ]);
    
        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->permissions);
    
        if ($request->ajax()) {
            return response()->json(['message' => 'Role created successfully.']);
        }
    
        return redirect()->route('permissions')->with('success', 'Role created successfully.');
    }

    public function getRoles()
        {
            $roles = Role::all(); // Assuming you have a Role model

            return response()->json(['roles' => $roles]);
        }
    public function getRoleDetails($id)
        {
            $role = Role::find($id);

            // Return role details as JSON
            return response()->json([
                'name' => $role->name,
                'permissions' => $role->permissions->toArray(),
            ]);
        }
        public function edit(Request $request, $id)
        {
             $request->validate([
                'name' => 'required|unique:roles,name,' . $id,
                'permissions' => 'required|array',
            ]);
            $role = Role::find($id);
            $role->update([
                'name' => $request->name,
            ]);
            $role->syncPermissions($request->permissions);
    
            return response()->json(['message' => 'Role updated successfully.']);
        }
}
