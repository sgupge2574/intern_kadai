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
                    $user = Model_User::find('first', array(
                        'where' => array(
                            'username' => $name,
                            'password' => md5($password)
                        )
                    ));

                    if ($user) {
                        Session::set('user_id', $user->id);
                        Session::set_flash('success', $user->username . 'さん、おかえりなさい！');
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
                    $existing_user = Model_User::find('first', array(
                        'where' => array('username' => $name)
                    ));

                    if ($existing_user) {
                        Session::set_flash('error', 'このユーザー名は既に使用されています');
                    } else {
                        $user = Model_User::forge(array(
                            'username' => $name,
                            'password' => md5($password),
                            'created_at' => date('Y-m-d H:i:s')
                        ));
                        $user->save();

                        Session::set('user_id', $user->username);
                        Session::set_flash('success', $user->username . 'さん、アカウントを作成しました！');
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
        Session::set_flash('success', 'ログアウトしました');
        Response::redirect('auth/login');
    }
}