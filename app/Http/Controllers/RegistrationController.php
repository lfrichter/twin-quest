<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        // Returns the Inertia view for the registration form
        return Inertia::render('Registrations/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Basic storage logic without validation for now
        // Validation will be added later

        $data = $request->only(['name', 'email']);

        Registration::create($data);

        // Redirect back to the create form with a success message
        return redirect()->route('registrations.create')->with('success', 'Registration submitted successfully!');
    }
}
