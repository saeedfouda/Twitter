<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Session;
use Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Display the form to request a password reset link by chosen option.
     *
     * @return \Illuminate\Http\Response
     */
    public function showResetOptionsForm(Request $request)
    {
        $this->validateCredintial($request);
        $field = filter_var($request->only('credintial')['credintial'], FILTER_VALIDATE_EMAIL) ? 'email': 'username';
        $user  = \App\User::where($field, $request->only('credintial')['credintial'])->first();
        Session::put('resetting_password', [$user->email]);
        return view('auth.passwords.options');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'option' => 'required|in:0'
        ]);

        if($validation->fails()){
            return back()->withErrors($validation->errors());
        }else{

            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                ['email' => Session::get('resetting_password')[0]]
            );

            return $response == Password::RESET_LINK_SENT
                        ? $this->sendResetLinkResponse($response)
                        : $this->sendResetLinkFailedResponse($request, $response);

        }
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param  string  $response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendResetLinkResponse($response)
    {
        return view('auth.passwords.sent');
    }

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateCredintial(Request $request)
    {
        $field = filter_var($request->only('credintial')['credintial'], FILTER_VALIDATE_EMAIL) ? 'email': 'username';
        $this->validate($request, [
            'credintial' => "required|exists:users,{$field}"
        ], [
            'credintial.exists' => 'We couldn\'t find any account with that information'
        ]);
    }

}
