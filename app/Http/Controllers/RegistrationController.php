<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request; // Added for Request
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator; // Added for Validator
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RegistrationController extends Controller
{
    /**
     * Show the form for creating a new registration.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Registrations/Create');
    }

    /**
     * Store a newly created registration in storage.
     */
    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            Registration::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        });

        return redirect()->route('registrations.create')
            ->with('success', 'Registration successful!');
    }

    /**
     * Validate if the email already exists in the registrations table.
     */
    public function validateEmail(Request $request): JsonResponse
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $exists = Registration::where('email', $request->email)->exists();

        return response()->json(['exists' => $exists]);
    }
}
