<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいプロジェクト - プロジェクト管理</title>
    <link rel="stylesheet" href="<?php echo Uri::base(); ?>assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <div class="container">
        <!-- ヘッダー -->
        <div class="header">
            <h1 class="title">新しいプロジェクト</h1>
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

        <!-- プロジェクト作成フォーム -->
        <div class="card card-center">
            <div class="card-header">
                <a href="<?php echo Uri::create('project'); ?>" class="back-btn">← 戻る</a>
                <h2 class="card-title">新しいプロジェクト</h2>
            </div>
            
            <form method="post" action="<?php echo Uri::create('project/create'); ?>">
                <?php if (isset($csrf_token)): ?>
                    <?php echo Form::hidden(Config::get('security.csrf_token_key', 'fuel_csrf_token'), $csrf_token); ?>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name" class="form-label">プロジェクト名</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="プロジェクト名を入力してください" required>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">作成</button>
                    <a href="<?php echo Uri::create('project'); ?>" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="<?php echo Uri::base(); ?>assets/js/common.js"></script>
</body>
</html>