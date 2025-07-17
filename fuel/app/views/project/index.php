<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロジェクト一覧 - プロジェクト管理</title>
    <style>
        /* 省略可能：CSSはcreate.phpと同じですので、必要ならコピペしてください */
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
        .project-list {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }
        .project-item {
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .project-item:last-child {
            border-bottom: none;
        }
        .project-name {
            font-size: 16px;
            color: #111827;
        }
        .project-actions a {
            font-size: 14px;
            margin-left: 8px;
            color: #2563eb;
            text-decoration: underline;
        }
        .btn {
            display: inline-block;
            margin-top: 16px;
            padding: 8px 16px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1 class="title">プロジェクト一覧</h1>
            <div class="user-info">
                <span class="user-name">こんにちは、<?php echo htmlspecialchars($current_user, ENT_QUOTES, 'UTF-8'); ?>さん</span>
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

        <!-- プロジェクトリスト -->
        <div class="project-list">
            <?php if ($projects): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-item">
                        <span class="project-name"><?php echo Html::chars($project->name); ?></span>
                        <div class="project-actions">
                            <a href="<?php echo Uri::create("project/view/{$project->id}"); ?>">表示</a>
                            <a href="<?php echo Uri::create("project/edit/{$project->id}"); ?>">編集</a>
                            <a href="<?php echo Uri::create("project/delete/{$project->id}"); ?>" onclick="return confirm('削除してもよろしいですか？');">削除</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>まだプロジェクトがありません。</p>
            <?php endif; ?>
        </div>

        <!-- 新規作成ボタン -->
        <a href="<?php echo Uri::create('project/create'); ?>" class="btn">＋ 新しいプロジェクトを作成</a>
    </div>

    <script>
        function logout() {
            if (confirm('ログアウトしますか？')) {
                window.location.href = '<?php echo Uri::create('auth/logout'); ?>';
            }
        }
    </script>
</body>
</html>
