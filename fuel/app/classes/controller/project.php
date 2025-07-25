<?php

class Controller_Project extends Controller
{
    public function before()
    {
        parent::before();
        
        // 認証不要のアクション名一覧
        $no_auth = array('auth/login', 'auth/register');
        if (!in_array(Uri::string(), $no_auth) && !Session::get('user_id')) {
            Response::redirect('auth/login');
        }
    }

    public function action_index()
    {
        try {
            $user_id = Session::get('user_id');
            
            $result = DB::select('*')
                ->from('projects')
                ->where('user_id', $user_id)
                ->order_by('created_at', 'desc')
                ->execute();

            $projects = $result->as_array();
            
            // データのnullチェックと変換
            $clean_projects = array();
            foreach ($projects as $project) {
                $clean_projects[] = (object)array(
                    'id' => (int)$project['id'],
                    'name' => $project['name'] ?: '',
                    'created_at' => $project['created_at'] ?: '',
                    'user_id' => (int)$project['user_id']
                );
            }
            
            $data = array(
                'projects' => $clean_projects,
                'current_user' => Session::get('username') ?: ''
            );
            
            return Response::forge(View::forge('project/index', $data));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            return Response::forge(View::forge('project/index', array(
                'projects' => array(),
                'current_user' => Session::get('username') ?: ''
            )));
        }
    }

    public function action_create()
    {
        if (Input::method() == 'POST') {
            $val = Validation::forge();
            $val->add('name', 'プロジェクト名')->add_rule('required');

            if ($val->run()) {
                try {
                    DB::insert('projects')
                        ->set(array(
                            'name' => Input::post('name'),
                            'user_id' => Session::get('user_id'),
                            'created_at' => date('Y-m-d H:i:s')
                        ))
                        ->execute();
                    
                    Session::set_flash('success', 'プロジェクトを追加しました');
                    Response::redirect('project');
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'プロジェクト名を入力してください');
            }
        }

        return Response::forge(View::forge('project/create', array(
            'current_user' => Session::get('username') ?: ''
        )));
    }

    public function action_edit($id = null)
    {
        try {
            $result = DB::select('*')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                Response::redirect('project');
            }

            $project_data = $result->current();

            if (Input::method() == 'POST') {
                $val = Validation::forge();
                $val->add('name', 'プロジェクト名')->add_rule('required');

                if ($val->run()) {
                    try {
                        DB::update('projects')
                            ->set(array('name' => Input::post('name')))
                            ->where('id', $id)
                            ->where('user_id', Session::get('user_id'))
                            ->execute();
                        
                        Session::set_flash('success', 'プロジェクトを更新しました');
                        Response::redirect('project');
                    } catch (Exception $e) {
                        Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                    }
                } else {
                    Session::set_flash('error', 'プロジェクト名を入力してください');
                }
            }

            // データのクリーニング
            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

            return Response::forge(View::forge('project/edit', array(
                'project' => $project,
                'current_user' => Session::get('username') ?: ''
            )));

        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            Response::redirect('project');
        }
    }

    public function action_delete($id = null)
    {
        try {
            // プロジェクトの存在確認
            $result = DB::select('id')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($result->count() > 0) {
                // 関連するタスクも削除
                DB::delete('tasks')
                    ->where('project_id', $id)
                    ->execute();

                // プロジェクトを削除
                DB::delete('projects')
                    ->where('id', $id)
                    ->where('user_id', Session::get('user_id'))
                    ->execute();

                Session::set_flash('success', 'プロジェクトを削除しました');
            } else {
                Session::set_flash('error', 'プロジェクトが見つかりません');
            }
        } catch (Exception $e) {
            Session::set_flash('error', '削除に失敗しました: ' . $e->getMessage());
        }
        
        Response::redirect('project');
    }

    public function action_view($id = null)
    {
        try {
            // プロジェクトの取得
            $project_result = DB::select('*')
                ->from('projects')
                ->where('id', $id)
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($project_result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                Response::redirect('project');
            }

            $project_data = $project_result->current();

            // タスクの取得
            $tasks_result = DB::select('*')
                ->from('tasks')
                ->where('project_id', $id)
                ->order_by('created_at', 'asc')
                ->execute();

            $tasks_data = $tasks_result->as_array();

            // データのクリーニングとオブジェクト変換
            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

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

            return Response::forge(View::forge('project/view', array(
                'project' => $project,
                'tasks' => $tasks,
                'current_user' => Session::get('username') ?: ''
            )));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            Response::redirect('project');
        }
    }
}