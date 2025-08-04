<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function createTestUser()
    {
        // Check if test user exists
        $existingUser = User::where('username', 'test')->first();

        if ($existingUser) {
            $message = "Test user already exists!";
            $details = [
                'Username' => 'test',
                'Password' => 'test',
            ];
        } else {
            // Create test user
            $user = new User();
            $user->username = 'test';
            $user->password = Hash::make('test');
            $user->email = 'test@example.com';
            $user->firstname = 'Test';
            $user->lastname = 'User';
            $user->role = 'admin';

            if ($user->save()) {
                $message = "Test user created successfully!";
                $details = [
                    'Username' => 'test',
                    'Password' => 'test',
                    'User ID' => $user->id,
                ];
            } else {
                $message = "Failed to create test user.";
                $details = [];
            }
        }

        return view('setup.result', compact('message', 'details'));
    }
}
