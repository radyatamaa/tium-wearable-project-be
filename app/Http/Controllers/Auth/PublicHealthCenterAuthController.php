<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PublicHealthCenterAuthController extends Controller
{
    protected $redirectTo = '/home';

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        if (Auth::guard('publicHealthCenter')->attempt(['user_id' => $request->user_id, 'password' => $request->password])) {
            $user = User::where('user_id', $request->user_id)->first();
            $user->last_login = now();
            $user->save();
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
        Auth::guard('publicHealthCenter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/public-health-center/login');
    }

    public function loginWithToken(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where('user_id', $request->user_id)->first();

        if ($user && Auth::guard('publicHealthCenter')->attempt(['user_id' => $request->user_id, 'password' => $request->password])) {
            $token = $user->createToken('public-health-center-token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    public function logoutWithToken(Request $request)
    {
        $request->user('publicHealthCenter')->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}