<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 19/12/2018
 * Time: 03:03
 */

namespace App\Http\Controllers;


use App\Events\ChatCreated;
use App\Repositories\ChatParticipantRepository;
use App\Repositories\ChatRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    private $chats;
    private $chatParticipants;
    private $users;

    public function __construct(ChatRepository $chats, ChatParticipantRepository $chatParticipants, UserRepository $users)
    {
        $this->chats = $chats;
        $this->chatParticipants = $chatParticipants;
        $this->users = $users;
    }

    public function save(Request $request)
    {
        $currentUser = Auth::user();
        $chatUsers = $this->users->getByIds($request->get('participants'))->toArray();
        array_push($chatUsers, Auth::user());
        if (count($chatUsers) <= 1) {
            return response("Less than 2 users selected", 400);
        }
        $chat = $this->chats->save(['name' => $request->get('name'), 'created_at' => Carbon::now()->toDateTimeString()]);
        $chat->participants()->createMany(array_map(function ($x) use ($chat, $currentUser) {
            return ['user_id' => $x['id'], 'chat_id' => $chat->id, "is_admin" => $x['id'] == $currentUser->id];
        }, $chatUsers));
        $chat = $chat->load('participants', 'participants.user', 'participants.chat');
        $this->chats->fillName($chat, $currentUser);
        foreach ($chat->participants as $part) {
            event(new ChatCreated($part->user, $chat));
        }
        return $chat;
    }

    public function getById($id)
    {
        $currentUser = Auth::user();
        return $this->chats->getById($id, $currentUser);
    }

    public function updatePermissions(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get("chat"));
        $userValidated = false;
        foreach ($chat->participants as $participant) {
            if ($participant->user->id == $user->id) {
                if (!$participant->is_admin && $participant->is_deleted) {
                    return response("You cant update permissions", 403);
                } else {
                    $userValidated = true;
                    break;
                }
            }
        }
        if (!$userValidated) {
            return response("You cant update permissions", 403);
        }
        $this->chatParticipants->updatePermissions($request->get('participants'));
        return response("true", 200);
    }

    public function getParticipants(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get("chat"));
        $chat = $chat->load('participants', 'participants.user', 'participants.chat');
        $userValidated = false;
        foreach ($chat->participants as $participant) {
            if ($participant->user->id == $user->id && !$participant->is_deleted) {
                if (!$participant->is_admin && $participant->is_deleted) {
                    return response("You cant request participants for this chat", 403);
                } else {
                    $userValidated = true;
                    break;
                }
            }
        }
        if (!$userValidated) {
            return response("You cant request participants for this chat", 403);
        }
        return $chat->participants;
    }

    public function leave(Request $request)
    {
        $user = Auth::user();
        $chat = $this->chats->get($request->get("chat"));
        $participant = null;
        foreach ($chat->participants as $part) {
            if ($part->user->id == $user->id) {
                $participant = $part;
            }
        }
        if (is_null($participant)) {
            return response("You are not participating in this chat", 403);
        }
        return $this->chatParticipants->update(["is_deleted" => 1], $participant->id);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        return $this->chats->search($request->get('searchQuery'), $user);
    }

    public function getAllByUser()
    {
        $user = Auth::user();
        return $this->chats->allForUser($user);
    }
}
