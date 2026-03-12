<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEventRequest;
use App\Http\Resources\EventResource;
use App\Service\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $service,
    ){
    }

    // IDE KELL AZ AUTHgi
    public function index(): ResourceCollection
    {
        return $this->service->all()->toResourceCollection();
    }

    public function store(SaveEventRequest $request)
    {
        return $this->service->create($request->validated())->toResource();
    }

    public function show(string $id): JsonResource
    {
        return $this->service->find($id)->toResource();
    }

    public function update(SaveEventRequest $request, string $id): JsonResource
    {
        return $this->service->update((int)$id, $request->validated())->toResource();
    }

    public function destroy(string $id): Response
    {
        $this->service->delete((int)$id);
        return response()->noContent();
    }
}
