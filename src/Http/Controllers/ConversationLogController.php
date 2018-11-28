<?php

namespace NovaConversationLog\Http\Controllers;

use Webchat\Message;
use Webchat\UserStore;
use Webchat\ChatbotUser;

class ConversationLogController
{
    public function conversationLog($user, $offset)
    {
        return Message::where('user_id', $user)
            ->offset($offset)
            ->limit(100)
            ->orderBy('microtime')
            ->get()
            ->each(function ($message) {
                if ($message->user) {
                    $message->user = unserialize($message->user);
                }
                return $message;
            });
    }

    public function contextLog($user)
    {
        return UserStore::where('user_id', $user)
            ->orderBy('created_at')
            ->get();
    }

    public function userIsInHandoverMode($user)
    {
        $inHandOverMode = ChatbotUser::where('user_id', $user)
            ->first()
            ->isInHandoverMode();

        return $inHandOverMode ? 'true' : 'false';
    }
}
