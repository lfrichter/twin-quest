<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Http\Requests\ValidateEmailRequest;
use App\Models\Registration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Throwable;

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
        try {
            DB::transaction(function () use ($request) {
                Registration::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);
            });
        } catch (Throwable $e) {
            Log::error('Error during registration process: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('registrations.create')
                ->with('error', 'An unexpected error occurred while processing your registration. Please try again.');
        }

        return redirect()->route('main.index')
            ->with('success', 'Registration successful!');
    }

    /**
     * Validate if the email already exists in the registrations table.
     */
    public function validateEmail(ValidateEmailRequest $request): JsonResponse
    {
        return response()->json(['exists' => false]);
    }
}
