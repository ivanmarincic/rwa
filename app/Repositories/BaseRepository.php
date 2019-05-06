<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:42
 */

namespace App\Repositories;


use App\Repositories\Contracts\Repository;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements Repository
{

    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function save(array $data)
    {
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        return $this->model->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    public function get($id)
    {
        return $this->model->where("id", $id)->first();
    }
}
