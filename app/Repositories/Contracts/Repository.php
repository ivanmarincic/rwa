<?php
/**
 * Created by IntelliJ IDEA.
 * User: Ivan
 * Date: 13/12/2018
 * Time: 23:41
 */

namespace App\Repositories\Contracts;


interface Repository
{
    public function all();

    public function save(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function get($id);
}
