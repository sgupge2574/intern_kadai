<?php

/**
 * 認証コントローラー
 * ユーザーのログイン、登録、ログアウト機能を提供
 */
class Controller_Auth extends Controller
{
    /**
     * ログイン処理
     * ユーザー名とパスワードによる認証を行う
     */
    public function action_login()
    {
        // 既にログイン済みの場合はプロジェクト一覧にリダイレクト
        if (Session::get('user_id')) {
            return Response::redirect('project');
        }

        // POSTリクエストの場合（ログインフォーム送信時）
        if (Input::method() == 'POST') {
            // フォームデータを取得
            $name = Input::post('name');
            $password = Input::post('password');
            
            // バリデーション設定
            $val = Validation::forge();
            $val->add('name', 'ユーザー名')->add_rule('required');
            $val->add('password', 'パスワード')->add_rule('required');
            
            // バリデーション実行
            if ($val->run()) {
                try {
                    // データベースからユーザー情報を検索
                    // 注意: 実際のプロダクションではmd5ではなく、より安全なハッシュ化を使用すべき
                    $result = DB::select('*')
                        ->from('users')
                        ->where('username', $name)
                        ->where('password', md5($password))
                        ->execute();
                    
                    // ユーザーが見つかった場合
                    if ($result->count() > 0) {
                        $user = $result->current();
                        
                        // セッションにユーザー情報を保存
                        Session::set('user_id', $user['id']);
                        Session::set('username', $user['username']);
                        
                        // 成功メッセージを設定
                        Session::set_flash('success', $user['username'] . 'さん、おかえりなさい！');
                        
                        // プロジェクト一覧にリダイレクト
                        return Response::redirect('project');
                    } else {
                        // 認証失敗時のエラーメッセージ
                        Session::set_flash('error', 'ユーザー名またはパスワードが間違っています');
                    }
                } catch (Exception $e) {
                    // データベースエラー時の処理
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                // バリデーションエラー時の処理
                Session::set_flash('error', '入力内容を確認してください');
            }
        }

        // ログインページを表示
        return Response::forge(View::forge('auth/login'));
    }

    /**
     * ユーザー登録処理
     * 新規ユーザーアカウントの作成を行う
     */
    public function action_register()
    {
        // 既にログイン済みの場合はプロジェクト一覧にリダイレクト
        if (Session::get('user_id')) {
            return Response::redirect('project');
        }

        // POSTリクエストの場合（登録フォーム送信時）
        if (Input::method() == 'POST') {
            // フォームデータを取得
            $name = Input::post('name');
            $password = Input::post('password');
            
            // バリデーション設定
            $val = Validation::forge();
            $val->add('name', 'ユーザー名')
                ->add_rule('required')
                ->add_rule('min_length', 2); // 最小2文字
            $val->add('password', 'パスワード')
                ->add_rule('required')
                ->add_rule('min_length', 4); // 最小4文字
            
            // バリデーション実行
            if ($val->run()) {
                try {
                    // ユーザー名の重複チェック
                    $existing = DB::select('id')
                        ->from('users')
                        ->where('username', $name)
                        ->execute();
                    
                    // 既に同じユーザー名が存在する場合
                    if ($existing->count() > 0) {
                        Session::set_flash('error', 'このユーザー名は既に使用されています');
                    } else {
                        // 新規ユーザーをデータベースに挿入
                        // 注意: 実際のプロダクションではmd5ではなく、より安全なハッシュ化を使用すべき
                        $result = DB::insert('users')
                            ->set(array(
                                'username' => $name,
                                'password' => md5($password),
                                'created_at' => date('Y-m-d H:i:s')
                            ))
                            ->execute();
                        
                        // 挿入されたユーザーIDを取得
                        $user_id = $result[0];
                        
                        // セッションにユーザー情報を保存（自動ログイン）
                        Session::set('user_id', $user_id);
                        Session::set('username', $name);
                        
                        // 成功メッセージを設定
                        Session::set_flash('success', $name . 'さん、アカウントを作成しました！');
                        
                        // プロジェクト一覧にリダイレクト
                        return Response::redirect('project');
                    }
                } catch (Exception $e) {
                    // データベースエラー時の処理
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                // バリデーションエラー時の処理
                Session::set_flash('error', '入力内容を確認してください');
            }
        }

        // 登録ページを表示
        return Response::forge(View::forge('auth/register'));
    }

    /**
     * ログアウト処理
     * セッションを削除してログイン画面にリダイレクト
     */
    public function action_logout()
    {
        // セッションからユーザー情報を削除
        Session::delete('user_id');
        Session::delete('username');
        
        // ログアウト完了メッセージを設定
        Session::set_flash('success', 'ログアウトしました');
        
        // ログイン画面にリダイレクト
        return Response::redirect('auth/login');
    }
}