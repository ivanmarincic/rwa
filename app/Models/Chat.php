<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:30
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $table = 'chats';

    public $timestamps = false;
    protected $fillable = [
        'name', 'created_at', 'last_message'
    ];

    public function participants()
    {
        return $this->hasMany('App\Models\ChatParticipant');
    }
}
