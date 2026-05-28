<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('authentik');
        return $driver->stateless()->redirect();
    }

    public function callback()
    {
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver('authentik');
        $socialUser = $driver->stateless()->user();

        // $user->token;
        $user = User::updateOrCreate(
            ['authentik_id' => $socialUser->getId()],
            [
                'name'     => $socialUser->getName(),
                'email'    => $socialUser->getEmail(),
                'password' => $user->password ?? bcrypt(str()->random(32)),
            ]
        );

        Auth::login($user, remember: true);
        session(['authentik_id_token' => $socialUser->token]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $logoutUrl = config('services.authentik.base_url')
            . '/application/o/' . config('services.authentik.slug') . '/end-session/'
            . '?redirect_uri=' . urlencode(config('app.url'));

        if ($request->expectsJson()) {
            return response()->json(['redirect' => $logoutUrl]);
        }

        return redirect($logoutUrl);
    }
}
