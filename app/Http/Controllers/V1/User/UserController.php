<?php

namespace App\Http\Controllers\V1\User;

use App\Enums\RoleType;
use App\Http\Controllers\Controller;
use App\Http\HttpResponse;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\PaginateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserCreatedResource;
use App\Http\Resources\User\UserPaginateResourceCollection;
use App\Http\Resources\User\UserResource;
use App\Jobs\User\CreateUser;
use App\Jobs\User\PaginateUser;
use App\Jobs\User\UpdateUser;
use App\Models\User;
use App\Policies\User\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group User
 *
 * APIs for managing users
 *
 * @apiResource
 *
 * @authenticated
 *
 **/
class UserController extends Controller
{
    /**
     * Allow to pull the users
     *
     * @apiResourceModel App\Models\User
     *
     * @throws AuthorizationException
     */
    public function index(PaginateUserRequest $paginateUserRequest): JsonResponse
    {
        $this->authorize(UserPolicy::VIEW, User::class);

        $paginateUserRequest->validated();

        $paginateCollection = $this->dispatchSync(PaginateUser::fromRequest($paginateUserRequest));

        return HttpResponse::make()
            ->setMessage(trans('messages.user.list'))
            ->setData(UserPaginateResourceCollection::make($paginateCollection))
            ->ok();
    }

    /**
     * Allow to create the  user
     *
     * @apiResource App\Http\Resources\User\UserCreatedResource
     *
     * @apiResourceModel App\Models\User
     *
     * @throws AuthorizationException
     */
    public function store(CreateUserRequest $createUserRequest): JsonResponse
    {
        // $this->authorize(UserPolicy::CREATE, User::class);

        $createUserRequest->validated();

        $createdUser = $this->dispatchSync(CreateUser::fromRequest($createUserRequest, RoleType::User));

        return HttpResponse::make()
            ->setMessage(trans('messages.user.create'))
            ->setData(UserCreatedResource::make($createdUser))
            ->ok(Response::HTTP_CREATED);
    }

    /**
     * Allow to show the  user
     *
     * @apiResource App\Http\Resources\User\UserResource
     *
     * @apiResourceModel App\Models\User
     *
     * @throws AuthorizationException
     */
    public function show(User $user): JsonResponse
    {
        // $this->authorize(UserPolicy::VIEW, User::class);

        return HttpResponse::make()
            ->setMessage(trans('messages.user.show'))
            ->setData(UserResource::make($user))
            ->ok();
    }

    /**
     * Allow to update the user
     *
     * @apiResource App\Http\Resources\User\UserCreatedResource
     *
     * @apiResourceModel App\Models\User
     *
     * @throws AuthorizationException
     */
    public function update(UpdateUserRequest $updateUserRequest, User $user): JsonResponse
    {
        $this->authorize(UserPolicy::UPDATE, User::class);

        $updateUserRequest->validated();

        $updatedUser = $this->dispatchSync(UpdateUser::fromRequest($updateUserRequest, $user));

        return HttpResponse::make()
            ->setMessage(trans('messages.user.update'))
            ->setData(UserCreatedResource::make($updatedUser))
            ->ok();
    }

    /**
     * Allow to delete the  user
     *
     * @throws AuthorizationException
     */
    public function destroy(User $user): JsonResponse
    {

        $this->authorize(UserPolicy::DELETE, User::class);

        $user->delete();

        return HttpResponse::make()
            ->setMessage(trans('messages.user.delete'))
            ->ok();
    }
}
