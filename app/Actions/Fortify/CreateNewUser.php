<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'username'=>['required', 'max:255','unique:users','regex:/^\S*$/u'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'username' => $input['username'],
                'password' => Hash::make($input['password']),
                'extension' => $input['extension'],
                'lead_target' => $input['lead_target'],
                'incoming_visit_target' => $input['incoming_visit_target'],
                'popup_visit_target' => $input['popup_visit_target'],
                'telecalling_target' => $input['telecalling_target'],
            ]);
    
            $user->assignRole($input['role']);
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
    
            return response()->json(['error' => true]);
        }
    }
}
