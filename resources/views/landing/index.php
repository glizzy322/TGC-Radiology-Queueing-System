<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radiology Queue System</title>
    <style>
        :root {
            color-scheme: light;
            font-family: Inter, Arial, sans-serif;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, #f4efe8 0%, #eef5ef 100%);
            color: #13261c;
        }

        .panel {
            width: min(720px, calc(100% - 32px));
            padding: 40px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
            text-align: center;
        }

        .eyebrow {
            margin: 0 0 8px;
            font-size: 12px;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 800;
            color: #8b1a2b;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 36px;
            line-height: 1.1;
        }

        p {
            margin: 0 0 24px;
            font-size: 16px;
            line-height: 1.6;
            color: #4f594f;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 14px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 13px 20px;
            border-radius: 999px;
            text-decoration: none;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        .btn.primary {
            background: #8b1a2b;
            color: #fff;
            box-shadow: 0 10px 20px rgba(139, 26, 43, 0.18);
        }

        .btn.secondary {
            background: #e9f2ec;
            color: #145a2e;
        }
    </style>
</head>
<body>
    <main class="panel">
        <p class="eyebrow">Radiology Queue Management System</p>
        <h1>Choose the view you need</h1>
        <p>Open the receptionist or admin panel to manage queues, or open the public display to show current status to patients.</p>
        <div class="actions">
            <a class="btn primary" href="/receptionist.php">Receptionist / Admin Page</a>
            <a class="btn secondary" href="/public-display.php" target="_blank" rel="noopener noreferrer">Open Public Display</a>
        </div>
    </main>
</body>
</html>
