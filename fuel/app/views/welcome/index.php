<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロジェクト管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1024px;
            margin: 0 auto;
            padding: 24px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        
        .user-name {
            font-size: 14px;
            color: #6b7280;
        }
        
        .logout-btn {
            font-size: 14px;
            color: #2563eb;
            text-decoration: underline;
            background: none;
            border: none;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            color: #1d4ed8;
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 500;
            color: #374151;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            border: none;
        }
        
        .btn-primary {
            background-color: #2563eb;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }
        
        .btn-secondary:hover {
            background-color: #e5e7eb;
        }
        
        .btn-danger {
            color: #dc2626;
            background: none;
            padding: 4px 12px;
            font-size: 14px;
        }
        
        .btn-danger:hover {
            color: #b91c1c;
        }
        
        .btn-edit {
            color: #2563eb;
            background: none;
            padding: 4px 12px;
            font-size: 14px;
        }
        
        .btn-edit:hover {
            color: #1d4ed8;
        }
        
        .empty-state {
            text-align: center;
            padding: 48px 0;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            background-color: white;
        }
        
        .empty-message {
            color: #6b7280;
            margin-bottom: 16px;
        }
        
        .project-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .project-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .project-item:hover {
            background-color: #f9fafb;
        }
        
        .project-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .project-info {
            flex: 1;
        }
        
        .project-name {
            font-weight: 500;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .project-date {
            font-size: 14px;
            color: #6b7280;
        }
        
        .project-actions {
            display: flex;
            gap: 8px;
        }
        
        .flash-message {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid;
        }
        
        .flash-success {
            background-color: #f0fdf4;
            border-color: #bbf7d0;
            color: #166534;
        }
        
        .flash-error {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }
    </style>
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