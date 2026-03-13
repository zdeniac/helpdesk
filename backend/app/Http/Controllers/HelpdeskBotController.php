<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHelpdeskMessage;
use App\Services\HelpdeskBotService;
use Illuminate\Support\Facades\Auth;

class HelpdeskBotController extends Controller
{
    public function __construct(private readonly HelpdeskBotService $service)
    {
    }

    public function store(StoreHelpdeskMessage $request)
    {
        $conversationDTO = $this->service->reply(Auth::id(), $request->input('message'));

        return response()->json($conversationDTO);
    }

    public function show()
    {
        return $this->service->startConversation(Auth::id())->toResource();
    }
}
