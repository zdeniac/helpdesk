<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelpdeskMessage;
use App\Services\HelpdeskBotService;

class HelpdeskBotController extends Controller
{
    public function __construct(
        private readonly HelpdeskBotService $service
    ) {
    }

    public function show()
    {
        return $this->service->startConversation(auth('api')->id())->toResource();
    }

    public function store(StoreHelpdeskMessage $request)
    {
        $conversationDTO = $this->service->reply($request->validated('message'), auth('api')->id());

        return response()->json($conversationDTO);
    }
}
