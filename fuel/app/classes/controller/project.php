<?php

class Controller_Project extends Controller
{
    public function before()
    {
        parent::before();
        
        // ログインチェック（セッションがない場合はログインページへ）
        if (!Session::get('user_name')) {
            Response::redirect('auth/login');
        }
    }

    public function action_index()
    {
        try {
            $user_name = Session::get('user_name');
            $projects = Model_Project::find('all', array(
                'where' => array('user_name' => $user_name),
                'order_by' => array('created_at' => 'desc')
            ));
            
            $data = array(
                'projects' => $projects,
                'current_user' => $user_name
            );
            
            return Response::forge(View::forge('project/index', $data));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラーが発生しました');
            return Response::forge(View::forge('project/index', array(
                'projects' => array(),
                'current_user' => Session::get('user_name')
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
                    $project = Model_Project::forge(array(
                        'name' => Input::post('name'),
                        'user_name' => Session::get('user_name'),
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                    $project->save();
                    
                    Session::set_flash('success', 'プロジェクトを追加しました');
                    Response::redirect('project');
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベースエラーが発生しました');
                }
            } else {
                Session::set_flash('error', 'プロジェクト名を入力してください');
            }
        }

        return Response::forge(View::forge('project/create', array(
            'current_user' => Session::get('user_name')
        )));
    }

    public function action_edit($id = null)
    {
        $project = Model_Project::find($id);
        if (!$project || $project->user_name !== Session::get('user_name')) {
            Session::set_flash('error', 'プロジェクトが見つかりません');
            Response::redirect('project');
        }

        if (Input::method() == 'POST') {
            $val = Validation::forge();
            $val->add('name', 'プロジェクト名')->add_rule('required');

            if ($val->run()) {
                try {
                    $project->name = Input::post('name');
                    $project->save();
                    
                    Session::set_flash('success', 'プロジェクトを更新しました');
                    Response::redirect('project');
                } catch (Exception $e) {
                    Session::set_flash('error', 'データベースエラーが発生しました');
                }
            } else {
                Session::set_flash('error', 'プロジェクト名を入力してください');
            }
        }

        return Response::forge(View::forge('project/edit', array(
            'project' => $project,
            'current_user' => Session::get('user_name')
        )));
    }

    public function action_delete($id = null)
    {
        try {
            $project = Model_Project::find($id);
            if ($project && $project->user_name === Session::get('user_name')) {
                $project->delete();
                Session::set_flash('success', 'プロジェクトを削除しました');
            } else {
                Session::set_flash('error', 'プロジェクトが見つかりません');
            }
        } catch (Exception $e) {
            Session::set_flash('error', '削除に失敗しました');
        }
        
        Response::redirect('project');
    }

    public function action_view($id = null)
    {
        try {
            $project = Model_Project::find($id);
            if (!$project || $project->user_name !== Session::get('user_name')) {
                Session::set_flash('error', 'プロジェクトが見つかりません');
                Response::redirect('project');
            }
            
            $tasks = Model_Task::find('all', array(
                'where' => array('project_id' => $id),
                'order_by' => array('created_at' => 'asc')
            ));
            
            return Response::forge(View::forge('project/view', array(
                'project' => $project,
                'tasks' => $tasks,
                'current_user' => Session::get('user_name')
            )));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラーが発生しました');
            Response::redirect('project');
        }
    }
}