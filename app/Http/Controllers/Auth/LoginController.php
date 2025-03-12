<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers; // This provides the default methods for user authentication.

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home'; // This is where users will be redirected after login.

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Allow only guests to access the login page, but allow logged-in users to logout.
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // Return the login view
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Validate the login form data.
        $this->validateLogin($request);

        // Check if the user has exceeded the maximum number of login attempts.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            // Fire a lockout event and send the lockout response.
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Attempt to log the user in.
        if ($this->attemptLogin($request)) {
            // If successful, clear login attempts and send the login response.
            $this->clearLoginAttempts($request);
            return $this->sendLoginResponse($request);
        }

        // If login failed, increment login attempts and return an error response.
        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        // Validate the login request data (email and password).
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Use Laravel's built-in Auth to logout the user.
        Auth::logout();

        // Invalidate the session and regenerate the CSRF token.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the home page (or any other page you prefer after logout).
        return redirect('/');
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return back()->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => trans('auth.failed'), // This is the default error message for failed logins.
            ]);
    }
}

