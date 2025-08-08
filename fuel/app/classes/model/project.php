<?php

class Model_Project extends \Model
{
    public static function find_by_id_and_user($id, $user_id)
    {
        return DB::select('*')
            ->from('projects')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute()
            ->current();
    }

        public static function find_by_user_id($user_id)
    {
        return DB::select('*')
            ->from('projects')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'desc')
            ->execute();
    }

        public static function create_project($name, $user_id)
    {
        return DB::insert('projects')
            ->set(array(
                'name' => $name,
                'user_id' => $user_id,
                'created_at' => date('Y-m-d H:i:s')
            ))
            ->execute();
    }

    public static function update_project_name($id, $user_id, $new_name)
    {
        return DB::update('projects')
            ->set([
            'name' => $new_name,
            'updated_at' => date('Y-m-d H:i:s')
            ])
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute();
    }

    public static function exists_by_id_and_user($project_id, $user_id)
    {
    return DB::select('id')
        ->from('projects')
        ->where('id', $project_id)
        ->where('user_id', $user_id)
        ->execute()
    }

    public static function delete_by_id_and_user($id, $user_id)
    {
        return DB::delete('projects')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute();
    }
}
