<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?> - プロジェクト管理</title>
    <link rel="stylesheet" href="/assets/css/style.css">
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
                <h2 class="section-title">
                    <a href="<?php echo Uri::create('project'); ?>" class="back-link">← 戻る</a>
                    タスク一覧
                </h2>
                <a href="<?php echo Uri::create('task/create/'.$project->id); ?>" class="btn btn-primary">
                    + 新しいタスク
                </a>
            </div>

            <div class="filter-section">
                <label class="filter-option">
                    <input type="checkbox" class="filter-checkbox" data-bind="checked: showCompleted">
                    完了済みタスクを表示
                </label>
            </div>

            <!-- 空の状態 -->
            <div class="empty-state" data-bind="visible: filteredTasks().length === 0">
                <p class="empty-message">タスクがありません</p>
                <a href="<?php echo Uri::create('task/create/'.$project->id); ?>" class="btn btn-primary">
                    最初のタスクを作成
                </a>
            </div>

            <!-- タスク一覧（index.phpのproject-listと同じ構造） -->
            <div data-bind="visible: filteredTasks().length > 0">
                <div class="project-list" data-bind="foreach: filteredTasks">
                    <div class="project-item">
                        <div class="project-content">
                            <div class="project-info">
                                <div class="task-checkbox-wrapper">
                                    <input type="checkbox" class="task-status-checkbox" data-bind="checked: status, click: $parent.toggleStatus">
                                </div>
                                <div class="task-details">
<h3 class="project-name" data-bind="text: name, css: { 'task-completed': status() == 1 }, click: $parent.toggleStatus"></h3>
                                    <p class="project-date" data-bind="text: due_date, visible: due_date"></p>
                                </div>
                            </div>
                            <div class="project-actions">
                                <button class="btn btn-edit" data-bind="click: $parent.editTask">
                                    編集
                                </button>
                                <button class="btn btn-danger" data-bind="click: $parent.deleteTask">
                                    削除
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Knockout.js -->
    <script src="/assets/js/knockout-min.js"></script>
    
    <script>
    function TaskViewModel() {
        var self = this;
        self.isToggling = ko.observable(false);
        
        // PHPからJS用データを生成（index.phpと同じ方法）
        self.tasks = ko.observableArray([
            <?php
            if (!empty($tasks)) {
                $js_tasks = [];
                foreach ($tasks as $task) {
                    $js_tasks[] = sprintf(
                        "{id: %d, name: '%s', due_date: %s, status: ko.observable(%d)}",
                        $task->id,
                        addslashes($task->name),
                        $task->due_date !== null ? "'" . addslashes($task->due_date) . "'" : 'null',
                        $task->status
                    );
                }
                echo implode(",\n", $js_tasks);
            }
            ?>
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
            if (self.isToggling()) return;
            self.isToggling(true);

            var prevStatus = task.status();
            var newStatus = prevStatus == 1 ? 0 : 1;
            task.status(newStatus); // UI即反映

            fetch('<?php echo Uri::create('task/toggle_status'); ?>/' + task.id, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus })
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (!data.success) {
                    task.status(prevStatus); // サーバーエラー時のみ元に戻す
                } else {
                    task.status(data.status); // サーバーの値で上書き
                }
                self.isToggling(false);
            })
            .catch(function(error) {
                task.status(prevStatus); // 通信エラー時も元に戻す
                console.error('Error:', error);
                self.isToggling(false);
            });
            return true;
        };

        // タスク編集
        self.editTask = function(task) {
            window.location.href = '<?php echo Uri::create('task/edit'); ?>/' + task.id;
        };

        // タスク削除
        self.deleteTask = function(task) {
            if (confirm('本当に削除しますか？')) {
                window.location.href = '<?php echo Uri::create('task/delete'); ?>/' + task.id;
            }
        };
    }

    var taskViewModel = new TaskViewModel();
    ko.applyBindings({ taskViewModel: taskViewModel });

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