<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:30
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';

    public $timestamps = false;
    protected $fillable = [
        'participant_id', 'content', 'chat_id', 'created_at', 'edited_at'
    ];

    public function chat()
    {
        return $this->belongsTo('App\Models\Chat');
    }

    public function participant()
    {
        return $this->belongsTo('App\Models\ChatParticipant');
    }
}
