<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelpdeskMessage;
use App\Services\ConversationService;
use App\Services\HelpdeskAgentService;

class HelpdeskAgentController extends Controller
{
    public function __construct(
        private readonly ConversationService $convoService,
        private readonly HelpdeskAgentService $agentService,
    ) {
    }

    public function index()
    {
        return $this->convoService->listByStatus()->toResourceCollection();
    }

    public function store(StoreHelpdeskMessage $request)
    {
        $conversationDTO = $this->agentService->reply(
            $request->validated('conversation_id'),
            auth('api')->id(),
            $request->validated('message')
        );

        return response()->json($conversationDTO);
    }

    public function show(string $id)
    {
        return $this->convoService->find($id)->toResource();
    }

    public function close(string $id)
    {
        $this->agentService->closeConversation((int) $id);
        return response()->noContent();
    }
}
