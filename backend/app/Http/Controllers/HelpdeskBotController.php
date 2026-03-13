<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelpdeskMessage;
use App\Services\HelpdeskBotService;
use Illuminate\Support\Facades\Auth;

class HelpdeskBotController extends Controller
{
    public function __construct(
        private readonly HelpdeskBotService $service
    ){
    }

    public function store(StoreHelpdeskMessage $request)
    {
        $conversationDTO = $this->service->reply(auth('api')->id(), $request->validated('message'));

        return response()->json($conversationDTO);
    }

    public function show()
    {
        return $this->service->startConversation(auth('api')->id())->toResource();
    }
}
