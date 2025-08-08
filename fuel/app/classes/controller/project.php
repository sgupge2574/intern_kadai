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

        // ユーザーIDがセッションにない場合
        if (!Session::get('user_id')) {
            // ログイン画面にリダイレクト
            return Response::redirect('auth/login');
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
            $result = Model_Project::find_by_user_id($user_id);
            
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
        if (Input::method() === 'POST') {
            // バリデーション設定
            $val = Validation::forge();
            $val->add('name', 'プロジェクト名')->add_rule('required');
            
            // バリデーション実行
            if ($val->run()) {
                try {
                    // データベースに新規プロジェクトを挿入
                    Model_Project::create_project(Input::post('name'), Session::get('user_id'));

                    
                    // 成功メッセージを設定
                    Session::set_flash('success', 'プロジェクトを追加しました');
                    
                    // プロジェクト一覧にリダイレクト
                    return Response::redirect('project');
                    
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
            $result = Model_Project::find_by_id_and_user($id, Session::get('user_id'));
            
            // プロジェクトが見つからない場合
            if ($result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                return Response::redirect('project');
            }
            
            $project_data = $result->current();
            
            // POSTリクエストの場合（フォーム送信時）
            if (Input::method() === 'POST') {
                // バリデーション設定
                $val = Validation::forge();
                $val->add('name', 'プロジェクト名')->add_rule('required');
                
                // バリデーション実行
                if ($val->run()) {
                    try {
                        // データベースのプロジェクト情報を更新
                        Model_Project::update_project_name($id, Session::get('user_id'), Input::post('name'));

                        
                        // 成功メッセージを設定
                        Session::set_flash('success', 'プロジェクトを更新しました');
                        
                        // プロジェクト一覧にリダイレクト
                        return Response::redirect('project');
                        
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
            return Response::redirect('project');
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
            $result = Model_Project::find_by_id_and_user($id, Session::get('user_id'));
            
            // プロジェクトが存在する場合
            if ($result->count() > 0) {
                // 関連するタスクを先に削除（外部キー制約対応）
                Model_Task::delete_by_project_id($id);
                
                // プロジェクトを削除
                Model_Project::delete_by_id_and_user($id, Session::get('user_id'));
                
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
        return Response::redirect('project');
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
            $project_result = Model_Project::find_by_id_and_user($id, Session::get('user_id'));
            
            // プロジェクトが見つからない場合
            if ($project_result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                return Response::redirect('project');
            }
            
            // 関連するタスクの取得（作成日時の昇順）
            $tasks_result = Model_Task::find_by_project_id_ordered($id);
            
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
            return Response::redirect('project');
        }
    }
}