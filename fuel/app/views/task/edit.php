<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク編集 - プロジェクト管理</title>
    <link rel="stylesheet" href="<?php echo Uri::base(); ?>assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1 class="title">タスク編集</h1>
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

        <!-- タスク編集フォーム -->
        <div class="card card-center">
            <div class="card-header">
                <a href="<?php echo Uri::create('project/view/'.$project->id); ?>" class="back-btn">← 戻る</a>
                <h2 class="card-title">タスクを編集</h2>
            </div>
            
            <div class="project-name"><?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?></div>
            
            <form method="post" action="<?php echo Uri::create('task/edit/'.$task->id); ?>">
                <?php if (isset($csrf_token)): ?>
                    <?php echo Form::hidden(Config::get('security.csrf_token_key', 'fuel_csrf_token'), $csrf_token); ?>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name" class="form-label">タスク名</label>
                    <input type="text" id="name" name="name" class="form-input" value="<?php echo htmlspecialchars($task->name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="タスク名を入力してください" required>
                </div>
                
                <div class="form-group">
                    <label for="due_date" class="form-label">期限</label>
                    <input type="date" id="due_date" name="due_date" class="form-input" value="<?php echo $task->due_date; ?>">
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">更新</button>
                    <a href="<?php echo Uri::create('project/view/'.$project->id); ?>" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo Uri::base(); ?>assets/js/common.js"></script>
</body>
</html>