<?php

class Controller_Task extends Controller
{
    public function before()
    {
        parent::before();
        
        // ログインチェック
        if (!Session::get('user_id')) {
            Response::redirect('auth/login');
        }
    }

    public function action_create($project_id = null)
    {
        // プロジェクトの存在確認
        $project = Model_Project::find($project_id);
        if (!$project || $project->user_id !== Session::get('user_id')) {
            Session::set_flash('error', 'プロジェクトが見つかりません');
            Response::redirect('project');
        }

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

                    $task = Model_Task::forge(array(
                        'project_id' => $project_id,
                        'name' => Input::post('name'),
                        'due_date' => $due_date,
                        'status' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                    $task->save();
                    
                    Session::set_flash('success', 'タスクを追加しました');
                    Response::redirect('project/view/'.$project_id);
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'タスク名を入力してください');
            }
        }

        return Response::forge(View::forge('task/create', array(
            'project' => $project,
            'current_user' => Session::get('user_id')
        )));
    }

    public function action_edit($id = null)
    {
        $task = Model_Task::find($id);
        if (!$task) {
            Session::set_flash('error', 'タスクが見つかりません');
            Response::redirect('project');
        }

        // タスクの所有者確認
        $project = Model_Project::find($task->project_id);
        if (!$project || $project->user_id !== Session::get('user_id')) {
            Session::set_flash('error', 'アクセス権限がありません');
            Response::redirect('project');
        }

        if (Input::method() == 'POST') {
            $val = Validation::forge();
            $val->add('name', 'タスク名')->add_rule('required');

            if ($val->run()) {
                try {
                    $task->name = Input::post('name');
                    $due_date = Input::post('due_date');
                    if (empty($due_date)) {
                        $due_date = date('Y-m-d');
                    }
                    $task->due_date = $due_date;
                    $task->save();
                    
                    Session::set_flash('success', 'タスクを更新しました');
                    Response::redirect('project/view/'.$task->project_id);
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'タスク名を入力してください');
            }
        }

        return Response::forge(View::forge('task/edit', array(
            'task' => $task,
            'project' => $project,
            'current_user' => Session::get('user_id')
        )));
    }

    public function action_delete($id = null)
    {
        try {
            $task = Model_Task::find($id);
            if (!$task) {
                Session::set_flash('error', 'タスクが見つかりません');
                Response::redirect('project');
            }

            // タスクの所有者確認
            $project = Model_Project::find($task->project_id);
            if (!$project || $project->user_id !== Session::get('user_id')) {
                Session::set_flash('error', 'アクセス権限がありません');
                Response::redirect('project');
            }

            $project_id = $task->project_id;
            $task->delete();
            Session::set_flash('success', 'タスクを削除しました');
            Response::redirect('project/view/'.$project_id);
            
        } catch (Exception $e) {
            Session::set_flash('error', '削除に失敗しました');
            Response::redirect('project');
        }
    }

    public function action_toggle_status($id = null)
    {
        try {
            $task = Model_Task::find($id);
            if (!$task) {
                return Response::forge(json_encode(array('success' => false)), 404);
            }

            // タスクの所有者確認
            $project = Model_Project::find($task->project_id);
            if (!$project || $project->user_id !== Session::get('user_id')) {
                return Response::forge(json_encode(array('success' => false)), 403);
            }

            $task->status = $task->status ? 0 : 1;
            $task->save();
            
            return Response::forge(json_encode(array(
                'success' => true,
                'status' => $task->status
            )));
            
        } catch (Exception $e) {
            return Response::forge(json_encode(array('success' => false)), 500);
        }
    }
}