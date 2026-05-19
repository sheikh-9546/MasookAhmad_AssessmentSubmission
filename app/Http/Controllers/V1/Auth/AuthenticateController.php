<?php

namespace App\Http\Controllers\V1\Auth;

use App\Events\Auth\AccessTokenCreated;
use App\Exceptions\AuthExceptions\InvalidCredentialException;
use App\Http\Controllers\Controller;
use App\Http\HttpResponse;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\OAuthResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * Class AuthenticateController
 *
 * @group OAuth
 *
 * @apiResource
 */
class AuthenticateController extends Controller
{
    /**
     * Allow to log in user
     *
     * @apiResource App\Http\Resources\Auth\OAuthResource
     *
     * @apiResourceModel App\Models\User
     *
     * @throws Throwable
     */
    public function login(LoginRequest $loginRequest): JsonResponse
    {
        $loginRequest->validated();

        $this->assertEmailAddress($loginRequest->getEmail());

        $this->authenticate($loginRequest->getEmail(), $loginRequest->getCurrentPassword());

        AccessTokenCreated::dispatch(auth()->user());

        return HttpResponse::make()
            ->setMessage(trans('messages.user.login_success'))
            ->setData(new OAuthResource(auth()->user()))
            ->ok();
    }

    /**
     * @throws Throwable
     */
    private function assertEmailAddress($email)
    {
        $foundedUser = User::whereEmail($email)->first();

        if (empty($foundedUser)) {
            throw_unless(
                $foundedUser,
                ValidationException::withMessages(['email' => trans('messages.user.emailNotRegistred')])
            );
        } elseif (! empty($foundedUser) && $foundedUser->status == 0) {
            throw_unless(
                $foundedUser->status,
                ValidationException::withMessages(['email' => trans('messages.user.deactiveUser')])
            );
        }

    }

    /**
     * @throws Throwable
     */
    private function authenticate($email, $password)
    {
        throw_unless(
            Auth::attempt(['email' => $email, 'password' => $password], true),
            new InvalidCredentialException('Please provide the valid login credentials.')
        );
    }
}
