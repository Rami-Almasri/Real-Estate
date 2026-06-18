<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\District;
use App\Models\Office;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'بيانات الدخول غير صحيحة.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->isOffice() ? route('dashboard.index') : route('account.matches')
        );
    }

    public function showRegister()
    {
        return view('auth.register', [
            'cities'    => City::all(),
            'districts' => District::with('city')->get(),
        ]);
    }

    public function register(Request $request)
    {
        $role = $request->input('role', 'buyer');

        $base = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:buyer,office'],
        ]);

        if ($role === 'office') {
            $office = $request->validate([
                'address'     => ['required', 'string', 'max:160'],
                'district_id' => ['required', 'exists:districts,id'],
            ]);

            $district = District::find($office['district_id']);

            $officeModel = Office::create([
                'address'     => $office['address'],
                'district_id' => $district->id,
                'latitude'    => $district->latitude ?? 0,
                'longitude'   => $district->longitude ?? 0,
            ]);

            $user = $officeModel->provider()->create([
                'name'     => $base['name'],
                'email'    => $base['email'],
                'password' => Hash::make($base['password']),
            ]);
        } else {
            $user = User::create([
                'name'          => $base['name'],
                'email'         => $base['email'],
                'password'      => Hash::make($base['password']),
                'userable_type' => User::class,
                'userable_id'   => 0,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(
            $user->isOffice() ? route('dashboard.index') : route('match.wizard')
        )->with('success', 'مرحباً بك في عقّار سوريا! 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
