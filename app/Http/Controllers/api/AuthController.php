<?php

namespace App\Http\Controllers\api;


use App\Http\Requests\user\auth\ForgotPasswordRequest;
use App\Http\Requests\user\auth\LoginRequest;
use App\Http\Requests\user\auth\RegisterRequest;
use App\Http\Resources\UserBasicResource;
use App\Mail\ResetPasswordMail;
use App\Models\ResetPassword;
use App\Repositories\ResetPasswordRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;




class AuthController extends BaseApiController
{
    //

    public function __construct(
        protected UserRepository          $userRepository,
        protected ResetPasswordRepository $resetPasswordRepository)
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        if (Auth::attempt($data)) {
            $user = auth()->user();

            $token = $user->createToken('access_token');
            $refreshToken = $user->createToken('refresh_token');

            // Token life to end of day. Must log in if pass to new day
            $endTimeOfAccessToken = Carbon::now()->addHours(12);
            $endTimeOfRefreshToken = Carbon::now()->addHours(12);


            $token->accessToken->expires_at = $endTimeOfAccessToken;
            $token->accessToken->save();

            $refreshToken->accessToken->expires_at = $endTimeOfRefreshToken;
            $refreshToken->accessToken->save();

            $result = UserBasicResource::make($user);
            $data = [
                'access_token' => $token->plainTextToken,
                'refresh_token' => $refreshToken->plainTextToken,
                'user' => $result,
            ];

            return $this->sendResponse($data, __('common.request_successful'));
        }

        return $this->sendError('Thông tin đăng nhập không chính xác');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = $this->userRepository->create($data);

        event(new Registered($user));
        Auth::login($user);

        $token = $user->createToken('authToken');

        // Token life to end of day. Must log in if pass to new day
        $endTimeOfToken = Carbon::now()->addHours(24);
        $token->accessToken->expires_at = $endTimeOfToken;
        $token->accessToken->save();

        return $this->sendResponse([$user, $token], __('common.register_successful'));

    }
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return $this->sendResponse($user, __('common.logout_successful'));
    }

    public function sendMailForgotPassword(Request $request): JsonResponse
    {
        $user = $this->userRepository->findByCondition(['email' => $request['email']])->first();

        $mailData = ['email' => $user->email, 'token' => rand(100000, 1000000)];

        $passwordReset = ResetPassword::updateOrCreate([
            'email' => $mailData['email'],
        ], [
            'token' => $mailData['token'],
        ]);

        if ($passwordReset) {
           Mail::to($mailData['email'])->send(new ResetPasswordMail($mailData));
        }

        return $this->sendResponse($passwordReset, 'check mail');
    }
    public function reset(ForgotPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();
        $passwordReset = $this->resetPasswordRepository->findByCondition([
            'email' => $data['email'],
            'token' => $data['code'],
        ])->first();

        if($passwordReset){
            if (Carbon::parse($passwordReset->updated_at)->addMinutes(15)->isPast()) {
                $passwordReset->delete();

                return $this->sendError('this code has expired', 422);
            }

            $user = $this->userRepository->findByCondition(['email' => $data['email']])->first();
            $user->update( ['password' => Hash::make($data['new_password'])] );

            $passwordReset->delete();

            return $this->sendResponse($user,'success');
        }

        return $this->sendError('wrong email or reset-password code');
    }
        function refreshToken(Request $request): JsonResponse
        {
            $accessToken = $request->user()->createToken('access_token', ['*'], Carbon::now()->addHours(24));
            return $this->sendResponse(['access_token' => $accessToken->plainTextToken], __('common.refresh_token_successful'));
        }
}
