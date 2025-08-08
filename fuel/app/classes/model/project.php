<?php

class Model_Project extends \Model
{
    public static function find_by_id_and_user($id, $user_id)
    {
    /**
    * 指定されたIDとユーザーIDに一致するプロジェクトを取得
    */
        return DB::select('*')
            ->from('projects')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute()
            ->current();
    }

    /**
     * 指定ユーザーの全プロジェクトを取得（作成日の降順）
     */
    public static function find_by_user_id($user_id)
    {
        return DB::select('*')
            ->from('projects')
            ->where('user_id', $user_id)
            ->order_by('created_at', 'desc')
            ->execute();
    }
    /**
    * 新しいプロジェクトを作成する
    */
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

     /**
     * プロジェクト名を更新する（更新日時もセット）
     */
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

    /**
     * プロジェクトが指定されたIDとユーザーIDで存在するか確認
     */
    public static function exists_by_id_and_user($project_id, $user_id)
    {
        return DB::select('id')
            ->from('projects')
            ->where('id', $project_id)
            ->where('user_id', $user_id)
            ->execute()
    }
    /**
     * プロジェクトを削除（ユーザーID付きで安全に）
     */
    public static function delete_by_id_and_user($id, $user_id)
    {
        return DB::delete('projects')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->execute();
    }
}
