<?php
class Model_User extends Model
{
    /**
     * ユーザー名とパスワードで認証を行う
     */
    public static function authenticate($username, $password)
    {
        return DB::select('*')
            ->from('users')
            ->where('username', $username)
            ->where('password', md5($password))  
            ->execute()
            ->current();
    }

    /**
     * 指定したユーザー名がすでに存在するかチェックする
     */
    public static function exists_by_username($username)
    {
        $result = DB::select('id')
            ->from('users')
            ->where('username', $username)
            ->execute();

        return $result->count() > 0;
    }

    /**
     * 新規ユーザーをデータベースに登録する
     */
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
