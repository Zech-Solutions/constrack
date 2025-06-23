<?php

namespace App\Http\Controllers;

use App\Models\PreRegistration;
use Illuminate\Http\Request;

class PreRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:preregistrations,email',
            'contact_number' => 'nullable|string|max:20',
        ]);

        PreRegistration::create($validated);

        return back()->with('success', 'Thank you for signing up!');
    }
}
