<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserDetailRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UserDetailResource;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;


class UserDetailController
{

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $users = UserDetail::all();
            if ($users->isEmpty()) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to get users', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(UserDetailResource::collection($users), true, 'UserDetail data found'))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = UserDetail::find($id);
            if (!$user) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to get user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new UserDetailResource($user), true, 'UserDetail data found'))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserDetailRequest $request): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new MessageResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }

        try {
            $validated = $request->validated();
            $user = UserDetail::create($validated);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to create user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new UserDetailResource($user), true, 'UserDetail created successfully'))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserDetailRequest $request, $id): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new MessageResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }


        try {
            $user = UserDetail::find($id);
            if (!$user) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
            $validated = $request->validated();
            $user->update($validated);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to update user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new UserDetailResource($user), true, 'UserDetail updated successfully'))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        $user = UserDetail::find($id);
        if (!$user) {
            return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
        }

        try {
            $user->delete();
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to delete user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new UserDetailResource($user), true, 'UserDetail deleted successfully'))->response();
    }
}
