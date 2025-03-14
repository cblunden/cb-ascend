<?php

namespace App\Http\Controllers\Auth;

use App\Actions\RegisterUser;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = RegisterUser::dispatchSync($request->only(
            'name', 'email', 'password', 'password_confirmation', 'library_id'
        ));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
