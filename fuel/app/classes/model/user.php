<?php
class Model_User extends Model
{
    public static function authenticate($username, $password)
    {
        return DB::select('*')
            ->from('users')
            ->where('username', $username)
            ->where('password', md5($password))  
            ->execute()
            ->current();
    }

        public static function exists_by_username($username)
    {
        $result = DB::select('id')
            ->from('users')
            ->where('username', $username)
            ->execute();

        return $result->count() > 0;
    }

        public static function create_user($username, $password)
    {
        return DB::insert('users')
            ->set([
                'username' => $username,
                'password' => md5($password),
                'created_at' => date('Y-m-d H:i:s'),
            ])
            ->execute();
    }
}
