<?php
class Model_Task extends \Model
{
    public static function find_by_id($id)
    {
        return DB::select('*')->from('tasks')->where('id', $id)->execute()->current();
    }

    public static function create($data)
    {
        return DB::insert('tasks')->set($data)->execute();
    }

    public static function update_status($id, $status)
    {
        return DB::update('tasks')
            ->set(['status' => $status])
            ->where('id', $id)
            ->execute();
    }

    public static function delete($id)
    {
        return DB::delete('tasks')->where('id', $id)->execute();
    }
}
