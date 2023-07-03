<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;

class MailWelcomeController extends BaseWelcomeController
{
    public function showWelcomeForm(Request $request, User $user)
    {
        return view('welcomeNotification.welcome')->with([
            'email' => $request->email,
            'user' => $user,
        ]);
    }

    protected function sendPasswordSavedResponse(): Response
    {
        return redirect()->to($this->redirectPath())->with('status', __('Welcome! You are now logged in!'));
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/admin/dashboard';
    }
}
