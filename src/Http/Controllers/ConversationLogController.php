<?php

namespace NovaConversationLog\Http\Controllers;

use OpenDialogAi\ConversationLog\ChatbotUser;
use OpenDialogAi\ConversationLog\Message;

class ConversationLogController
{
    public function conversationLog($user, $offset)
    {
        return Message::where('user_id', $user)
            ->offset($offset)
            ->limit(100)
            ->orderBy('microtime')
            ->get();
    }
}
