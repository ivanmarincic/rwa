<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:44
 */

namespace App\Repositories;

use App\Models\Chat;
use Illuminate\Support\Facades\DB;

class ChatRepository extends BaseRepository
{
    public function __construct(Chat $chat)
    {
        parent::__construct($chat);
    }

    public function allForUser($user)
    {
        return $this->fillNames($this->model
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', '=', $user->id)->where('is_deleted', '=', 0);
            })
            ->with('participants')
            ->with('participants.user')
            ->orderBy('created_at', 'desc')
            ->orderBy('last_message', 'desc')
            ->get(), $user);
    }

    public function getById($id, $currentUser)
    {
        $chat = $this->model
            ->with('participants')
            ->with('participants.user')
            ->where("id", $id)->first();
        $this->fillName($chat, $currentUser);
        return $chat;
    }

    public function search($searchQuery, $user)
    {
        $matchedUsers = DB::table('users')
            ->where('full_name', 'like', "%" . $searchQuery . "%")
            ->orWhere('username', 'like', "%" . $searchQuery . "%")
            ->pluck("id");
        $matchedChats = DB::table('chat_participants')
            ->where('user_id', '=', $user->id)
            ->where('is_deleted', '=', 0)
            ->pluck("chat_id");
        return $this->fillNames($this->model
            ->whereHas('participants', function ($query) use ($user, $matchedUsers) {
                $query
                    ->whereIn('user_id', $matchedUsers);
            })
            ->whereIn("id", $matchedChats)
            ->with('participants')
            ->with('participants.user')
            ->orderBy('created_at', 'desc')
            ->orderBy('last_message', 'desc')
            ->get(), $user);
    }

    public function fillNames($chats, $currentUser)
    {
        foreach ($chats as $chat) {
            $this->fillName($chat, $currentUser);
        }
        return $chats;
    }

    public function fillName($chat, $currentUser)
    {
        $participants = $chat->participants->toArray();
        if (empty($chat->name) && !empty($participants)) {
            if (empty($name)) {
                $chat->name = implode(", ", array_map(function ($x) {
                    return empty($x['user']["full_name"]) ? $x['user']["username"] : $x['user']["full_name"];
                }, array_filter($participants, function ($x) use ($currentUser) {
                    return $x['user']['id'] != $currentUser->id;
                })));
            }
        }
    }
}
