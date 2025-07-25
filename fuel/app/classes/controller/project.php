<?php

/**
 * プロジェクト管理コントローラー
 * プロジェクトのCRUD操作（作成、読み取り、更新、削除）を提供
 */
class Controller_Project extends Controller
{
    /**
     * コントローラー実行前の共通処理
     * 認証チェックを行い、未ログインユーザーをログイン画面にリダイレクト
     */
    public function before()
    {
        // 親クラスのbefore()メソッドを実行
        parent::before();
        
        // 認証不要のアクション名一覧（ログイン・登録画面）
        $no_auth = array('auth/login', 'auth/register');
        
        // 現在のURIが認証不要リストにない かつ ユーザーIDがセッションにない場合
        if (!in_array(Uri::string(), $no_auth) && !Session::get('user_id')) {
            // ログイン画面にリダイレクト
            Response::redirect('auth/login');
        }
    }

    /**
     * プロジェクト一覧表示
     * ログインユーザーが作成したプロジェクトの一覧を表示
     */
    public function action_index()
    {
        try {
            // セッションからユーザーIDを取得
            $user_id = Session::get('user_id');
            
            // データベースからユーザーのプロジェクトを取得（作成日時の降順）
            $result = DB::select('*')
                ->from('projects')
                ->where('user_id', $user_id)
                ->order_by('created_at', 'desc')
                ->execute();
            
            // 結果を配列として取得
            $projects = $result->as_array();
            
            // データのnullチェックと型変換を行い、安全なデータ構造を作成
            $clean_projects = array();
            foreach ($projects as $project) {
                $clean_projects[] = (object)array(
                    'id' => (int)$project['id'],                    // 整数型に変換
                    'name' => $project['name'] ?: '',               // null の場合は空文字
                    'created_at' => $project['created_at'] ?: '',   // null の場合は空文字
                    'user_id' => (int)$project['user_id']           // 整数型に変換
                );
            }
            
            // ビューに渡すデータを準備
            $data = array(
                'projects' => $clean_projects,
                'current_user' => Session::get('username') ?: ''
            );
            
            // プロジェクト一覧ビューを表示
            return Response::forge(View::forge('project/index', $data));
            
        } catch (Exception $e) {
            // データベースエラー時の処理
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            
            // エラー時は空のプロジェクト配列でビューを表示
            return Response::forge(View::forge('project/index', array(
                'projects' => array(),
                'current_user' => Session::get('username') ?: ''
            )));
        }
    }

    /**
     * プロジェクト作成処理
     * 新規プロジェクトの作成フォーム表示と作成処理
     */
    public function action_create()
    {
        // POSTリクエストの場合（フォーム送信時）
        if (Input::method() == 'POST') {
            // バリデーション設定
            $val = Validation::forge();
            $val->add('name', 'プロジェクト名')->add_rule('required');
            
            // バリデーション実行
            if ($val->run()) {
                try {
                    // データベースに新規プロジェクトを挿入
                    DB::insert('projects')
                        ->set(array(
                            'name' => Input::post('name'),
                            'user_id' => Session::get('user_id'),
                            'created_at' => date('Y-m-d H:i:s')
                        ))
                        ->execute();
                    
                    // 成功メッセージを設定
                    Session::set_flash('success', 'プロジェクトを追加しました');
                    
                    // プロジェクト一覧にリダイレクト
                    Response::redirect('project');
                    
                } catch (Exception $e) {
                    // データベースエラー時の処理
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                // バリデーションエラー時の処理
                Session::set_flash('error', 'プロジェクト名を入力してください');
            }
        }

        // プロジェクト作成フォームを表示
        return Response::forge(View::forge('project/create', array(
            'current_user' => Session::get('username') ?: ''
        )));
    }

    /**
     * プロジェクト編集処理
     * 既存プロジェクトの編集フォーム表示と更新処理
     * 
     * @param int $id プロジェクトID
     */
    public function action_edit($id = null)
    {
        try {
            // 指定されたIDのプロジェクトを取得（ユーザー権限チェック付き）
            $result = DB::select('*')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))  // 他ユーザーのプロジェクトは編集不可
                ->execute();
            
            // プロジェクトが見つからない場合
            if ($result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                Response::redirect('project');
            }
            
            $project_data = $result->current();
            
            // POSTリクエストの場合（フォーム送信時）
            if (Input::method() == 'POST') {
                // バリデーション設定
                $val = Validation::forge();
                $val->add('name', 'プロジェクト名')->add_rule('required');
                
                // バリデーション実行
                if ($val->run()) {
                    try {
                        // データベースのプロジェクト情報を更新
                        DB::update('projects')
                            ->set(array('name' => Input::post('name')))
                            ->where('id', $id)
                            ->where('user_id', Session::get('user_id'))  // セキュリティ確保
                            ->execute();
                        
                        // 成功メッセージを設定
                        Session::set_flash('success', 'プロジェクトを更新しました');
                        
                        // プロジェクト一覧にリダイレクト
                        Response::redirect('project');
                        
                    } catch (Exception $e) {
                        // データベースエラー時の処理
                        Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                    }
                } else {
                    // バリデーションエラー時の処理
                    Session::set_flash('error', 'プロジェクト名を入力してください');
                }
            }

            // データのクリーニングと型変換
            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

            // プロジェクト編集フォームを表示
            return Response::forge(View::forge('project/edit', array(
                'project' => $project,
                'current_user' => Session::get('username') ?: ''
            )));
            
        } catch (Exception $e) {
            // データベースエラー時の処理
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            Response::redirect('project');
        }
    }

    /**
     * プロジェクト削除処理
     * プロジェクトと関連するタスクを削除
     * 
     * @param int $id プロジェクトID
     */
    public function action_delete($id = null)
    {
        try {
            // プロジェクトの存在確認（ユーザー権限チェック付き）
            $result = DB::select('id')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))  // 他ユーザーのプロジェクトは削除不可
                ->execute();
            
            // プロジェクトが存在する場合
            if ($result->count() > 0) {
                // 関連するタスクを先に削除（外部キー制約対応）
                DB::delete('tasks')
                    ->where('project_id', $id)
                    ->execute();
                
                // プロジェクトを削除
                DB::delete('projects')
                    ->where('id', $id)
                    ->where('user_id', Session::get('user_id'))  // セキュリティ確保
                    ->execute();
                
                // 成功メッセージを設定
                Session::set_flash('success', 'プロジェクトを削除しました');
            } else {
                // プロジェクトが見つからない場合
                Session::set_flash('error', 'プロジェクトが見つかりません');
            }
            
        } catch (Exception $e) {
            // データベースエラー時の処理
            Session::set_flash('error', '削除に失敗しました: ' . $e->getMessage());
        }
        
        // プロジェクト一覧にリダイレクト
        Response::redirect('project');
    }

    /**
     * プロジェクト詳細表示
     * プロジェクト情報と関連するタスク一覧を表示
     * 
     * @param int $id プロジェクトID
     */
    public function action_view($id = null)
    {
        try {
            // プロジェクトの取得（ユーザー権限チェック付き）
            $project_result = DB::select('*')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))  // 他ユーザーのプロジェクトは閲覧不可
                ->execute();
            
            // プロジェクトが見つからない場合
            if ($project_result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                Response::redirect('project');
            }
            
            $project_data = $project_result->current();
            
            // 関連するタスクの取得（作成日時の昇順）
            $tasks_result = DB::select('*')
                ->from('tasks')
                ->where('project_id', $id)
                ->order_by('created_at', 'asc')
                ->execute();
            
            $tasks_data = $tasks_result->as_array();

            // プロジェクトデータのクリーニングとオブジェクト変換
            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

            // タスクデータのクリーニングとオブジェクト変換
            $tasks = array();
            foreach ($tasks_data as $task) {
                $tasks[] = (object)array(
                    'id' => (int)$task['id'],
                    'project_id' => (int)$task['project_id'],
                    'name' => $task['name'] ?: '',
                    'due_date' => $task['due_date'] ?: null,
                    'status' => (int)$task['status'],
                    'created_at' => $task['created_at'] ?: ''
                );
            }

            // プロジェクト詳細ビューを表示
            return Response::forge(View::forge('project/view', array(
                'project' => $project,
                'tasks' => $tasks,
                'current_user' => Session::get('username') ?: ''
            )));
            
        } catch (Exception $e) {
            // データベースエラー時の処理
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            Response::redirect('project');
        }
    }
}