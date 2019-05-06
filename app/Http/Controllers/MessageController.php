<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 22/12/2018
 * Time: 16:36
 */

namespace App\Http\Controllers;


use App\Events\MessageDeleted;
use App\Events\MessageSent;
use App\Events\MessageUpdated;
use App\Repositories\ChatRepository;
use App\Repositories\MessageRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

    private $messages;
    private $chats;

    public function __construct(MessageRepository $messages, ChatRepository $chats)
    {
        $this->messages = $messages;
        $this->chats = $chats;
    }

    public function save(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get('chat'))->load('participants')->load('participants.user');
        $message = $this->messages->save(["content" => $request->get('content'), "participant_id" => $request->get('user'), "chat_id" => $chat->id, "created_at" => Carbon::now()->toDateTimeString()])->load('participant')->load('participant.user');
        $chat->update(array("last_message" => Carbon::now()->toDateTimeString()));
        $this->chats->fillName($chat, $user);
        $returnObj = $message->toArray();
        $returnObj["chat"] = $chat->toArray();
        foreach ($chat->participants as $part) {
            event(new MessageSent($part->user, $returnObj));
        }
        return $returnObj;
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get("chat"));
        $message = $this->messages->getById($request->get('message'));
        if (is_null($message)) {
            return response("Cant find message", 400);
        }
        $userValidated = false;
        foreach ($chat->participants as $participant) {
            if (!$message->participant->is_deleted && $participant->user->id == $user->id) {
                if ($message->participant->id != $participant->id) {
                    return response("You cant edit this message", 403);
                } else {
                    $userValidated = true;
                    break;
                }
            }
        }
        if (!$userValidated) {
            return response("You cant edit this message", 403);
        }
        $message->update(["content" => $request->get('content'), "edited_at" => Carbon::now()->toDateTimeString()]);
        $this->chats->fillName($chat, $user);
        $returnObj = $message->toArray();
        $returnObj["chat"] = $chat->toArray();
        foreach ($chat->participants as $part) {
            event(new MessageUpdated($part->user, $returnObj));
        }
        return $returnObj;
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get("chat"));
        $message = $this->messages->getById($request->get('message'));
        if (is_null($message)) {
            return response("Cant find message", 400);
        }
        $userValidated = false;
        foreach ($chat->participants as $participant) {
            if ($participant->user->id == $user->id) {
                if ($message->participant->is_deleted) {
                    break;
                }
                if ($participant->is_admin && $message->participant->id == $participant->id) {
                    $userValidated = true;
                    break;
                } else {
                    return response("You cant delete this message", 403);
                }
            }
        }
        if (!$userValidated) {
            return response("You cant delete this message", 403);
        }
        $this->chats->fillName($chat, $user);
        $returnObj = $message->toArray();
        $returnObj["chat"] = $chat->toArray();
        $message->destroy($message->id);
        foreach ($chat->participants as $part) {
            event(new MessageDeleted($part->user, $returnObj));
        }
        return $returnObj;
    }

    public function getByChat(Request $request)
    {
        return $this->messages->getByChat($request->get('chat'), $request->get('offset'));
    }
}
