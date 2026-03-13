<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\UserEventService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function __construct(
        private readonly UserEventService $service,
    ){
    }

    public function index(): ResourceCollection
    {
        return $this->service->listByUser(auth('api')->id())->toResourceCollection();
    }

    public function store(StoreEventRequest $request)
    {
        return $this->service->createByUser($request->validated(), auth('api')->id())->toResource();
    }

    public function show(string $id): JsonResource
    {
        return $this->service->findByUser((int)$id, auth('api')->id())->toResource();
    }

    public function update(UpdateEventRequest $request, string $id): JsonResource
    {
        return $this->service->updateByUser((int)$id, $request->validated(), auth('api')->id())->toResource();
    }

    public function destroy(string $id): Response
    {
        $this->service->deleteByUser((int)$id, auth('api')->id());
        return response()->noContent();
    }
}
