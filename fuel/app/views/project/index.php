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
            <div data-bind="visible: projects().length > 0">
                <div class="project-list" data-bind="foreach: projects">
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
    </div>

    <!-- Knockout.js -->
    <script src="/assets/js/knockout-min.js"></script>
    

    <script>
    function ProjectViewModel() {
        var self = this;

        // PHPからJS用データを生成
        self.projects = ko.observableArray([
            <?php
            if (!empty($projects)) {
                $js_projects = [];
                foreach ($projects as $p) {
                    $js_projects[] = sprintf(
                        "{id: %d, name: '%s', created_at: '%s'}",
                        $p->id,
                        addslashes($p->name),
                        $p->created_at
                    );
                }
                echo implode(",\n", $js_projects);
            }
            ?>
        ]);

        self.viewProject = function(project) {
            window.location.href = '<?php echo Uri::create('project/view'); ?>/' + project.id;
        };
        self.editProject = function(project) {
            window.location.href = '<?php echo Uri::create('project/edit'); ?>/' + project.id;
        };
        self.deleteProject = function(project) {
            if (confirm('本当に削除しますか？')) {
                window.location.href = '<?php echo Uri::create('project/delete'); ?>/' + project.id;
            }
        };
    }

    var projectViewModel = new ProjectViewModel();
    ko.applyBindings({ projectViewModel: projectViewModel });

    function logout() {
        if (confirm('ログアウトしますか？')) {
            window.location.href = '<?php echo Uri::create('auth/logout'); ?>';
        }
    }
</script>

</body>
</html>