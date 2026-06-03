<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoltWise</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f0f4ff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: white;
            border-radius: 24px;
            padding: 56px 48px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 8px 40px rgba(59, 91, 219, 0.12);
        }

        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 36px;
        }
        .logo-icon {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #3B5BDB, #6b8ef5);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: #1a1d2e;
        }
        .logo-text span { color: #3B5BDB; }

        h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #1a1d2e;
            line-height: 1.25;
            margin-bottom: 12px;
        }
        h1 em {
            font-style: normal;
            color: #3B5BDB;
        }

        p {
            font-size: 0.95rem;
            color: #6b7280;
            line-height: 1.65;
            margin-bottom: 36px;
        }

        .buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        a.btn {
            display: block;
            padding: 14px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #3B5BDB;
            color: white;
            box-shadow: 0 4px 16px rgba(59,91,219,0.3);
        }
        .btn-primary:hover {
            background: #2f4ac7;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,91,219,0.4);
        }
        .btn-outline {
            border: 1.5px solid #e2e8f0;
            color: #1a1d2e;
        }
        .btn-outline:hover {
            border-color: #3B5BDB;
            color: #3B5BDB;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            font-size: 0.8rem;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 40px;
            padding-top: 32px;
            border-top: 1px solid #f0f3ff;
        }
        .feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        .feature span {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94a3b8;
            text-align: center;
            line-height: 1.3;
        }

        footer {
            margin-top: 24px;
            font-size: 0.78rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">
            <div class="logo-icon">⚡</div>
            <div class="logo-text">Volt<span>Wise</span></div>
        </div>

        <h1>Monitor your energy,<br><em>save more money</em></h1>
        <p>Track your household electricity consumption, calculate costs, and stay within your monthly budget — all in one place.</p>

        <div class="buttons">
            <a href="/register" class="btn btn-primary">Create free account</a>
            <div class="divider">or</div>
            <a href="/login" class="btn btn-outline">Sign in to your account</a>
        </div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon" style="background:#EEF2FF">📊</div>
                <span>Track<br>Usage</span>
            </div>
            <div class="feature">
                <div class="feature-icon" style="background:#fff8ee">💰</div>
                <span>Calculate<br>Costs</span>
            </div>
            <div class="feature">
                <div class="feature-icon" style="background:#f0fff4">🎯</div>
                <span>Set<br>Budget</span>
            </div>
            <div class="feature">
                <div class="feature-icon" style="background:#f3f0ff">📅</div>
                <span>View<br>History</span>
            </div>
        </div>
    </div>

    <footer>© 2025 VoltWise · Group B</footer>

</body>
</html>