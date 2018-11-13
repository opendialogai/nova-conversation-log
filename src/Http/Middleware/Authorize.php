<?php

namespace NovaConversationLog\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use NovaConversationLog\ConversationLog;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    public function handle(Request $request, Closure $next): Response
    {
        return app(ConversationLog::class)->authorize($request)
            ? $next($request)
            : abort(403);
    }
}
