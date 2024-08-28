<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends BaseApiController
{
    //
    public function resend(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();
        return $this->sendResponse(null, 'auth.email_sent');
    }

    public function verify(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return $this->sendError('email has already been verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return $this->sendResponse(null, 'Email verified');
    }
}
