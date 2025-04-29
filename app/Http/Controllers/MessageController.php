<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessageGetRequest;
use App\Http\Requests\MessageRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ResponseResource;
use App\Models\Message;
use Illuminate\Http\JsonResponse;

class MessageController
{
    /**
     * Display a listing of the resource.
     *
     * @param MessageGetRequest $request
     * @return JsonResponse
     */
    public function index(MessageGetRequest $request): JsonResponse
    {
        try {
            if (isset($request->validator) && $request->validator->fails()) {
                return (new ResponseResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
            }
            $validatedData = $request->validated();

            $query = Message::query();

            if (isset($validatedData['user_id'])) {
                $query->where('user_id', '=', $validatedData['user_id']);
            }

            if (isset($validatedData['begin_date'])) {
                $query->whereDate('scheduled_at', '>=', $validatedData['begin_date']);
            }

            if (isset($validatedData['end_date'])) {
                $query->whereDate('scheduled_at', '<=', $validatedData['end_date']);
            }

            $sortBy = $validatedData['sort_by'] ?? 'created_at';
            $sortDirection = $validatedData['sort_direction'] ?? 'desc';

            $query->orderBy($sortBy, $sortDirection);

            if (isset($validatedData['per_page'])) {
                $data = $query->paginate($validatedData['per_page']);
                $data->appends($validatedData);
            } else {
                $data = $query->get();
            }
            if ($data->isEmpty()) {
                return (new ResponseResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new ResponseResource(null, false, 'Failed to get users', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new ResponseResource(MessageResource::collection($data), true, 'Message data found'))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = Message::find($id);
            if (!$user) {
                return (new ResponseResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new ResponseResource(null, false, 'Failed to get user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new ResponseResource(new MessageResource($user), true, 'Message data found'))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MessageRequest $request): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new ResponseResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }

        try {
            $validated = $request->validated();
            $user = Message::create($validated);
        } catch (\Exception $e) {
            return (new ResponseResource(null, false, 'Failed to create user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new ResponseResource(new MessageResource($user), true, 'Message created successfully'))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MessageRequest $request, $id): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new ResponseResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }


        try {
            $user = Message::find($id);
            if (!$user) {
                return (new ResponseResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
            $validated = $request->validated();
            $user->update($validated);
        } catch (\Exception $e) {
            return (new ResponseResource(null, false, 'Failed to update user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new ResponseResource(new MessageResource($user), true, 'Message updated successfully'))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        $user = Message::find($id);
        if (!$user) {
            return (new ResponseResource(null, false, 'Data not found'))->response()->setStatusCode(404);
        }

        try {
            $user->delete();
        } catch (\Exception $e) {
            return (new ResponseResource(null, false, 'Failed to delete user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new ResponseResource(new MessageResource($user), true, 'Message deleted successfully'))->response();
    }
}
