<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminGeneralPurpose;

class GeneralPurposeAuthController extends Controller
{
    protected $redirectTo = '/general-purpose/home';

    public function showLoginForm(Request $request)
    {
        if ($request->key) {
            $result = $this->loginRemotely($request);
            return $result;
        }
        return view('general-purpose.auth.login');
    }

    protected function loginRemotely(Request $request)
    {
        $decodedString = base64_decode($request->key);
        $userLogin = json_decode($decodedString, true);
        if (isset($userLogin['user_id']) && isset($userLogin['password'])) {
            $user = AdminGeneralPurpose::where('user_id', $userLogin['user_id'])->first();

            if ($user && $userLogin['password'] === $user->password) {
                Auth::guard('generalPurpose')->login($user);
                return redirect()->intended($this->redirectTo);
            }
        }

        return back()->withErrors([
            'user_id' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::guard('generalPurpose')->attempt($request->only('user_id', 'password'))) {
            return redirect()->intended($this->redirectTo);
        }

        return back()->withErrors([
            'user_id' => 'The provided credentials do not match our records.',
        ])->withInput();
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('generalPurpose')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/general-purpose/login');
    }

    public function loginWithToken(Request $request)
    {
        $this->validateLogin($request);

        $user = AdminGeneralPurpose::where('user_id', $request->user_id)->first();

        if ($user && Auth::guard('generalPurpose')->attempt($request->only('user_id', 'password'))) {
            $token = $user->createToken('general-purpose-token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    public function logoutWithToken(Request $request)
    {
        $request->user('generalPurpose')->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}