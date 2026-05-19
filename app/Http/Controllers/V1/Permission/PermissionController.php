<?php

namespace App\Http\Controllers\V1\Permission;

use App\Http\Controllers\Controller;
use App\Http\HttpResponse;
use App\Http\Requests\Permission\CreatePermissionRequest;
use App\Http\Requests\Permission\PaginatePermissionRequest;
use App\Http\Requests\Permission\UpdatePermissionRequest;
use App\Http\Resources\Permission\PermissionPaginateResourceCollection;
use App\Http\Resources\Permission\PermissionResource;
use App\Jobs\Permission\CreatePermission;
use App\Jobs\Permission\PaginatePermission;
use App\Jobs\Permission\UpdatePermission;
use App\Models\Permission;
use App\Policies\Permission\PermissionPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @group Permission
 *
 * APIs for managing permissions

 *
 * @authenticated
 */
class PermissionController extends Controller
{
    /**
     * Allow to pull the permissions
     *
     * @apiResourceModel App\Models\Permission
     *
     * @throws AuthorizationException
     */
    public function index(PaginatePermissionRequest $paginatePermissionRequest): JsonResponse
    {
        $this->authorize(PermissionPolicy::VIEW, Permission::class);

        $paginatePermissionRequest->validated();

        $paginateCollection = $this->dispatchSync(PaginatePermission::fromRequest($paginatePermissionRequest));

        return HttpResponse::make()
            ->setMessage(trans('messages.permission.list'))
            ->setData(PermissionPaginateResourceCollection::make($paginateCollection))
            ->ok();
    }

    /**
     * Allow to create the permission
     *
     * @apiResource App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     *
     * @throws AuthorizationException
     */
    public function store(CreatePermissionRequest $createPermissionRequest): JsonResponse
    {
        $this->authorize(PermissionPolicy::CREATE, Permission::class);

        $createPermissionRequest->validated();

        $createdPermission = $this->dispatchSync(CreatePermission::fromRequest($createPermissionRequest));

        return HttpResponse::make()
            ->setMessage(trans('messages.permission.create'))
            ->setData(PermissionResource::make($createdPermission))
            ->ok(Response::HTTP_CREATED);
    }

    /**
     * Allow to show the permission
     *
     * @apiResource App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     *
     * @throws AuthorizationException
     */
    public function show(Permission $permission): JsonResponse
    {
        $this->authorize(PermissionPolicy::VIEW, Permission::class);

        return HttpResponse::make()
            ->setMessage(trans('messages.permission.show'))
            ->setData(PermissionResource::make($permission))
            ->ok();
    }

    /**
     * Allow to update the permission
     *
     * @apiResource App\Http\Resources\Permission\PermissionResource
     *
     * @apiResourceModel App\Models\Permission
     *
     * @throws AuthorizationException
     */
    public function update(UpdatePermissionRequest $updatePermissionRequest, Permission $permission): JsonResponse
    {
        $this->authorize(PermissionPolicy::UPDATE, Permission::class);

        $updatePermissionRequest->validated();

        $updatedPermission = $this->dispatchSync(UpdatePermission::fromRequest($updatePermissionRequest, $permission));

        return HttpResponse::make()
            ->setMessage(trans('messages.permission.update'))
            ->setData(PermissionResource::make($updatedPermission))
            ->ok();
    }

    /**
     * Allow to delete the permission
     *
     * @throws AuthorizationException
     */
    public function destroy(Permission $permission): JsonResponse
    {
        $this->authorize(PermissionPolicy::DELETE, Permission::class);

        $permission->delete();

        return HttpResponse::make()
            ->setMessage(trans('messages.permission.delete'))
            ->ok();
    }
}
