<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:44
 */

namespace App\Repositories;

use App\Models\ChatParticipant;
use Illuminate\Support\Facades\DB;

class ChatParticipantRepository extends BaseRepository
{
    public function __construct(ChatParticipant $chatParticipant)
    {
        parent::__construct($chatParticipant);
    }

    public function saveAll(array $data)
    {
        DB::transaction(function () use ($data) {
            $result = [];
            for ($x = 0; $x < count($data); $x++) {
                array_push($result, $this->model->save($data[$x]));
            }
            return $result;
        });
    }

    public function updatePermissions(array $data)
    {
        DB::transaction(function () use ($data) {
            for ($x = 0; $x < count($data); $x++) {
                $participant = $data[$x];
                $this->update(["is_admin" => $participant["is_admin"]], $participant["id"]);
            }
        });
    }
}
