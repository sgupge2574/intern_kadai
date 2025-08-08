<?php

class Controller_Task extends Controller
{
    public function before()
    {
        parent::before();
        
        // ログインチェック
        if (!Session::get('user_id')) {
            return Response::redirect('auth/login');
        }
    }

    public function action_create($project_id = null)
    {
        try {
            // プロジェクトの存在確認
            $user_id = Session::get('user_id');
            $project_result = Model_Project::find_by_id_and_user($project_id, $user_id);

            if ($project_result->count() == 0) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                return Response::redirect('project');
            }

            $project_data = $project_result->current();

            if (Input::method() == 'POST') {
                $val = Validation::forge();
                $val->add('name', 'タスク名')->add_rule('required');

                if ($val->run()) {
                    try {
                        // due_dateを取得し、nullまたは空文字なら今日の日付をセット
                        $due_date = Input::post('due_date');
                        if (empty($due_date)) {
                            $due_date = date('Y-m-d');
                        }

                        Model_Task::create_task($project_id, Input::post('name'), $due_date);
                        
                        Session::set_flash('success', 'タスクを追加しました');
                        return Response::redirect('project/view/'.$project_id);
                    } catch (Exception $e) {
                        Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                    }
                } else {
                    Session::set_flash('error', 'タスク名を入力してください');
                }
            }

            // データのクリーニング
            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

            return Response::forge(View::forge('task/create', array(
                'project' => $project,
                'current_user' => Session::get('username') ?: ''
            )));

        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            return Response::redirect('project');
        }
    }

    public function action_edit($id = null)
    {
        try {
            // タスクの取得
            $task_result = DB::select('*')
                ->from('tasks')
                ->where('id', $id)
                ->execute();

            if ($task_result->count() == 0) {
                Session::set_flash('error', 'タスクが見つかりません');
                return Response::redirect('project');
            }

            $task_data = $task_result->current();

            // タスクの所有者確認
            $project_result = DB::select('*')
                ->from('projects')
                ->where('id', $task_data['project_id'])
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($project_result->count() == 0) {
                Session::set_flash('error', 'アクセス権限がありません');
                return Response::redirect('project');
            }

            $project_data = $project_result->current();

            if (Input::method() == 'POST') {
                $val = Validation::forge();
                $val->add('name', 'タスク名')->add_rule('required');

                if ($val->run()) {
                    try {
                        $due_date = Input::post('due_date');
                        if (empty($due_date)) {
                            $due_date = date('Y-m-d');
                        }

                        DB::update('tasks')
                            ->set(array(
                                'name' => Input::post('name'),
                                'due_date' => $due_date
                            ))
                            ->where('id', $id)
                            ->execute();
                        
                        Session::set_flash('success', 'タスクを更新しました');
                        return Response::redirect('project/view/'.$task_data['project_id']);
                    } catch (Exception $e) {
                        Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                    }
                } else {
                    Session::set_flash('error', 'タスク名を入力してください');
                }
            }

            // データのクリーニング
            $task = (object)array(
                'id' => (int)$task_data['id'],
                'project_id' => (int)$task_data['project_id'],
                'name' => $task_data['name'] ?: '',
                'due_date' => $task_data['due_date'] ?: '',
                'status' => (int)$task_data['status'],
                'created_at' => $task_data['created_at'] ?: ''
            );

            $project = (object)array(
                'id' => (int)$project_data['id'],
                'name' => $project_data['name'] ?: '',
                'created_at' => $project_data['created_at'] ?: '',
                'user_id' => (int)$project_data['user_id']
            );

            return Response::forge(View::forge('task/edit', array(
                'task' => $task,
                'project' => $project,
                'current_user' => Session::get('username') ?: ''
            )));

        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            return Response::redirect('project');
        }
    }

    public function action_delete($id = null)
    {
        try {
            // タスクの取得
            $task_result = DB::select('*')
                ->from('tasks')
                ->where('id', $id)
                ->execute();

            if ($task_result->count() == 0) {
                Session::set_flash('error', 'タスクが見つかりません');
                return Response::redirect('project');
            }

            $task_data = $task_result->current();

            // タスクの所有者確認
            $project_result = DB::select('id')
                ->from('projects')
                ->where('id', $task_data['project_id'])
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($project_result->count() == 0) {
                Session::set_flash('error', 'アクセス権限がありません');
                return Response::redirect('project');
            }

            $project_id = $task_data['project_id'];

            // タスクを削除
            DB::delete('tasks')
                ->where('id', $id)
                ->execute();

            Session::set_flash('success', 'タスクを削除しました');
            return Response::redirect('project/view/'.$project_id);
            
        } catch (Exception $e) {
            Session::set_flash('error', '削除に失敗しました: ' . $e->getMessage());
            return Response::redirect('project');
        }
    }

    public function action_toggle_status($id = null)
    {
        try {
            // タスクの取得
            $task_result = DB::select('*')
                ->from('tasks')
                ->where('id', $id)
                ->execute();

            if ($task_result->count() == 0) {
                return Response::forge(json_encode(array('success' => false)), 404);
            }

            $task_data = $task_result->current();

            // タスクの所有者確認
            $project_result = DB::select('id')
                ->from('projects')
                ->where('id', $task_data['project_id'])
                ->where('user_id', Session::get('user_id'))
                ->execute();

            if ($project_result->count() == 0) {
                return Response::forge(json_encode(array('success' => false)), 403);
            }

            // ステータスを切り替え
            $new_status = $task_data['status'] ? 0 : 1;

            DB::update('tasks')
                ->set(array('status' => $new_status))
                ->where('id', $id)
                ->execute();
            
            return Response::forge(json_encode(array(
                'success' => true,
                'status' => $new_status
            )));
            
        } catch (Exception $e) {
            return Response::forge(json_encode(array('success' => false)), 500);
        }
    }
}