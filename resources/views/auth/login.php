<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Radiology QMS</title>
    <link rel="stylesheet" href="/css/receptionist.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            right: -20px;
            bottom: -20px;
            background-image: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.4)), url('/images/TGMCI LOGO.jpg');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            z-index: -1;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 40px 40px 50px;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 380px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .login-card h1 {
            font-size: 24px;
            color: var(--text-main);
            margin-bottom: 8px;
        }
        .login-card p {
            color: var(--text-light);
            margin-bottom: 24px;
            font-size: 14px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-main);
            font-size: 14px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s;
        }
        .form-group input:focus {
            border-color: var(--green);
        }
        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: var(--green);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-login:hover {
            background-color: #2b7a4c;
        }
        .error-msg {
            color: #dc2626;
            background: #fee2e2;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .logo-wrap {
            margin-bottom: 20px;
        }
        .logo-wrap img {
            height: 60px;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-wrap">
            <img src="/images/logo.png" alt="Tagum Global Medical Center Logo">
        </div>
        <h1>Staff Login</h1>
        <p>Radiology Queue Management System</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" id="name" name="name" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>
    </div>

</body>
</html>
