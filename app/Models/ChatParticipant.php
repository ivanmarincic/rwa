<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:31
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    protected $table = 'chat_participants';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'chat_id', 'is_admin'
    ];

    protected $hidden = ['user_id', 'chat_id'];

    public function chat()
    {
        return $this->belongsTo('App\Models\Chat');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
