<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しいタスク - プロジェクト管理</title>
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
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }
        
        .back-btn {
            display: flex;
            align-items: center;
            color: #2563eb;
            font-size: 16px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            margin-right: 8px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: 500;
            color: #111827;
        }
        
        .project-name {
            font-size: 16px;
            font-weight: 500;
            color: #111827;
            margin-top: 8px;
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
        
        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
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
            justify-content: center;
            flex: 1;
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
            <h1 class="title">新しいタスク</h1>
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

        <!-- タスク作成フォーム -->
        <div class="card">
            <div class="card-header">
                <a href="<?php echo Uri::create('project/view/'.$project->id); ?>" class="back-btn">
                    ← 新しいタスク
                </a>
            </div>
            
            <div class="project-name"><?php echo htmlspecialchars($project->name, ENT_QUOTES, 'UTF-8'); ?></div>
            
            <form method="post" action="<?php echo Uri::create('task/create/'.$project->id); ?>">
                <div class="form-group">
                    <label for="name" class="form-label">タスク名</label>
                    <input type="text" id="name" name="name" class="form-input" placeholder="タスク名を入力してください" required>
                </div>
                
                <div class="form-group">
                    <label for="due_date" class="form-label">期限</label>
                    <input type="date" id="due_date" name="due_date" class="form-input">
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">作成</button>
                    <a href="<?php echo Uri::create('project/view/'.$project->id); ?>" class="btn btn-secondary">キャンセル</a>
                </div>
            </form>
        </div>
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