<?php
class Model_Task extends \Model
{
    /**
     * 新しいタスクを作成する
     */
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
    /**
     * IDでタスクを取得する
     */
    public static function find_by_id($id)
    {
        $result = DB::select('*')
            ->from('tasks')
            ->where('id', $id)
            ->execute();

        return $result->count() > 0 ? $result->current() : null;
    }
    /**
     * タスクの内容を更新する
     */
    public static function update_task($id, $name, $due_date)
    {
        return DB::update('tasks')
            ->set(array(
                'name' => $name,
                'due_date' => $due_date
                'updated_at' => date('Y-m-d H:i:s')
            ))
            ->where('id', $id)
            ->execute();
    }
    /**
     * タスクをIDで削除する
     */
    public static function delete_by_id($id)
    {
        return DB::delete('tasks')
            ->where('id', $id)
            ->execute();
    }

    /**
     * タスクのステータスを更新する
     */
    public static function update_status($id, $new_status)
    {
        return DB::update('tasks')
            ->set(['status' => $new_status])
            ->where('id', $id)
            ->execute();
    }
    /**
     * 指定プロジェクトのタスク一覧を作成日時昇順で取得する
     */
    public static function find_by_project_id_ordered($project_id)
    {
        return DB::select('*')
            ->from('tasks')
            ->where('project_id', $project_id)
            ->order_by('created_at', 'asc')
            ->execute()
    }
}
