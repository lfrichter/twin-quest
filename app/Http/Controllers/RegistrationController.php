<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration; // Assuming your model is App\Models\Registration
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class RegistrationController extends Controller
{
    /**
     * Display the registration form.
     */
    public function create(): InertiaResponse
    {
        // Renders the Vue component located at resources/js/Pages/Registrations/Create.vue
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
                    'name' => $request->validated()['name'],
                    'email' => $request->validated()['email'],
                    'password' => Hash::make($request->validated()['password']),
                ]);
            });

            return redirect()->route('registrations.create')
                ->with('success', 'Registro realizado com sucesso!'); // Flash message for success

        } catch (\Throwable $th) {
            // Log the error or handle it as needed
            // For simplicity, redirecting back with a generic error
            // In a real application, you might want more specific error handling
            report($th); // Log the exception

            return redirect()->back()
                ->withInput() // Send back the old input
                ->with('error', 'Ocorreu um erro durante o registro. Por favor, tente novamente.');
        }
    }
}
