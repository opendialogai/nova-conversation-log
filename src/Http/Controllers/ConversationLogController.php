<?php

namespace NovaConversationLog\Http\Controllers;

use Webchat\Message;
use Webchat\UserStore;

class ConversationLogController
{
    public function conversationLog($user, $offset)
    {
        return Message::where('user_id', $user)
            ->offset($offset)
            ->limit(20)
            ->orderBy('microtime')
            ->get();
    }

    public function contextLog($user)
    {
        return UserStore::where('user_id', $user)
            ->orderBy('created_at')
            ->get();
    }
}
