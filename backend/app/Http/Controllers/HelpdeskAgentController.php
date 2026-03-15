<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelpdeskMessage;
use App\Services\ConversationService;
use App\Services\HelpdeskBotService;

class HelpdeskAgentController extends Controller
{
    public function __construct(
        private readonly ConversationService $convoService,
        private readonly HelpdeskBotService $botService
    ) {
    }

    public function index()
    {   
        return $this->convoService->listByStatus()->toResourceCollection()->toPrettyJson();
    }

    public function store(StoreHelpdeskMessage $request, string $conversationId)
    {
        $conversationDTO = $this->botService->replyByAgent(
            $request->validated('message'),
            $conversationId,
            auth('api')->id(),
        );

        return response()->json($conversationDTO);
    }

    public function show(string $id)
    {
        return response()->json($this->botService->findConversationWithMessages((int) $id));
    }

    public function close(string $id)
    {
        $this->botService->closeConversation((int) $id);
        return response()->noContent();
    }
}
