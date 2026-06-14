<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - VoltWise</title>
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 4rem;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            text-align: center;
            max-width: 600px;
        }
        h1 {
            color: #4A7CF6;
            margin-top: 0;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }
        .term {
            font-size: 2rem;
            font-weight: 700;
            padding: 2rem;
            background: #f1f5f9;
            border-radius: 12px;
            border: 2px dashed #cbd5e1;
            margin-bottom: 3rem;
        }
        a {
            color: #4A7CF6;
            text-decoration: none;
            font-weight: 600;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Terms of Service</h1>
        
        <div class="term">
            Be a human.
        </div>
        
        <p>
            <a href="{{ route('register') }}">← Back to Registration</a>
        </p>
    </div>
</body>
</html>
