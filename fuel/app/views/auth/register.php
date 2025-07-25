<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アカウント作成 - プロジェクト管理</title>
    <link rel="stylesheet" href="<?php echo Uri::base(); ?>assets/css/style.css?v=<?php echo time(); ?>">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1 class="title">タスク管理</h1>
            <p class="subtitle">効率的にプロジェクトとタスクを管理しましょう</p>
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

        <div class="card">
            <div class="card-header-center">
                <h2 class="card-title">アカウント作成</h2>
            </div>
            
            <form method="post" action="<?php echo Uri::create('auth/register'); ?>">
                <?php if (isset($csrf_token)): ?>
                    <?php echo Form::hidden(Config::get('security.csrf_token_key', 'fuel_csrf_token'), $csrf_token); ?>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="name" class="form-label">ユーザー名</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="ユニークなユーザー名を入力" required minlength="2">
                    <p class="form-hint">※ユーザー名は他の人と重複できません</p>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="パスワードを入力" required minlength="4">
                    <p class="form-hint">※4文字以上で入力してください</p>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">アカウント作成</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="<?php echo Uri::create('auth/login'); ?>" class="link">既にアカウントをお持ちの方はこちら</a>
            </div>
        </div>
    </div>
    
    <script src="<?php echo Uri::base(); ?>assets/js/common.js"></script>
</body>
</html>