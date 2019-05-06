<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:44
 */

namespace App\Repositories;

use App\Models\Message;

class MessageRepository extends BaseRepository
{
    public function __construct(Message $message)
    {
        parent::__construct($message);
    }

    public function getByChat($chatId, $offset)
    {
        return $this->model->where("chat_id", $chatId)->skip($offset)->take(15)->orderBy('created_at', 'desc')->with('participant')->with('participant.user')->get();
    }

    public function getById($id)
    {
        return $this->model->where("id", $id)->with('participant')->with('chat')->first();
    }
}
