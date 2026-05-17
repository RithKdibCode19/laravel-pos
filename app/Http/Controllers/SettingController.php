<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function index()
    {
        if (auth()->id() !== 1) {
            return back()->with('error', 'You do not have permission to access settings.');
        }

        // You can fetch settings data from the database here if needed
        $settings = []; // Replace with actual settings data
        $categories = Category::withCount('products')->get();

        return view('settings.index', compact('settings', 'categories'));
    }

    public function update(Request $request)
    {
        // Implement settings update logic here
        // For example, validate and save settings to the database

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    /**
     * Show the form for editing the user's profile.
     */
    public function profile()
    {
        return view('settings.profile');
    }

    /**
     * Show the form for changing the user's password.
     */
    public function password()
    {
        return view('settings.password');
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        return redirect()->route('settings.profile')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        auth()->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('settings.password')
            ->with('success', 'Password updated successfully.');
    }
} 