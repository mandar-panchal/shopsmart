<?php

namespace App\Http\Controllers\authorization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    //
    public function index(){
       
        $pageConfigs = ['pageHeader' => false];
        return view('authorization/permission', ['pageConfigs' => $pageConfigs]);
    }
    public function getdata(){
        $permissions = Permission::with('roles')->get();

        // You can customize the data structure if needed
        $formattedPermissions = $permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
                'created_at' => $permission->created_at,
                'roles' => $permission->roles->pluck('name')->toArray(),
                // Add other properties as needed
            ];
        });
    
        return response()->json(['data' => $formattedPermissions]);
        // $permissions = Permission::all();
        // return response()->json(['data' => $permissions]);
    }

        public function getPermissions()
        {
            $permissions = Permission::all();
            return response()->json($permissions);
        }
    public function create(Request $request){
        $validator = $request->validate([
            'name' => ['required', 'string', 'unique:permissions', 'regex:/^\S*$/u']
        ]);
    
        try {
            $permission = Permission::create($validator);
            $response = [
                'success' => true,
                'message' => 'Permission created successfully',
                'data' => $permission,
            ];
        } catch (\Exception $e) {
            $response = [
                'success' => false,
                'message' => 'Error creating permission',
                'error' => $e->getMessage(),
            ];
        }
    
        return response()->json($response);
    }

    public function edit($permissionId)
    {
        $permission = Permission::find($permissionId);

        return response()->json(['success' => true, 'data' => $permission]);
    }

    public function update(Request $request, $permissionId)
    {
        $permission = Permission::find($permissionId);

        $request->validate([
            'editPermissionName' => ['required', 'string', 'unique:permissions,name,' . $permission->id, 'regex:/^\S*$/u'],
        ]);

        $permission->update(['name' => $request->editPermissionName]);

        return response()->json(['success' => true, 'message' => 'Permission updated successfully', 'data' => $permission]);
    }
    public function delete($permissionId)
    {
        // Find and delete the permission
        Permission::findOrFail($permissionId)->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
