<?php

class Controller_Auth extends Controller
{
    public function action_login()
    {
        // 既にログイン済みの場合はリダイレクト
        if (Session::get('user_id')) {
            Response::redirect('project');
        }

        if (Input::method() == 'POST') {
            $name = Input::post('name');
            $password = Input::post('password');
            
            $val = Validation::forge();
            $val->add('name', 'ユーザー名')->add_rule('required');
            $val->add('password', 'パスワード')->add_rule('required');

            if ($val->run()) {
                try {
                    $result = DB::select('*')
                        ->from('users')
                        ->where('username', $name)
                        ->where('password', md5($password))
                        ->execute();

                    if ($result->count() > 0) {
                        $user = $result->current();
                        Session::set('user_id', $user['id']);
                        Session::set('username', $user['username']);
                        Session::set_flash('success', $user['username'] . 'さん、おかえりなさい！');
                        Response::redirect('project');
                    } else {
                        Session::set_flash('error', 'ユーザー名またはパスワードが間違っています');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', '入力内容を確認してください');
            }
        }

        return Response::forge(View::forge('auth/login'));
    }

    public function action_register()
    {
        // 既にログイン済みの場合はリダイレクト
        if (Session::get('user_id')) {
            Response::redirect('project');
        }

        if (Input::method() == 'POST') {
            $name = Input::post('name');
            $password = Input::post('password');
            
            $val = Validation::forge();
            $val->add('name', 'ユーザー名')->add_rule('required')->add_rule('min_length', 2);
            $val->add('password', 'パスワード')->add_rule('required')->add_rule('min_length', 4);

            if ($val->run()) {
                try {
                    // ユーザー名の重複チェック
                    $existing = DB::select('id')
                        ->from('users')
                        ->where('username', $name)
                        ->execute();

                    if ($existing->count() > 0) {
                        Session::set_flash('error', 'このユーザー名は既に使用されています');
                    } else {
                        // 新規ユーザー作成
                        $result = DB::insert('users')
                            ->set(array(
                                'username' => $name,
                                'password' => md5($password),
                                'created_at' => date('Y-m-d H:i:s')
                            ))
                            ->execute();

                        $user_id = $result[0]; // 挿入されたIDを取得

                        Session::set('user_id', $user_id);
                        Session::set('username', $name);
                        Session::set_flash('success', $name . 'さん、アカウントを作成しました！');
                        Response::redirect('project');
                    }
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', '入力内容を確認してください');
            }
        }

        return Response::forge(View::forge('auth/register'));
    }

    public function action_logout()
    {
        Session::delete('user_id');
        Session::delete('username');
        Session::set_flash('success', 'ログアウトしました');
        Response::redirect('auth/login');
    }
}