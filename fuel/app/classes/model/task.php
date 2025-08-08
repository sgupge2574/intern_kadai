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

}
