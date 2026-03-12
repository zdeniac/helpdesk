<?php

namespace App\Http\Controllers;

use App\Service\HelpdeskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    public function __construct(private readonly HelpdeskService $service)
    {
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $userId = $request->user()->id;
        $conversationDTO = $this->service->botReply($userId, $request->input('message'));

        return response()->json($conversationDTO);
    }

    public function show()
    {
        return $this->service->startConversation(Auth::id())->toResource();
    }
}
