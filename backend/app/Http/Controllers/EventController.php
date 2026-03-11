<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveEventRequest;
use App\Http\Resources\EventResource;
use App\Service\EventService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $service,
    ) {
    }

    // IDE KELL AZ AUTH
    public function index()
    {
        return $this->service->all()->toResourceCollection();
    }

    public function store(SaveEventRequest $request)
    {
        return $this->service->create($request->validated())->toResource();
    }

    public function show(string $id)
    {
        return $this->service->find($id)->toResource();
    }

    public function update(SaveEventRequest $request, string $id)
    {
        return $this->service->update((int)$id, $request->validated())->toResource();
    }

    public function destroy(string $id)
    {
        $this->service->delete((int)$id);
        return response()->noContent();
    }
}
