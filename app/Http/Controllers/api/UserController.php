<?php

namespace App\Http\Controllers\api;

use App\Http\Requests\user\StoreUserRequest;
use App\Http\Requests\user\ShowUserRequest;

use App\Http\Requests\user\UpdateUserRequest;
use App\Http\Requests\user\UserSearchRequest;
use App\Http\Resources\UserBasicResource;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function __construct(protected UserRepository  $userRepository)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(UserSearchRequest $request): \Illuminate\Http\JsonResponse
    {
        //
        $dataSearch = $request->validated();

        $users = $this->userRepository->getListByConditions($dataSearch);
        $result = UserBasicResource::collection($users);

        return $this->sendPaginationResponse($users, $result);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): \Illuminate\Http\JsonResponse
    {
        //
        $data = $request->validated();

        $data['email_verified_at'] = now();
        $user = $this->userRepository->create($data);
        $result = UserBasicResource::make($user);

        return $this->sendResponse($result);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): \Illuminate\Http\JsonResponse
    {
        //
        $user = $this->userRepository->find($id);
        $result = UserBasicResource::make($user);

        return $this->sendResponse($result);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        //
        $data = $request->validated();

        $user = $this->userRepository->update($id, $data);
        $result = UserBasicResource::make($user);

        return $this->sendResponse($result);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id): \Illuminate\Http\JsonResponse
    {
        //
        $check = $this->userRepository->delete($id);
        if (!$check) {
            return $this->sendError(__('common.delete_failed'));
        }

        return $this->sendResponse(null, __('common.delete_successful'));
    }
}
