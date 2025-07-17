<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?> - プロジェクト管理</title>
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
            display: flex;
            align-items: center;
        }
        
        .back-link {
            color: #2563eb;
            text-decoration: none;
            margin-right: 12px;
        }
        
        .back-link:hover {
            text-decoration: underline;
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
        
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        
        .task-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 16px;
        }
        
        .task-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .task-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        
        .task-checkbox {
            margin-right: 12px;
        }
        
        .task-details {
            flex: 1;
        }
        
        .task-name {
            font-weight: 500;
            color: #111827;
            margin-bottom: 4px;
        }
        
        .task-name.completed {
            text-decoration: line-through;
            color: #9ca3af;
        }
        
        .task-date {
            font-size: 14px;
            color: #6b7280;
        }
        
        .task-actions {
            display: flex;
            gap: 8px;
        }
        
        .filter-option {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }
        
        .filter-checkbox {
            margin-right: 8px;
        }
        
        .filter-label {
            font-size: 14px;
            color: #374151;
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
            <h1 class="title"><?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?></h1>
            <div class="user-info">
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

        <!-- タスク一覧セクション -->
        <div data-bind="with: taskViewModel">
            <div class="section-header">
                <div class="section-title">
                    <a href="<?php echo Uri::create('project'); ?>" class="back-link">← 戻る</a>
                    <?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <a href="<?php echo Uri::create('task/create/'.$project->id); ?>" class="btn btn-primary">
                    + 新しいタスク
                </a>
            </div>

            <div class="filter-option">
                <input type="checkbox" id="show-completed" class="filter-checkbox" data-bind="checked: showCompleted">
                <label for="show-completed" class="filter-label">完了済みタスクを表示</label>
            </div>

            <!-- 空の状態 -->
            <div class="empty-state" data-bind="visible: filteredTasks().length === 0">
                <p class="empty-message">タスクがありません</p>
                <a href="<?php echo Uri::create('task/create/'.$project->id); ?>" class="btn btn-primary">
                    最初のタスクを作成
                </a>
            </div>

            <!-- タスク一覧 -->
            <div class="task-list" data-bind="visible: filteredTasks().length > 0, foreach: filteredTasks">
                <div class="task-item">
                    <div class="task-content">
                        <div class="task-info">
                            <input type="checkbox" class="task-checkbox" data-bind="checked: status, click: $parent.toggleStatus">
                            <div class="task-details">
                                <div class="task-name" data-bind="text: name, css: { completed: status() == 1 }"></div>
                                <div class="task-date" data-bind="text: due_date, visible: due_date"></div>
                            </div>
                        </div>
                        <div class="task-actions">
                            <a data-bind="attr: { href: '<?php echo Uri::create('task/edit'); ?>/' + id }" class="btn btn-edit">
                                編集
                            </a>
                            <button class="btn btn-danger" data-bind="click: $parent.deleteTask">
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
        // タスクViewModel
        function TaskViewModel() {
            var self = this;
            
            // タスクデータ
            self.tasks = ko.observableArray([
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $index => $task): ?>
                        {
                            id: <?php echo $task->id; ?>,
                            name: '<?php echo addslashes($task->name); ?>',
                            due_date: '<?php echo $task->due_date; ?>',
                            status: ko.observable(<?php echo $task->status; ?>)
                        }<?php echo ($index < count($tasks) - 1) ? ',' : ''; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            ]);
            
            // 完了済みタスク表示設定
            self.showCompleted = ko.observable(true);
            
            // Cookie保存
            self.showCompleted.subscribe(function(value) {
                setCookie('showCompleted', value, 30);
            });
            
            // Cookie読み込み
            var savedShowCompleted = getCookie('showCompleted');
            if (savedShowCompleted !== null) {
                self.showCompleted(savedShowCompleted === 'true');
            }
            
            // フィルタリングされたタスク
            self.filteredTasks = ko.computed(function() {
                return self.tasks().filter(function(task) {
                    return self.showCompleted() || task.status() == 0;
                });
            });
            
            // タスク状態切り替え
            self.toggleStatus = function(task) {
                fetch('<?php echo Uri::create('task/toggle_status'); ?>/' + task.id, {
                    method: 'POST'
                })
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (data.success) {
                        task.status(data.status);
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error);
                });
                
                return true; // イベントバブリングを許可
            };
            
            // タスク削除
            self.deleteTask = function(task) {
                if (confirm('本当に削除しますか？')) {
                    window.location.href = '<?php echo Uri::create('task/delete'); ?>/' + task.id;
                }
            };
        }

        // ViewModelをバインド
        var taskViewModel = new TaskViewModel();
        ko.applyBindings({ taskViewModel: taskViewModel });

        // ログアウト機能
        function logout() {
            if (confirm('ログアウトしますか？')) {
                window.location.href = '<?php echo Uri::create('auth/logout'); ?>';
            }
        }
        
        // Cookie関連のユーティリティ関数
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }
    </script>
</body>
</html>