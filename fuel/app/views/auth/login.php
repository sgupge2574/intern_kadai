<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - プロジェクト管理</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            max-width: 400px;
            width: 100%;
            padding: 24px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 8px;
        }
        
        .subtitle {
            color: #6b7280;
            font-size: 16px;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }
        
        .card-header {
            text-align: center;
            margin-bottom: 24px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 500;
            color: #111827;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 4px;
        }
        
        .form-input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        
        .form-hint {
            font-size: 12px;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            border: none;
        }
        
        .btn-primary {
            background-color: #2563eb;
            color: white;
            width: 100%;
            padding: 10px;
            margin-top: 16px;
        }
        
        .btn-primary:hover {
            background-color: #1d4ed8;
        }
        
        .link {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }
        
        .link:hover {
            text-decoration: underline;
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 16px;
        }
        
        .demo-info {
            background-color: #f3f4f6;
            border-radius: 4px;
            padding: 12px;
            margin-top: 16px;
            font-size: 14px;
            color: #4b5563;
        }
        
        .demo-title {
            font-weight: 500;
            margin-bottom: 4px;
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
        <div class="header">
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
            <div class="card-header">
                <h2 class="card-title">ログイン</h2>
            </div>
            
            <form method="post" action="<?php echo Uri::create('auth/login'); ?>">
                <div class="form-group">
                    <label for="name" class="form-label">ユーザー名</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="ユーザー名を入力" required>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">パスワード</label>
                    <input type="password" id="password" name="password" class="form-input" placeholder="パスワードを入力" required>
                </div>
                
                <button type="submit" class="btn btn-primary">ログイン</button>
            </form>
            
            <div class="text-center mt-4">
                <a href="<?php echo Uri::create('auth/register'); ?>" class="link">アカウントをお持ちでない方はこちら</a>
            </div>
            
            <div class="demo-info">
                <p class="demo-title">デモ用アカウント:</p>
                <p>ユーザー名: demo</p>
                <p>パスワード: demo</p>
            </div>
        </div>
    </div>
</body>
</html>