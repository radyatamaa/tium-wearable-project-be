<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminPublicWelfare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PublicWelfareAuthController extends Controller
{
    protected $redirectTo = '/public-welfare/home';

    public function showLoginForm(Request $request)
    {
        if ($request->key) {
            $result = $this->loginRemotely($request);
            return $result;
        }
        return view('public-welfare.auth.login');
    }

    protected function loginRemotely(Request $request)
    {
        $decodedString = base64_decode($request->key);
        $userLogin = json_decode($decodedString, true);
        if (isset($userLogin['user_id']) && isset($userLogin['password'])) {
            $user = AdminPublicWelfare::where('user_id', $userLogin['user_id'])->first();

            if ($user && $userLogin['password'] === $user->password) {
                Auth::guard('publicWelfare')->login($user);
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

        if (Auth::guard('publicWelfare')->attempt($request->only('user_id', 'password'))) {
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
        Auth::guard('publicWelfare')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/public-welfare/login');
    }

    public function loginWithToken(Request $request)
    {
        $this->validateLogin($request);

        $user = User::where('user_id', $request->user_id)->first();

        if ($user && Auth::guard('publicWelfare')->attempt(['user_id' => $request->user_id, 'password' => $request->password])) {
            $token = $user->createToken('public-welfare-token')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    public function logoutWithToken(Request $request)
    {
        $request->user('publicWelfare')->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}