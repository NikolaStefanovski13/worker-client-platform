<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Skill;
use App\Models\ServiceArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $skills = Skill::orderBy('category')->orderBy('name')->get();

        // Group skills by category for easy display
        $skillsByCategory = $skills->groupBy('category');

        return view('profile.edit', [
            'user' => $user,
            'skillsByCategory' => $skillsByCategory,
            'userSkills' => $user->skills->pluck('id')->toArray(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Validate the basic profile data
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'bio' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'service_radius' => ['nullable', 'integer', 'min:1', 'max:100'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
            'profile_image' => ['nullable', 'image', 'max:1024'],
        ]);

        // Update user data
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle profile image upload if provided
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            // Store the new image
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->update(['profile_image' => $path]);
        }

        // Update profile data
        $user->profile()->update([
            'bio' => $validated['bio'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'service_radius' => $user->isWorker() ? ($validated['service_radius'] ?? null) : null,
        ]);

        // Update skills for workers
        if ($user->isWorker() && isset($validated['skills'])) {
            $user->skills()->sync($validated['skills']);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Add a service area for workers
     */
    public function addServiceArea(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user->isWorker()) {
            return Redirect::route('profile.edit')->with('error', 'Only workers can add service areas.');
        }

        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:20'],
        ]);

        $user->serviceAreas()->create($validated);

        return Redirect::route('profile.edit')->with('status', 'service-area-added');
    }

    /**
     * Remove a service area
     */
    public function removeServiceArea(ServiceArea $serviceArea): RedirectResponse
    {
        $user = Auth::user();

        if ($serviceArea->user_id !== $user->id) {
            return Redirect::route('profile.edit')->with('error', 'You do not have permission to remove this service area.');
        }

        $serviceArea->delete();

        return Redirect::route('profile.edit')->with('status', 'service-area-removed');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
