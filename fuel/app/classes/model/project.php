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
}
