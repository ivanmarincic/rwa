<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:44
 */

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function getByUsername($username)
    {
        return $this->model->where('username', $username)->first();
    }

    public function getByIds($ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

    public function search($searchQuery)
    {
        return $this->model->where([['username', 'like', "%" . $searchQuery . "%"], ['username', '<>', Auth::user()->username]])->get();
    }

    public function allButMe()
    {
        return $this->model->where('username', '<>', Auth::user()->username)->get();
    }
}
