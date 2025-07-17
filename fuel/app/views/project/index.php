<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロジェクト管理</title>
	 <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1 class="title">プロジェクト管理</h1>
            <div class="user-info">
                <span class="user-name">こんにちは
                <button class="logout-btn" onclick="logout()">ログアウト</button>
            </div>
        </div>

        <!-- フラッシュメッセージ -->
        <?php if (Session::get_flash('success')): ?>
            <div class="flash-message flash-success">
                <?php echo Session::get_flash('success'); ?>
            </div>
        <?php endif; ?>

        <?php if (Session::get_flash('error')): ?>
            <div class="flash-message flash-error">
                <?php echo Session::get_flash('error'); ?>
            </div>
        <?php endif; ?>

        <!-- プロジェクト一覧セクション -->
        <div data-bind="with: projectViewModel">
            <div class="section-header">
                <h2 class="section-title">プロジェクト一覧</h2>
                <a href="<?php echo Uri::create('project/create'); ?>" class="btn btn-primary">
                    + 新しいプロジェクト
                </a>
            </div>

            <!-- 空の状態 -->
            <div class="empty-state" data-bind="visible: projects().length === 0">
                <p class="empty-message">プロジェクトがありません</p>
                <a href="<?php echo Uri::create('project/create'); ?>" class="btn btn-primary">
                    最初のプロジェクトを作成
                </a>
            </div>

            <!-- プロジェクト一覧 -->
            <div class="project-list" data-bind="visible: projects().length > 0, foreach: projects">
                <div class="project-item">
                    <div class="project-content">
                        <div class="project-info" data-bind="click: $parent.viewProject">
                            <h3 class="project-name" data-bind="text: name"></h3>
                            <p class="project-date" data-bind="text: created_at"></p>
                        </div>
                        <div class="project-actions">
                            <button class="btn btn-edit" data-bind="click: $parent.editProject">
                                編集
                            </button>
                            <button class="btn btn-danger" data-bind="click: $parent.deleteProject">
                                削除
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Knockout.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/knockout/3.5.1/knockout-min.js"></script>
    
    <script>
        // プロジェクトViewModel
        function ProjectViewModel() {
            var self = this;
            
            // プロジェクトデータ
            self.projects = ko.observableArray([
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $index => $project): ?>
                        {
                            id: <?php echo $project->id; ?>,
                            name: '<?php echo addslashes($project->name); ?>',
                            created_at: '<?php echo $project->created_at; ?>'
                        }<?php echo ($index < count($projects) - 1) ? ',' : ''; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            ]);
            
            // プロジェクト詳細表示
            self.viewProject = function(project) {
                window.location.href = '<?php echo Uri::create('project/view'); ?>/' + project.id;
            };
            
            // プロジェクト編集
            self.editProject = function(project) {
                window.location.href = '<?php echo Uri::create('project/edit'); ?>/' + project.id;
            };
            
            // プロジェクト削除
            self.deleteProject = function(project) {
                if (confirm('本当に削除しますか？')) {
                    window.location.href = '<?php echo Uri::create('project/delete'); ?>/' + project.id;
                }
            };
        }

        // ViewModelをバインド
        var projectViewModel = new ProjectViewModel();
        ko.applyBindings({ projectViewModel: projectViewModel });

        // ログアウト機能
        function logout() {
            if (confirm('ログアウトしますか？')) {
                window.location.href = '<?php echo Uri::create('auth/logout'); ?>';
            }
        }
    </script>
</body>
</html>