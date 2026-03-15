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
        return response()->json($this->service->startConversation(auth('api')->id()));
    }

    public function store(StoreHelpdeskMessage $request)
    {
        $conversationDTO = $this->service->replyByBot(
            $request->validated('message'), auth('api')->id()
        );

        return response()->json($conversationDTO);
    }
}
