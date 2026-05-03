<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta-desc', 'VoltWise Energy – Monitor your electricity')">
    <title>@yield('title', 'Dashboard') – VoltWise Energy</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           #RESET & THEME VARIABLES
        ============================================= */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root, [data-theme="light"] {
            --bg-base:          #f8fafc;
            --bg-sidebar:       #ffffff;
            --bg-topbar:        #ffffff;
            --bg-card:          #ffffff;
            --bg-tip:            #f8fafc;
            --bg-feature:       #ffffff;
            --bg-banner:        linear-gradient(135deg,#eef3ff 0%,#e8f0fe 50%,#f0f7ff 100%);
            --bg-banner-border: #bfdbfe;

            --border:           #e2e8f0;

            --text-primary:     #0f172a;
            --text-secondary:   #334155;
            --text-muted:       #64748b;
            --text-faint:       #94a3b8;

            --icon-btn-bg:      #f1f5f9;
            --icon-btn-hover:   #e2e8f0;

            --shadow-card:      0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);

            --blue-600:  #4A7CF6;
            --blue-700:  #3563e9;
            --blue-100:  #dbeafe;
            --blue-200:  #bfdbfe;

            --nav-active-bg:    #dbeafe;
            --nav-active-color: #1d4ed8;
            --nav-hover-bg:     #f1f5f9;

            --icon-blue-bg:   #dbeafe;   --icon-blue-fg:   #4A7CF6;
            --icon-green-bg:  #d1fae5;   --icon-green-fg:  #10b981;
            --icon-orange-bg: #ffedd5;   --icon-orange-fg: #f97316;
            --icon-purple-bg: #ede9fe;   --icon-purple-fg: #8b5cf6;

            --tip-yellow-bg: #fef9c3; --tip-yellow-fg: #ca8a04;
            --tip-blue-bg:   #dbeafe; --tip-blue-fg:   #4A7CF6;
            --tip-orange-bg: #ffedd5; --tip-orange-fg: #f97316;

            --wf-icon-bg:  #dbeafe; --wf-icon-fg:  #4A7CF6;
            --wf-green-bg: #d1fae5; --wf-green-fg: #059669;

            --logout-hover-bg:    #fef2f2;
            --logout-hover-color: #ef4444;
            --flash-bg: #f0fdf4; --flash-border: #bbf7d0; --flash-color: #166534;

            /* Avatar & Profile */
            --avatar-menu-bg: #ffffff;
            --avatar-menu-border: #e2e8f0;
            --avatar-menu-color: #0f172a;

            /* Budget Tracker */
            --budget-track-bg:      #e2e8f0;
            --budget-bar-safe:      #10b981;
            --budget-bar-warn:      #f59e0b;
            --budget-bar-danger:    #ef4444;
            --budget-label-safe:    #065f46;
            --budget-label-warn:    #78350f;
            --budget-label-danger:  #7f1d1d;
            --budget-badge-safe-bg: #d1fae5;
            --budget-badge-warn-bg: #fef3c7;
            --budget-badge-danger-bg: #fee2e2;
            --modal-overlay:        rgba(15,23,42,0.45);
            --modal-bg:              #ffffff;
        }

        [data-theme="dark"] {
            --bg-base:          #0d1117;
            --bg-sidebar:       #111827;
            --bg-topbar:        #111827;
            --bg-card:          #1a2235;
            --bg-tip:            #1e2840;
            --bg-feature:       #1e2840;
            --bg-banner:        linear-gradient(135deg,#0f1d3a 0%,#0e1a36 60%,#0f1f3d 100%);
            --bg-banner-border: #1e3a5f;

            --border:           rgba(255,255,255,0.07);

            --text-primary:     #f1f5f9;
            --text-secondary:   #cbd5e1;
            --text-muted:       #94a3b8;
            --text-faint:       #64748b;

            --icon-btn-bg:      #1e2840;
            --icon-btn-hover:   #273350;

            --shadow-card:      0 1px 3px rgba(0,0,0,0.3);

            --blue-600:  #4A7CF6;
            --blue-700:  #3563e9;
            --blue-100:  rgba(74,124,246,0.15);
            --blue-200:  rgba(74,124,246,0.25);

            --nav-active-bg:    rgba(74,124,246,0.2);
            --nav-active-color: #93b4fb;
            --nav-hover-bg:     rgba(255,255,255,0.05);

            --icon-blue-bg:   rgba(74,124,246,0.2);  --icon-blue-fg:   #93b4fb;
            --icon-green-bg:  rgba(16,185,129,0.15); --icon-green-fg:  #6ee7b7;
            --icon-orange-bg: rgba(249,115,22,0.15); --icon-orange-fg: #fdba74;
            --icon-purple-bg: rgba(139,92,246,0.15); --icon-purple-fg: #c4b5fd;

            --tip-yellow-bg: rgba(234,179,8,0.15);  --tip-yellow-fg: #fde047;
            --tip-blue-bg:   rgba(74,124,246,0.2);  --tip-blue-fg:   #93b4fb;
            --tip-orange-bg: rgba(249,115,22,0.15); --tip-orange-fg: #fdba74;

            --wf-icon-bg:  rgba(74,124,246,0.2);  --wf-icon-fg:  #93b4fb;
            --wf-green-bg: rgba(16,185,129,0.15); --wf-green-fg: #6ee7b7;

            --logout-hover-bg:    rgba(239,68,68,0.1);
            --logout-hover-color: #f87171;
            --flash-bg: rgba(16,185,129,0.1); --flash-border: rgba(16,185,129,0.2); --flash-color: #6ee7b7;

            /* Avatar & Profile */
            --avatar-menu-bg: #1a2235;
            --avatar-menu-border: rgba(255,255,255,0.07);
            --avatar-menu-color: #f1f5f9;

            /* Budget Tracker */
            --budget-track-bg:      rgba(255,255,255,0.08);
            --budget-bar-safe:      #10b981;
            --budget-bar-warn:      #f59e0b;
            --budget-bar-danger:    #ef4444;
            --budget-label-safe:    #6ee7b7;
            --budget-label-warn:    #fde047;
            --budget-label-danger:  #fca5a5;
            --budget-badge-safe-bg: rgba(16,185,129,0.15);
            --budget-badge-warn-bg: rgba(245,158,11,0.15);
            --budget-badge-danger-bg: rgba(239,68,68,0.15);
            --modal-overlay:        rgba(0,0,0,0.65);
            --modal-bg:              #1a2235;
        }

        /* =============================================
           BASE
        ============================================= */
        html { font-family:'Inter',-apple-system,BlinkMacSystemFont,sans-serif; font-size:14px; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-base); color:var(--text-primary); min-height:100vh; transition:background 0.25s, color 0.25s; }

        /* =============================================
           APP SHELL
        ============================================= */
        .app-shell { display:flex; min-height:100vh; }

        /* =============================================
           SIDEBAR
        ============================================= */
        .sidebar {
            width:200px; background:var(--bg-sidebar);
            border-right:1px solid var(--border);
            display:flex; flex-direction:column;
            position:fixed; top:0; left:0; height:100vh; z-index:100;
            transition:background 0.25s, border-color 0.25s;
        }

        .sidebar-brand {
            display:flex; align-items:center; gap:0.5rem;
            padding:1.125rem 1.25rem; border-bottom:1px solid var(--border);
            text-decoration:none; transition:border-color 0.25s;
        }
        .brand-bolt {
            width:34px; height:34px; background:var(--blue-600);
            border-radius:50%; display:flex; align-items:center; justify-content:center;
            flex-shrink:0; box-shadow:0 2px 8px rgba(74,124,246,0.35);
        }
        .brand-bolt svg { width:16px; height:16px; fill:white; }
        .brand-text { font-size:1.1rem; font-weight:700; letter-spacing:-0.02em; }
        .brand-text .volt { color:var(--text-primary); transition:color 0.25s; }
        .brand-text .wise { color:var(--blue-600); }

        .sidebar-nav { padding:1rem 0.75rem; display:flex; flex-direction:column; gap:2px; flex:1; }

        .nav-item {
            display:flex; align-items:center; gap:0.625rem;
            padding:0.6rem 0.875rem; border-radius:8px;
            text-decoration:none; font-size:0.875rem; font-weight:500;
            color:var(--text-muted); transition:background 0.15s, color 0.15s;
        }
        .nav-item:hover  { background:var(--nav-hover-bg); color:var(--text-primary); }
        .nav-item.active { background:var(--nav-active-bg); color:var(--nav-active-color); font-weight:600; }
        .nav-icon { width:17px; height:17px; flex-shrink:0; }

        .sidebar-footer { padding:0.75rem; border-top:1px solid var(--border); transition:border-color 0.25s; }
        .logout-btn {
            display:flex; align-items:center; gap:0.625rem;
            padding:0.6rem 0.875rem; border-radius:8px;
            background:none; border:none; cursor:pointer;
            font-size:0.875rem; font-weight:500; color:var(--text-muted);
            font-family:'Inter',sans-serif; transition:background 0.15s,color 0.15s; width:100%;
        }
        .logout-btn:hover { background:var(--logout-hover-bg); color:var(--logout-hover-color); }
        .logout-btn svg { width:16px; height:16px; }

        /* =============================================
           MAIN AREA
        ============================================= */
        .main-area { margin-left:200px; flex:1; display:flex; flex-direction:column; min-height:100vh; }

        /* TOP BAR */
        .topbar {
            height:60px; background:var(--bg-topbar);
            border-bottom:1px solid var(--border);
            display:flex; align-items:center; justify-content:flex-end;
            padding:0 1.75rem; gap:0.75rem;
            position:sticky; top:0; z-index:50;
            transition:background 0.25s, border-color 0.25s;
        }
        .topbar-icon-btn {
            width:36px; height:36px; display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:var(--icon-btn-bg); border:1px solid var(--border);
            cursor:pointer; color:var(--text-muted);
            transition:background 0.2s, color 0.2s, transform 0.2s;
            position:relative;
        }
        .topbar-icon-btn:hover { background:var(--icon-btn-hover); color:var(--text-primary); transform:rotate(20deg); }
        .topbar-icon-btn svg { width:17px; height:17px; }

        /* Theme icon swap */
        .theme-icon { position:absolute; transition:opacity 0.3s, transform 0.3s; }
        [data-theme="light"] .icon-moon { opacity:1; transform:scale(1); }
        [data-theme="light"] .icon-sun  { opacity:0; transform:scale(0.5); }
        [data-theme="dark"]  .icon-sun  { opacity:1; transform:scale(1); }
        [data-theme="dark"]  .icon-moon { opacity:0; transform:scale(0.5); }

        .topbar-divider { width:1px; height:22px; background:var(--border); transition:background 0.25s; }
        .topbar-avatar {
            width:36px; height:36px; border-radius:50%;
            background:var(--blue-100); border:2px solid var(--border);
            display:flex; align-items:center; justify-content:center;
            font-size:0.8125rem; font-weight:700; color:var(--blue-600); cursor:pointer;
            transition:border-color 0.25s, background 0.25s;
        }

        /* Avatar dropdown menu */
        .avatar-menu {
            position:absolute;
            top:calc(100% + 10px);
            right:0;
            background:var(--avatar-menu-bg);
            border:1px solid var(--avatar-menu-border);
            border-radius:10px;
            padding:0.5rem;
            display:none;
            z-index:999;
            min-width:150px;
            box-shadow:0 4px 20px rgba(0,0,0,0.12);
            transition:background 0.25s, border-color 0.25s;
        }
        .avatar-menu.open { display:block; }
        .avatar-menu a,
        .avatar-menu button {
            display:flex; align-items:center; gap:0.5rem;
            width:100%; padding:0.5rem 0.75rem;
            border-radius:6px; text-decoration:none;
            font-size:0.8125rem; font-weight:500;
            color:var(--avatar-menu-color);
            background:none; border:none; cursor:pointer;
            font-family:'Inter',sans-serif;
            transition:background 0.15s, color 0.15s;
            text-align:left;
        }
        .avatar-menu a:hover { background:var(--nav-hover-bg); }
        .avatar-menu button:hover { background:var(--logout-hover-bg); color:var(--logout-hover-color); }
        .avatar-menu-divider { height:1px; background:var(--border); margin:0.25rem 0; }

        /* PAGE CONTENT */
        .page-content { padding:1.75rem; flex:1; }

        /* =============================================
           SHARED COMPONENT STYLES
        ============================================= */

        /* Flash */
        .flash {
            display:flex; align-items:center; gap:0.5rem;
            padding:0.75rem 1rem; border-radius:8px; font-size:0.8125rem;
            margin-bottom:1.25rem;
            background:var(--flash-bg); border:1px solid var(--flash-border); color:var(--flash-color);
        }
        .flash svg { width:14px; height:14px; flex-shrink:0; }

        /* Page header */
        .page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:1.5rem; }
        .page-title    { font-size:1.5rem; font-weight:700; letter-spacing:-0.025em; color:var(--text-primary); transition:color 0.25s; }
        .page-subtitle { font-size:0.875rem; color:var(--text-muted); margin-top:0.25rem; transition:color 0.25s; }

        /* Cards */
        .card {
            background:var(--bg-card); border:1px solid var(--border);
            border-radius:12px; box-shadow:var(--shadow-card);
            transition:background 0.25s, border-color 0.25s, box-shadow 0.25s;
        }
        .card-body { padding:1.25rem; }
        .card-title    { font-size:0.875rem; font-weight:600; color:var(--text-primary); transition:color 0.25s; }
        .card-subtitle { font-size:0.75rem; color:var(--text-faint); margin-top:2px; transition:color 0.25s; }

        /* Stat card */
        .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.5rem; }
        .stat-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.75rem; }
        .stat-label  { font-size:0.8125rem; font-weight:500; color:var(--text-muted); transition:color 0.25s; }
        .stat-value  { font-size:1.625rem; font-weight:700; color:var(--text-primary); letter-spacing:-0.025em; line-height:1; margin-bottom:0.375rem; transition:color 0.25s; }
        .stat-value small { font-size:1rem; font-weight:500; color:var(--text-muted); }
        .stat-value.na { color:var(--text-faint); }
        .stat-detail { font-size:0.75rem; color:var(--text-faint); transition:color 0.25s; }

        .stat-icon { width:28px; height:28px; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:background 0.25s,color 0.25s; }
        .stat-icon svg { width:14px; height:14px; }
        .icon-blue   { background:var(--icon-blue-bg);   color:var(--icon-blue-fg); }
        .icon-green  { background:var(--icon-green-bg);  color:var(--icon-green-fg); }
        .icon-orange { background:var(--icon-orange-bg); color:var(--icon-orange-fg); }
        .icon-purple { background:var(--icon-purple-bg); color:var(--icon-purple-fg); }

        /* Chart row */
        .chart-row { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.5rem; }
        .chart-card { display:flex; flex-direction:column; min-height:220px; }

        /* Empty state */
        .empty-state {
            flex:1; display:flex; align-items:center; justify-content:center;
            flex-direction:column; gap:0.5rem; color:var(--text-faint);
        }
        .empty-state svg { width:36px; height:36px; opacity:0.35; }
        .empty-state p   { font-size:0.8125rem; text-align:center; }

        /* Welcome banner */
        .welcome-banner {
            background:var(--bg-banner); border:1px solid var(--bg-banner-border);
            border-radius:12px; padding:1.25rem 1.5rem; margin-bottom:1.5rem;
            transition:border-color 0.25s;
        }
        .welcome-top { display:flex; align-items:center; gap:0.875rem; margin-bottom:0.6rem; }
        .welcome-bolt {
            width:40px; height:40px; background:var(--blue-600); border-radius:50%;
            display:flex; align-items:center; justify-content:center; flex-shrink:0;
            box-shadow:0 3px 10px rgba(74,124,246,0.4);
        }
        .welcome-bolt svg { width:18px; height:18px; fill:white; }
        .welcome-heading      { font-size:1rem; font-weight:700; color:var(--text-primary); transition:color 0.25s; }
        .welcome-heading span { color:var(--blue-600); }
        .welcome-user         { font-size:0.8125rem; color:var(--text-muted); transition:color 0.25s; }
        .welcome-desc         { font-size:0.8125rem; color:var(--text-muted); margin-bottom:1rem; transition:color 0.25s; }

        .welcome-features { display:grid; grid-template-columns:repeat(3,1fr); gap:0.75rem; }
        .welcome-feature {
            background:var(--bg-feature); border:1px solid var(--bg-banner-border);
            border-radius:8px; padding:0.75rem 1rem;
            display:flex; align-items:flex-start; gap:0.625rem;
            transition:background 0.25s, border-color 0.25s;
        }
        .wf-icon { width:28px; height:28px; background:var(--wf-icon-bg); color:var(--wf-icon-fg); border-radius:6px; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.25s,color 0.25s; }
        .wf-icon.green { background:var(--wf-green-bg); color:var(--wf-green-fg); }
        .wf-icon svg { width:14px; height:14px; }
        .wf-title { font-size:0.8125rem; font-weight:600; color:var(--text-secondary); transition:color 0.25s; }
        .wf-desc  { font-size:0.75rem; color:var(--text-muted); margin-top:2px; transition:color 0.25s; }

        /* Tips */
        .tips-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:0.875rem; }
        .tip-item {
            background:var(--bg-tip); border:1px solid var(--border);
            border-radius:10px; padding:1rem;
            display:flex; align-items:flex-start; gap:0.75rem;
            transition:background 0.25s, border-color 0.15s, box-shadow 0.15s;
        }
        .tip-item:hover { border-color:var(--blue-600); box-shadow:0 2px 10px rgba(74,124,246,0.15); }
        .tip-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0; transition:background 0.25s,color 0.25s; }
        .tip-icon svg { width:16px; height:16px; }
        .tip-icon-yellow { background:var(--tip-yellow-bg); color:var(--tip-yellow-fg); }
        .tip-icon-blue   { background:var(--tip-blue-bg);   color:var(--tip-blue-fg); }
        .tip-icon-orange { background:var(--tip-orange-bg); color:var(--tip-orange-fg); }
        .tip-title { font-size:0.8125rem; font-weight:600; color:var(--text-secondary); transition:color 0.25s; }
        .tip-desc  { font-size:0.75rem; color:var(--text-muted); margin-top:3px; line-height:1.4; transition:color 0.25s; }

        /* Add Device button */
        .btn-primary {
            display:flex; align-items:center; gap:0.375rem;
            padding:0.5rem 1rem; background:var(--blue-600); color:white;
            border:none; border-radius:8px; font-size:0.875rem; font-weight:600;
            font-family:'Inter',sans-serif; cursor:pointer; white-space:nowrap;
            transition:background 0.15s, transform 0.15s, box-shadow 0.15s;
            box-shadow:0 2px 8px rgba(74,124,246,0.3);
        }
        .btn-primary:hover { background:var(--blue-700); transform:translateY(-1px); box-shadow:0 4px 14px rgba(74,124,246,0.4); }
        .btn-primary svg { width:14px; height:14px; }

        /* =============================================
           BUDGET TRACKER CARD
        ============================================= */
        .budget-card { margin-bottom: 1.5rem; }
        .budget-card-inner { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:1.25rem; }

        .budget-left { flex:1; min-width:0; }
        .budget-amounts {
            display:flex; align-items:baseline; gap:0.375rem;
            margin-bottom:0.625rem;
        }
        .budget-used {
            font-size:1.75rem; font-weight:700; letter-spacing:-0.03em;
            color:var(--text-primary); transition:color 0.25s;
        }
        .budget-sep { font-size:1rem; color:var(--text-faint); }
        .budget-total { font-size:1rem; font-weight:500; color:var(--text-muted); }
        .budget-no-set { font-size:0.8125rem; color:var(--text-faint); font-style:italic; margin-bottom:0.625rem; }

        /* Progress bar track */
        .budget-track {
            width:100%; height:10px; background:var(--budget-track-bg);
            border-radius:99px; overflow:hidden; margin-bottom:0.5rem;
        }
        .budget-fill {
            height:100%; border-radius:99px;
            background:var(--budget-bar-safe);
            transition:width 0.8s cubic-bezier(.4,0,.2,1), background 0.4s;
            will-change:width;
        }
        .budget-fill.warn   { background:var(--budget-bar-warn); }
        .budget-fill.danger { background:var(--budget-bar-danger); }

        .budget-meta { display:flex; align-items:center; gap:0.5rem; }
        .budget-pct-badge {
            display:inline-flex; align-items:center;
            padding:0.2rem 0.55rem; border-radius:99px;
            font-size:0.75rem; font-weight:700;
            background:var(--budget-badge-safe-bg);
            color:var(--budget-label-safe);
            transition:background 0.3s,color 0.3s;
        }
        .budget-pct-badge.warn   { background:var(--budget-badge-warn-bg);   color:var(--budget-label-warn); }
        .budget-pct-badge.danger { background:var(--budget-badge-danger-bg); color:var(--budget-label-danger); }
        .budget-pct-label { font-size:0.75rem; color:var(--text-faint); }

        .budget-right { display:flex; flex-direction:column; align-items:flex-end; gap:0.5rem; flex-shrink:0; }
        .budget-actions { display:flex; gap:0.5rem; align-items:center; }
        .btn-budget-set {
            display:inline-flex; align-items:center; gap:0.375rem;
            padding:0.45rem 0.875rem; border-radius:8px;
            font-size:0.8125rem; font-weight:600; font-family:'Inter',sans-serif;
            background:var(--blue-600); color:#fff; border:none; cursor:pointer;
            transition:background 0.15s,transform 0.15s,box-shadow 0.15s;
            box-shadow:0 2px 8px rgba(74,124,246,0.3);
        }
        .btn-budget-set:hover { background:var(--blue-700); transform:translateY(-1px); box-shadow:0 4px 12px rgba(74,124,246,0.4); }
        .btn-budget-set svg { width:13px; height:13px; }
        .btn-budget-clear {
            display:inline-flex; align-items:center; gap:0.3rem;
            padding:0.45rem 0.75rem; border-radius:8px;
            font-size:0.8125rem; font-weight:500; font-family:'Inter',sans-serif;
            background:none; color:var(--text-faint); border:1px solid var(--border);
            cursor:pointer; transition:border-color 0.15s,color 0.15s,background 0.15s;
        }
        .btn-budget-clear:hover { border-color:#ef4444; color:#ef4444; background:rgba(239,68,68,0.05); }
        .btn-budget-clear svg { width:12px; height:12px; }

        /* =============================================
           BUDGET MODAL
        ============================================= */
        .modal-overlay {
            position:fixed; inset:0; background:var(--modal-overlay);
            z-index:1000; display:flex; align-items:center; justify-content:center;
            opacity:0; pointer-events:none;
            transition:opacity 0.2s;
            backdrop-filter:blur(2px);
        }
        .modal-overlay.open { opacity:1; pointer-events:auto; }
        .modal {
            background:var(--modal-bg); border:1px solid var(--border);
            border-radius:16px; padding:1.75rem; width:100%; max-width:420px;
            box-shadow:0 20px 60px rgba(0,0,0,0.2);
            transform:scale(0.95) translateY(8px);
            transition:transform 0.25s cubic-bezier(.4,0,.2,1), background 0.25s, border-color 0.25s;
        }
        .modal-overlay.open .modal { transform:scale(1) translateY(0); }
        .modal-header {
            display:flex; align-items:center; justify-content:space-between;
            margin-bottom:1.25rem;
        }
        .modal-title { font-size:1rem; font-weight:700; color:var(--text-primary); transition:color 0.25s; }
        .modal-close {
            width:28px; height:28px; display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:none; border:none; cursor:pointer;
            color:var(--text-faint); transition:background 0.15s,color 0.15s;
        }
        .modal-close:hover { background:var(--icon-btn-hover); color:var(--text-primary); }
        .modal-close svg { width:14px; height:14px; }
        .modal-desc { font-size:0.8125rem; color:var(--text-muted); margin-bottom:1.25rem; line-height:1.5; transition:color 0.25s; }
        .modal-label {
            display:block; font-size:0.8125rem; font-weight:600;
            color:var(--text-secondary); margin-bottom:0.4rem; transition:color 0.25s;
        }
        .input-with-prefix {
            display:flex; align-items:center;
            border:1.5px solid var(--border); border-radius:9px; overflow:hidden;
            transition:border-color 0.2s, box-shadow 0.2s;
        }
        .input-with-prefix:focus-within {
            border-color:var(--blue-600);
            box-shadow:0 0 0 3px rgba(74,124,246,0.15);
        }
        .input-prefix {
            padding:0.65rem 0.75rem;
            background:var(--bg-tip); color:var(--text-muted);
            font-size:0.875rem; font-weight:600;
            border-right:1.5px solid var(--border);
            white-space:nowrap; transition:background 0.25s, border-color 0.25s, color 0.25s;
        }
        .input-budget {
            flex:1; padding:0.65rem 0.875rem;
            background:transparent; border:none; outline:none;
            font-size:0.9375rem; font-weight:600;
            color:var(--text-primary); font-family:'Inter',sans-serif;
            transition:color 0.25s;
        }
        .input-budget::placeholder { color:var(--text-faint); font-weight:400; }
        .modal-hint { font-size:0.75rem; color:var(--text-faint); margin-top:0.375rem; transition:color 0.25s; }
        .modal-actions {
            display:flex; gap:0.625rem; margin-top:1.5rem;
        }
        .btn-modal-cancel {
            flex:1; padding:0.6rem; border-radius:8px;
            background:none; border:1.5px solid var(--border);
            font-size:0.875rem; font-weight:600; font-family:'Inter',sans-serif;
            color:var(--text-muted); cursor:pointer;
            transition:border-color 0.15s,color 0.15s,background 0.15s;
        }
        .btn-modal-cancel:hover { background:var(--nav-hover-bg); color:var(--text-primary); }
        .btn-modal-save {
            flex:2; padding:0.6rem; border-radius:8px;
            background:var(--blue-600); border:none; color:#fff;
            font-size:0.875rem; font-weight:700; font-family:'Inter',sans-serif;
            cursor:pointer; transition:background 0.15s,transform 0.1s,box-shadow 0.15s;
            box-shadow:0 2px 8px rgba(74,124,246,0.3);
        }
        .btn-modal-save:hover { background:var(--blue-700); transform:translateY(-1px); box-shadow:0 4px 14px rgba(74,124,246,0.4); }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 1100px) {
            .stat-grid        { grid-template-columns:repeat(2,1fr); }
            .welcome-features { grid-template-columns:1fr 1fr; }
        }
        @media (max-width: 768px) {
            .sidebar    { display:none; }
            .main-area  { margin-left:0; }
            .stat-grid  { grid-template-columns:1fr 1fr; }
            .chart-row  { grid-template-columns:1fr; }
            .tips-grid  { grid-template-columns:1fr; }
            .welcome-features { grid-template-columns:1fr; }
            .page-header { flex-direction:column; gap:0.75rem; }
        }
    </style>

    @yield('styles')
</head>
<body>

{{-- Apply saved theme before paint to avoid flash --}}
<script>
    (function(){
        const t = localStorage.getItem('voltwise-theme') || 'light';
        document.documentElement.setAttribute('data-theme', t);
    })();
</script>

<div class="app-shell">

    {{-- ===== SIDEBAR ===== --}}
    <aside class="sidebar">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : url('/dashboard') }}" class="sidebar-brand">
            <div class="brand-bolt">
                <svg viewBox="0 0 24 24"><path d="M13 2L4.5 13.5H11L10 22L19.5 10.5H13L13 2Z"/></svg>
            </div>
            <span class="brand-text"><span class="volt">Volt</span><span class="wise">Wise</span></span>
        </a>

        <nav class="sidebar-nav" aria-label="Main">
            @if(auth()->user()->isAdmin())
                {{-- ========== ADMIN SIDEBAR ========== --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.master-devices.index') }}"
                   class="nav-item {{ request()->routeIs('admin.master-devices.*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 7V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v3"/>
                    </svg>
                    Master Devices
                </a>
            @else
                {{-- ========== USER SIDEBAR (unchanged) ========== --}}
                <a href="{{ url('/dashboard') }}"
                   class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                        <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                    </svg>
                    Dashboard
                </a>
                <a href="#" class="nav-item {{ request()->is('devices*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                    My Devices
                </a>
                <a href="#" class="nav-item {{ request()->is('tracker*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Daily Tracker
                </a>
                <a href="#" class="nav-item {{ request()->is('analytics*') ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6"  y1="20" x2="6"  y2="14"/>
                    </svg>
                    Analytics
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Sign Out
                </button>
            </form>
        </div>
    </aside>


    {{-- ===== MAIN ===== --}}
    <div class="main-area">

        {{-- Top Bar --}}
        <header class="topbar">
            <button class="topbar-icon-btn" id="theme-toggle" title="Toggle dark mode" aria-label="Toggle dark mode">
                <svg class="theme-icon icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
                <svg class="theme-icon icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1"  x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22"   x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12"  x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78"  x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
                </svg>
            </button>
            <div class="topbar-divider"></div>
            <div style="position:relative;">
                <div class="topbar-avatar" id="avatar-btn" title="{{ auth()->user()->name ?? 'User' }}">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                </div>
                <div id="avatar-menu" class="avatar-menu">
                    <a href="{{ route('profile') }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        Profile
                    </a>
                    <div class="avatar-menu-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            @if (session('success'))
                <div class="flash" role="status">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>

    </div>{{-- /.main-area --}}
</div>{{-- /.app-shell --}}

<script>
    // Avatar dropdown
    const avatarBtn = document.getElementById('avatar-btn');
    const avatarMenu = document.getElementById('avatar-menu');

    avatarBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        avatarMenu.classList.toggle('open');
    });

    document.addEventListener('click', function(e) {
        if (!avatarBtn.contains(e.target) && !avatarMenu.contains(e.target)) {
            avatarMenu.classList.remove('open');
        }
    });

    // Dark mode toggle
    document.getElementById('theme-toggle').addEventListener('click', function() {
        const html = document.documentElement;
        const next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('voltwise-theme', next);
    });
</script>

@yield('scripts')

</body>
</html>