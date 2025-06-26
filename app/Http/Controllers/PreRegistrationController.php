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
            'domain_name' => 'required|regex:/^[a-zA-Z0-9\-]+$/|unique:preregistrations,domain_name',
            'owner_firstname' => 'required|string|max:255',
            'owner_middlename' => 'nullable|string|max:255',
            'owner_lastname' => 'required|string|max:255',
            'owner_email' => 'required|email',
            'address' => 'required|string',
        ]);
        $validated['domain_name'] .= '.constrack.com';

        PreRegistration::create($validated);

        return back()->with('success', 'Thank you for signing up!');
    }
    public function show()
    {
        return view('signup');
    }
}
