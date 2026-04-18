<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta-desc', 'VoltWise Energy – Monitor your electricity')">
    <title>@yield('title', 'VoltWise') – VoltWise Energy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           RESET & THEME VARIABLES
        ============================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root, [data-theme="light"] {
            --blue-600: #4A7CF6;
            --blue-700: #3563e9;
            --blue-100: #dbeafe;

            --bg-right:         #ffffff;
            --bg-input:         #f8fafc;
            --bg-input-focus:   #ffffff;
            --border:           #e2e8f0;
            --border-hover:     #cbd5e1;
            --segment-empty:    #e2e8f0;

            --text-primary:     #0f172a;
            --text-secondary:   #334155;
            --text-muted:       #64748b;
            --text-placeholder: #94a3b8;

            --error:            #ef4444;
            --error-bg:         #fef2f2;
            --error-border:     #fecaca;
            --success-bg:       #f0fdf4;
            --success-border:   #bbf7d0;
            --success-color:    #065f46;

            --divider-color:    #e2e8f0;
            --divider-text:     #94a3b8;
            --section-line:     #e2e8f0;

            --radius: 8px;
        }

        [data-theme="dark"] {
            --blue-600: #4A7CF6;
            --blue-700: #3563e9;
            --blue-100: rgba(74,124,246,0.15);

            --bg-right:         #111827;
            --bg-input:         #1a2235;
            --bg-input-focus:   #1e2840;
            --border:           rgba(255,255,255,0.08);
            --border-hover:     rgba(255,255,255,0.15);
            --segment-empty:    rgba(255,255,255,0.1);

            --text-primary:     #f1f5f9;
            --text-secondary:   #cbd5e1;
            --text-muted:       #94a3b8;
            --text-placeholder: #64748b;

            --error:            #f87171;
            --error-bg:         rgba(239,68,68,0.1);
            --error-border:     rgba(239,68,68,0.3);
            --success-bg:       rgba(16,185,129,0.1);
            --success-border:   rgba(16,185,129,0.25);
            --success-color:    #6ee7b7;

            --divider-color:    rgba(255,255,255,0.08);
            --divider-text:     #64748b;
            --section-line:     rgba(255,255,255,0.08);

            --radius: 8px;
        }

        /* =============================================
           BASE LAYOUT
        ============================================= */
        html {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: 16px;
            -webkit-font-smoothing: antialiased;
        }

        body {
            background: var(--bg-right);
            color: var(--text-primary);
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            transition: background 0.25s, color 0.25s;
        }

        /* =============================================
           LEFT PANEL
        ============================================= */
        .panel-left {
            background: linear-gradient(160deg, #4A7CF6 0%, #3563e9 50%, #1d4ed8 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 3.5rem 4rem;
            position: relative;
            overflow: hidden;
        }

        /* ---- Floating ball keyframes ---- */
        @keyframes float1 {
            0%   { transform: translate(0,0) scale(1); }
            20%  { transform: translate(18px,-25px) scale(1.05); }
            45%  { transform: translate(-12px,30px) scale(0.95); }
            65%  { transform: translate(28px,10px) scale(1.08); }
            80%  { transform: translate(-8px,-18px) scale(0.97); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float2 {
            0%   { transform: translate(0,0) scale(1); }
            25%  { transform: translate(-22px,15px) scale(1.06); }
            50%  { transform: translate(15px,-28px) scale(0.93); }
            75%  { transform: translate(10px,20px) scale(1.04); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float3 {
            0%   { transform: translate(0,0) scale(1); }
            30%  { transform: translate(20px,22px) scale(1.1); }
            60%  { transform: translate(-18px,-15px) scale(0.9); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float4 {
            0%   { transform: translate(0,0) scale(1); }
            35%  { transform: translate(-25px,18px) scale(0.92); }
            70%  { transform: translate(14px,-22px) scale(1.07); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float5 {
            0%   { transform: translate(0,0) scale(1); }
            20%  { transform: translate(10px,-15px) scale(1.03); }
            50%  { transform: translate(-20px,12px) scale(0.96); }
            80%  { transform: translate(16px,20px) scale(1.05); }
            100% { transform: translate(0,0) scale(1); }
        }
        @keyframes float6 {
            0%   { transform: translate(0,0) scale(1); }
            40%  { transform: translate(22px,-30px) scale(1.08); }
            75%  { transform: translate(-10px,18px) scale(0.94); }
            100% { transform: translate(0,0) scale(1); }
        }

        /* Static balls */
        .panel-left::before {
            content: ''; position: absolute;
            top: -80px; right: -80px;
            width: 320px; height: 320px; border-radius: 50%;
            background: rgba(255,255,255,0.07);
            animation: float3 18s ease-in-out infinite;
        }
        .panel-left::after {
            content: ''; position: absolute;
            bottom: -60px; left: -60px;
            width: 240px; height: 240px; border-radius: 50%;
            background: rgba(255,255,255,0.05);
            animation: float4 22s ease-in-out infinite;
        }
        .ball {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.06);
            will-change: transform; pointer-events: none;
        }
        .ball-1 { width:160px; height:160px; top:40%; right:10%; animation: float1 14s ease-in-out infinite; }
        .ball-2 { width:90px;  height:90px;  bottom:28%; left:8%; animation: float2 11s ease-in-out infinite 1.5s; }

        /* Brand */
        .brand-logo { display:flex; align-items:center; gap:0.625rem; margin-bottom:3rem; text-decoration:none; }
        .brand-icon {
            width:42px; height:42px; background:rgba(255,255,255,0.2);
            border-radius:10px; display:flex; align-items:center; justify-content:center;
            font-size:1.25rem; backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,0.25);
        }
        .brand-name { font-size:1.375rem; font-weight:700; color:white; letter-spacing:-0.02em; }
        .brand-name span { color:rgba(255,255,255,0.7); }

        /* Panel tagline slot */
        .panel-tagline { position:relative; z-index:1; }
        .panel-tagline h2 {
            font-size:2rem; font-weight:700; color:white;
            line-height:1.25; letter-spacing:-0.03em; margin-bottom:1rem;
        }
        .panel-tagline p { font-size:0.9375rem; color:rgba(255,255,255,0.75); line-height:1.6; max-width:340px; }

        /* Feature list (login) */
        .feature-list { margin-top:2.5rem; display:flex; flex-direction:column; gap:1rem; }
        .feature-item { display:flex; align-items:center; gap:0.75rem; color:rgba(255,255,255,0.85); font-size:0.875rem; }
        .feature-item-icon {
            width:32px; height:32px; background:rgba(255,255,255,0.15);
            border-radius:8px; display:flex; align-items:center; justify-content:center;
            font-size:0.875rem; flex-shrink:0;
        }

        /* Steps list (register) */
        .steps-list { margin-top:2.5rem; display:flex; flex-direction:column; gap:0; }
        .step-item { display:flex; align-items:flex-start; gap:0.875rem; position:relative; padding-bottom:1.5rem; }
        .step-item:last-child { padding-bottom:0; }
        .step-item:not(:last-child)::after {
            content:''; position:absolute; left:15px; top:32px; bottom:0;
            width:1px; background:rgba(255,255,255,0.2);
        }
        .step-number {
            width:32px; height:32px; background:rgba(255,255,255,0.15);
            border:1.5px solid rgba(255,255,255,0.3); border-radius:50%;
            display:flex; align-items:center; justify-content:center;
            font-size:0.75rem; font-weight:700; color:white; flex-shrink:0;
        }
        .step-content { padding-top:0.375rem; }
        .step-content strong { display:block; color:white; font-size:0.875rem; font-weight:600; margin-bottom:0.125rem; }
        .step-content span { color:rgba(255,255,255,0.65); font-size:0.8125rem; }

        /* =============================================
           RIGHT PANEL
        ============================================= */
        .panel-right {
            background: var(--bg-right);
            display:flex; flex-direction:column;
            justify-content:center; align-items:center;
            padding:3rem 2rem; overflow-y:auto;
            transition: background 0.25s;
        }
        .form-container {
            width:100%; max-width:400px;
            animation: fadeUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* Form header */
        .form-header { margin-bottom:1.75rem; }
        .form-header h1 {
            font-size:1.625rem; font-weight:700; color:var(--text-primary);
            letter-spacing:-0.025em; margin-bottom:0.375rem; transition:color 0.25s;
        }
        .form-header p { font-size:0.9rem; color:var(--text-muted); transition:color 0.25s; }

        /* Alerts */
        .alert {
            display:flex; align-items:flex-start; gap:0.625rem;
            padding:0.875rem 1rem; border-radius:var(--radius);
            font-size:0.875rem; margin-bottom:1.25rem; border:1px solid;
        }
        .alert-success { background:var(--success-bg); border-color:var(--success-border); color:var(--success-color); }
        .alert-error   { background:var(--error-bg);   border-color:var(--error-border);   color:var(--error); }
        .alert-icon    { flex-shrink:0; margin-top:1px; }

        /* Form groups */
        .form-group { margin-bottom:1.125rem; }
        .form-label {
            display:block; font-size:0.8125rem; font-weight:600;
            color:var(--text-secondary); margin-bottom:0.5rem;
            letter-spacing:0.01em; transition:color 0.25s;
        }
        .input-wrapper { position:relative; }
        .input-icon {
            position:absolute; left:0.875rem; top:50%; transform:translateY(-50%);
            color:var(--text-muted); display:flex; align-items:center;
            pointer-events:none; transition:color 0.2s;
        }
        .input-icon svg { width:16px; height:16px; }
        .input-wrapper:focus-within .input-icon { color:var(--blue-600); }

        .form-input {
            width:100%; background:var(--bg-input); border:1.5px solid var(--border);
            border-radius:var(--radius); padding:0.75rem 0.875rem 0.75rem 2.625rem;
            font-size:0.9375rem; font-family:'Inter',sans-serif; color:var(--text-primary);
            outline:none; transition:border-color 0.2s, box-shadow 0.2s, background 0.2s, color 0.25s;
        }
        .form-input::placeholder { color:var(--text-placeholder); }
        .form-input:hover:not(:focus) { border-color:var(--border-hover); }
        .form-input:focus {
            border-color:var(--blue-600);
            box-shadow:0 0 0 3px rgba(74,124,246,0.15);
            background:var(--bg-input-focus);
        }
        .form-input.is-invalid { border-color:var(--error); box-shadow:0 0 0 3px rgba(239,68,68,0.1); }
        .form-input.has-toggle { padding-right:2.75rem; }

        .field-error {
            display:flex; align-items:center; gap:0.25rem;
            margin-top:0.375rem; font-size:0.8125rem; color:var(--error);
        }

        /* Password toggle */
        .toggle-password {
            position:absolute; right:0.875rem; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer;
            color:var(--text-muted); padding:0.25rem;
            display:flex; align-items:center; transition:color 0.2s; border-radius:4px;
        }
        .toggle-password:hover { color:var(--blue-600); }
        .toggle-password svg { width:16px; height:16px; }

        /* Remember me row */
        .form-row { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; }
        .checkbox-label {
            display:flex; align-items:center; gap:0.5rem; cursor:pointer;
            font-size:0.875rem; color:var(--text-muted); user-select:none; transition:color 0.25s;
        }
        .checkbox-label input[type="checkbox"] { width:15px; height:15px; accent-color:var(--blue-600); cursor:pointer; }
        .form-link { font-size:0.875rem; color:var(--blue-600); text-decoration:none; font-weight:500; transition:color 0.2s; }
        .form-link:hover { color:var(--blue-700); text-decoration:underline; }

        /* Section title (register) */
        .form-section-title {
            font-size:0.75rem; font-weight:600; text-transform:uppercase;
            letter-spacing:0.08em; color:var(--text-muted);
            margin:0.25rem 0 0.875rem; display:flex; align-items:center;
            gap:0.75rem; transition:color 0.25s;
        }
        .form-section-title::after { content:''; flex:1; height:1px; background:var(--section-line); transition:background 0.25s; }

        /* Terms (register) */
        .form-group-terms { margin-bottom:1.25rem; }
        .checkbox-label-terms {
            display:flex; align-items:flex-start; gap:0.5rem; cursor:pointer;
            font-size:0.8125rem; color:var(--text-muted); line-height:1.5; user-select:none; transition:color 0.25s;
        }
        .checkbox-label-terms input { width:14px; height:14px; accent-color:var(--blue-600); cursor:pointer; flex-shrink:0; margin-top:2px; }
        .checkbox-label-terms a { color:var(--blue-600); text-decoration:none; font-weight:500; }
        .checkbox-label-terms a:hover { text-decoration:underline; }

        /* Password strength (register) */
        .strength-meter { margin-top:0.5rem; display:none; }
        .strength-track { display:flex; gap:3px; margin-bottom:0.3125rem; }
        .strength-segment { flex:1; height:3px; border-radius:99px; background:var(--segment-empty); transition:background 0.3s; }
        .seg-weak   { background:#ef4444; }
        .seg-fair   { background:#f59e0b; }
        .seg-good   { background:#5b8af5; }
        .seg-strong { background:#10b981; }
        .strength-text { font-size:0.75rem; color:var(--text-muted); transition:color 0.3s; }

        /* Button */
        .btn-primary {
            width:100%; padding:0.8125rem 1rem;
            background:var(--blue-600); border:none; border-radius:var(--radius);
            color:white; font-size:0.9375rem; font-weight:600;
            font-family:'Inter',sans-serif; cursor:pointer;
            transition:background 0.2s, transform 0.15s, box-shadow 0.2s;
            box-shadow:0 2px 8px rgba(74,124,246,0.3);
            display:flex; align-items:center; justify-content:center; gap:0.5rem;
            margin-bottom:1.5rem;
        }
        .btn-primary:hover { background:var(--blue-700); transform:translateY(-1px); box-shadow:0 4px 16px rgba(74,124,246,0.35); }
        .btn-primary:active { transform:translateY(0); }
        .btn-primary:disabled { opacity:0.6; cursor:not-allowed; transform:none; }
        .btn-spinner {
            width:16px; height:16px;
            border:2px solid rgba(255,255,255,0.3); border-top-color:white;
            border-radius:50%; animation:spin 0.7s linear infinite; display:none;
        }
        @keyframes spin { to { transform:rotate(360deg); } }

        /* Divider & footer */
        .divider {
            display:flex; align-items:center; gap:0.875rem;
            margin-bottom:1.5rem; color:var(--divider-text);
            font-size:0.8125rem; transition:color 0.25s;
        }
        .divider::before, .divider::after {
            content:''; flex:1; height:1px;
            background:var(--divider-color); transition:background 0.25s;
        }
        .card-footer { text-align:center; font-size:0.875rem; color:var(--text-muted); transition:color 0.25s; }
        .card-footer a { color:var(--blue-600); text-decoration:none; font-weight:600; transition:color 0.2s; }
        .card-footer a:hover { color:var(--blue-700); text-decoration:underline; }

        /* Responsive */
        @media (max-width: 768px) {
            body { grid-template-columns:1fr; }
            .panel-left { display:none; }
            .panel-right { padding:2rem 1.25rem; min-height:100vh; }
        }
    </style>
</head>
<body>

{{-- Apply theme without flash --}}
<script>
    (function(){
        const t = localStorage.getItem('voltwise-theme') || 'light';
        document.documentElement.setAttribute('data-theme', t);
    })();
</script>

{{-- LEFT PANEL --}}
<div class="panel-left" id="panel-left" aria-hidden="true">
    <div class="ball ball-1"></div>
    <div class="ball ball-2"></div>

    <a href="{{ url('/') }}" class="brand-logo">
        <div class="brand-icon">⚡</div>
        <span class="brand-name">Volt<span>Wise</span></span>
    </a>

    <div class="panel-tagline">
        @yield('panel-content')
    </div>
</div>

{{-- RIGHT PANEL --}}
<div class="panel-right">
    <div class="form-container">
        @yield('form-content')
    </div>
</div>

<script>
    // Spawn extra random floating balls
    (function(){
        const panel = document.getElementById('panel-left');
        const anims = ['float1','float2','float3','float4','float5','float6'];
        for (let i = 0; i < 8; i++) {
            const b = document.createElement('div');
            const size = Math.random() * 100 + 30;
            b.style.cssText = [
                'position:absolute','border-radius:50%','pointer-events:none','will-change:transform',
                `width:${size}px`, `height:${size}px`,
                `top:${Math.random()*90}%`, `left:${Math.random()*80}%`,
                `background:rgba(255,255,255,${(Math.random()*0.06+0.03).toFixed(3)})`,
                `animation:${anims[~~(Math.random()*6)]} ${(Math.random()*15+9).toFixed(1)}s ease-in-out ${(Math.random()*8).toFixed(1)}s infinite ${Math.random()>.5?'alternate':'normal'}`,
            ].join(';');
            panel.appendChild(b);
        }
    })();

    @yield('page-scripts')
</script>
</body>
</html>
