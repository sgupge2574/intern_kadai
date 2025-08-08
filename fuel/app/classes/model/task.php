<?php
class Model_Task extends \Model
{
    public static function create_task($project_id, $name, $due_date)
    {
        return DB::insert('tasks')
            ->set(array(
                'project_id' => $project_id,
                'name' => $name,
                'due_date' => $due_date,
                'status' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ))
            ->execute();
    }

    public static function find_by_id($id)
    {
        $result = DB::select('*')
            ->from('tasks')
            ->where('id', $id)
            ->execute();

        return $result->count() > 0 ? $result->current() : null;
    }

    public static function update_task($id, $name, $due_date)
    {
        return DB::update('tasks')
            ->set(array(
                'name' => $name,
                'due_date' => $due_date
            ))
            ->where('id', $id)
            ->execute();
    }

        public static function delete_by_id($id)
    {
        return DB::delete('tasks')
            ->where('id', $id)
            ->execute();
    }

     public static function update_status($id, $new_status)
    {
        return DB::update('tasks')
            ->set(['status' => $new_status])
            ->where('id', $id)
            ->execute();
    }
}
