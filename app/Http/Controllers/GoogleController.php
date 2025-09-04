<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->redirect();
        } catch (\Exception $e) {
            // Log::error('Google OAuth redirect error: ' . $e->getMessage());
            return redirect('/login')->withErrors(['msg' => 'Cannot connect to Google. Please try again.']);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            // Log::info('Google callback started');

            $googleUser = Socialite::driver('google')->user();

            // Log::info('Google user retrieved: ' . $googleUser->getEmail());

            // verify if the email is verified (optional, can turn it off)
            if (!($googleUser->user['email_verified'] ?? false)) {
                // Log::warning('Email not verified for: ' . $googleUser->getEmail());
                return redirect('/login')->withErrors(['msg' => 'This Google email is not verified.']);
            }

            $user = User::where('email', $googleUser->getEmail())->first();

            $isNewUser = false;

            if (!$user) {
                $randomPassword = Str::random(16);

                // Log::info('Creating new user: ' . $googleUser->getEmail());
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make($randomPassword), // Default password, can be changed later
                    'role_id' => Role::CUSTOMER, // make this user a customer
                ]);

                $isNewUser = true;
                Mail::raw("Hi {$googleUser->getName()}, your temporary password is: {$randomPassword}. Please change it after logging in.", function ($message) use ($googleUser) {
                    $message->to($googleUser->getEmail())
                            ->subject('Welcome to Furniro');
                }); // send welcome email with temporary password
            }

            Auth::guard('web')->login($user, true);

            // Log::info('User logged in successfully: ' . $user->email);

            // If the user is new, redirect to profile edit with a welcome message
            if ($isNewUser) {
                return redirect('/profile')
                    ->with('welcome_message', 'Welcome, ' . $user->name . '! Your account has been created successfully. Please change your password after logging in.');
            }

            // Else redirect to customer categories
            return redirect('/customer/categories');

        } catch (\Exception $e) {
            // Log::error('Google callback error: ' . $e->getMessage());
            // Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect('/login')->withErrors(['msg' => 'Error: ' . $e->getMessage()]);
        }
    }
}
