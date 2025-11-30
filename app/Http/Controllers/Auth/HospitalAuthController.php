<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserAdminHospital;
use App\Models\UserStaffHospital;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EmergencyReportHospital;
use App\Models\PatientCallReportHospital;
use Illuminate\Support\Facades\Hash;

class HospitalAuthController extends Controller
{
    protected $redirectTo = '/hospital/home';

    public function showLoginForm(Request $request)
    {
        if ($request->key) {
            $result = $this->loginRemotely($request);
            return $result;
        }
        return view('hospital.auth.login');
    }
    protected function loginRemotely(Request $request)
    {
        $decodedString = base64_decode($request->key);
        $userLogin = json_decode($decodedString, true);
        if (isset($userLogin['admin_user_id']) && isset($userLogin['admin_password'])) {
            $user = UserAdminHospital::where('user_id', $userLogin['admin_user_id'])->first();

            if ($user && $userLogin['admin_password'] === $user->password) {
                Auth::guard('hospital')->login($user);
                return redirect()->intended($this->redirectTo);
            }
        } elseif (isset($userLogin['user_id']) && isset($userLogin['password'])) {
            $user = UserStaffHospital::where('user_id', $userLogin['user_id'])->first();

            if ($user && $userLogin['password'] === $user->password) {
                Auth::guard('hospitalStaff')->login($user);
                return redirect()->intended($this->redirectTo);
            }
        }

        return back()->withErrors([
            'user_id' => 'The provided credentials do not match our records.',
        ])->withInput();
    }
    public function login(Request $request)
    {
        // $this->validateLogin($request);

        if ($request->admin_user_id && $request->admin_password) {
            if (Auth::guard('hospital')->attempt(['user_id' => $request->admin_user_id, 'password' => $request->admin_password])) {
                return redirect()->intended($this->redirectTo);
            }
        } else if ($request->user_id && $request->password) {
            if (Auth::guard('hospitalStaff')->attempt(['user_id' => $request->user_id, 'password' => $request->password])) {
                return redirect()->intended($this->redirectTo);
            }
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
        Auth::guard('hospital')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Auth::guard('hospitalStaff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/hospital/login');
    }

    public function loginWithToken(Request $request)
    {
        $this->validateLogin($request);

        return response()->json(['error' => 'The provided credentials do not match our records.'], 401);
    }

    public function logoutWithToken(Request $request)
    {
        $request->user('hospital')->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully'], 200);
    }
}