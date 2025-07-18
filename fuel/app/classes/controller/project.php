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
            $projects = Model_Project::find('all', array(
                'where' => array('user_id' => $user_id),
                'order_by' => array('created_at' => 'desc')
            ));
            
            $data = array(
                'projects' => $projects,
                'current_user' => $user_id
            );
            
            return Response::forge(View::forge('project/index', $data));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            return Response::forge(View::forge('project/index', array(
                'projects' => array(),
                'current_user' => Session::get('user_id')
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
                        'user_id' => Session::get('user_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ));
                    $project->save();
                    
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
            'current_user' => Session::get('user_id')
        )));
    }

    public function action_edit($id = null)
    {
        $project = Model_Project::find($id);
        if (!$project || $project->user_id !== Session::get('user_id')) {
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
                    Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
                }
            } else {
                Session::set_flash('error', 'プロジェクト名を入力してください');
            }
        }

        return Response::forge(View::forge('project/edit', array(
            'project' => $project,
            'current_user' => Session::get('user_id')
        )));
    }

    public function action_delete($id = null)
    {
        try {
            $project = Model_Project::find($id);
            if ($project && $project->user_id === Session::get('user_id')) {
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
            if (!$project || $project->user_id !== Session::get('user_id')) {
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
                'current_user' => Session::get('user_id')
            )));
            
        } catch (Exception $e) {
            Session::set_flash('error', 'データベース接続エラー: ' . $e->getMessage());
            Response::redirect('project');
        }
    }
}