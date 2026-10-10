<?php

namespace App\Services;

use App\Exceptions\ConversationClosedException;
use App\Exceptions\AutoReplyRuleNotFoundException;
use App\Models\Conversation;
use Illuminate\Support\Facades\DB;
use App\Events\BotMessageCreated;
class MessageService
{
    public function __construct( private AutoReplyService $autoReplyService) {}

    public function send( Conversation $conversation,  string $content): array {
        if ($conversation->status === 'CLOSED') {
            throw new ConversationClosedException();
        }

        return DB::transaction(function () use (  $conversation,  $content ) {
            $userMessage = $conversation->messages()->create([
                'sender_type' => 'USER',
                'content' => trim($content),
            ]);
$rule = $this->autoReplyService
    ->findMatchingRule($userMessage->content);

if (!$rule) {
    $rule = $this->autoReplyService
        ->getFallbackRule();

    if (!$rule) {
        throw new AutoReplyRuleNotFoundException();
    }
}
            $botMessage = $conversation->messages()->create([
                'sender_type' => 'BOT',
                'content' => $rule->response,
            ]);
BotMessageCreated::dispatch($botMessage);
            return [
                'user_message' => $userMessage,
                'bot_message' => $botMessage,
            ];
        });
    }
}