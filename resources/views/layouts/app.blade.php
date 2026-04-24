<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FinTrack') — Lacak Keuanganmu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg: #0e0f14;
            --surface: #16181f;
            --card: #1c1e27;
            --border: #2a2d3a;
            --text: #e8eaf0;
            --muted: #6b7080;
            --income: #22d47c;
            --expense: #ff5757;
            --accent: #6c8dfa;
            --accent2: #f7b731;
            --radius: 14px;
            --font-head: 'Syne', sans-serif;
            --font-body: 'DM Sans', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-body);
            font-size: 15px;
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 28px 20px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
        }

        .logo {
            font-family: var(--font-head);
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 40px;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--accent), #9b5de5);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
            padding-left: 12px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            margin-bottom: 2px;
        }

        .nav-link:hover,
        .nav-link.active {
            background: var(--card);
            color: var(--text);
        }

        .nav-link.active {
            color: var(--accent);
        }

        .nav-icon {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .nav-icon svg {
            width: 17px;
            height: 17px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        .logo-icon svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
            stroke-width: 2.2;
            fill: none;
        }

        .nav-divider {
            margin: 20px 0;
            border: none;
            border-top: 1px solid var(--border);
        }

        .sidebar-footer {
            margin-top: auto;
            font-size: 12px;
            color: var(--muted);
            padding: 10px 12px;
            border-top: 1px solid var(--border);
        }

        /* ── Main Content ── */
        .main {
            margin-left: 240px;
            flex: 1;
            padding: 32px 36px;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
        }

        .page-title {
            font-family: var(--font-head);
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .page-subtitle {
            color: var(--muted);
            font-size: 14px;
            margin-top: 4px;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-family: var(--font-body);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent);
            color: #fff;
        }

        .btn-primary:hover {
            background: #5a7de8;
            transform: translateY(-1px);
        }

        .btn-income {
            background: rgba(34, 212, 124, 0.15);
            color: var(--income);
            border: 1px solid rgba(34, 212, 124, 0.3);
        }

        .btn-income:hover {
            background: rgba(34, 212, 124, 0.25);
        }

        .btn-expense {
            background: rgba(255, 87, 87, 0.15);
            color: var(--expense);
            border: 1px solid rgba(255, 87, 87, 0.3);
        }

        .btn-expense:hover {
            background: rgba(255, 87, 87, 0.25);
        }

        .btn-ghost {
            background: transparent;
            color: var(--muted);
            border: 1px solid var(--border);
        }

        .btn-ghost:hover {
            background: var(--card);
            color: var(--text);
        }

        .btn-sm {
            padding: 6px 14px;
            font-size: 13px;
        }

        .btn-danger {
            background: rgba(255, 87, 87, 0.15);
            color: var(--expense);
            border: 1px solid rgba(255, 87, 87, 0.3);
        }

        .btn-danger:hover {
            background: rgba(255, 87, 87, 0.25);
        }

        /* ── Cards ── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 24px;
        }

        /* ── Flash Alerts ── */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: rgba(34, 212, 124, 0.12);
            color: var(--income);
            border: 1px solid rgba(34, 212, 124, 0.25);
        }

        .alert-error {
            background: rgba(255, 87, 87, 0.12);
            color: var(--expense);
            border: 1px solid rgba(255, 87, 87, 0.25);
        }

        /* ── Form ── */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            padding: 11px 14px;
            font-family: var(--font-body);
            font-size: 14px;
            transition: border-color 0.2s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
        }

        .form-control::placeholder {
            color: var(--muted);
        }

        select.form-control option {
            background: var(--surface);
        }

        .form-error {
            font-size: 12px;
            color: var(--expense);
            margin-top: 5px;
        }

        /* ── Table ── */
        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid rgba(42, 45, 58, 0.5);
            font-size: 14px;
            vertical-align: middle;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* ── Badge ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-income {
            background: rgba(34, 212, 124, 0.12);
            color: var(--income);
        }

        .badge-expense {
            background: rgba(255, 87, 87, 0.12);
            color: var(--expense);
        }

        /* ── Grid ── */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* ── Amount colors ── */
        .amount-income {
            color: var(--income);
            font-weight: 600;
        }

        .amount-expense {
            color: var(--expense);
            font-weight: 600;
        }

        /* ── Pagination ── */
        .pagination {
            display: flex;
            gap: 6px;
            justify-content: center;
            margin-top: 24px;
            list-style: none;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--muted);
            font-size: 13px;
            text-decoration: none;
            transition: all 0.2s;
        }

        .pagination li.active span {
            background: var(--accent);
            color: #fff;
            border-color: var(--accent);
        }

        .pagination li a:hover {
            background: var(--border);
            color: var(--text);
        }

        /* ── Responsive ── */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main {
                margin-left: 0;
                padding: 20px 16px;
            }

            .grid-4,
            .grid-3,
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}" class="logo">
            <span class="logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17.93V18h-2v1.93C7.06 19.44 4.56 16.94 4.07 14H6v-2H4.07C4.56 7.06 7.06 4.56 10 4.07V6h2V4.07c2.94.49 5.44 2.99 5.93 5.93H16v2h1.93c-.49 2.94-2.99 5.44-5.93 5.93z"
                        stroke="none" fill="white" opacity="0.9" />
                    <circle cx="12" cy="12" r="3" stroke="none" fill="white" />
                </svg>
            </span>
            FinTrack
        </a>

        <div class="nav-label">Menu</div>

        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" />
                    <rect x="14" y="3" width="7" height="7" />
                    <rect x="14" y="14" width="7" height="7" />
                    <rect x="3" y="14" width="7" height="7" />
                </svg>
            </span>
            Dashboard
        </a>

        <a href="{{ route('transactions.index') }}"
            class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
            </span>
            Transaksi
        </a>

        <a href="{{ route('budgets.index') }}"
            class="nav-link {{ request()->routeIs('budgets.*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
            </span>
            Anggaran
        </a>

        <a href="{{ route('goals.index') }}"
            class="nav-link {{ request()->routeIs('goals.*') ? 'active' : '' }}">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <polyline points="12 6 12 12 16 14" />
                </svg>
            </span>
            Target Keuangan
        </a>

        <a href="{{ route('transactions.create') }}?type=income" class="nav-link">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="19" x2="12" y2="5" />
                    <polyline points="5 12 12 5 19 12" />
                </svg>
            </span>
            Tambah Pemasukan
        </a>

        <a href="{{ route('transactions.create') }}?type=expense" class="nav-link">
            <span class="nav-icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19" />
                    <polyline points="19 12 12 19 5 12" />
                </svg>
            </span>
            Tambah Pengeluaran
        </a>

        <div class="sidebar-footer">
            FinTrack v1.0 · {{ now()->format('Y') }}
        </div>
    </aside>

    <!-- Main Content -->
    <main class="main">
        @if (session('success'))
            <div class="alert alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                    stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="15" y1="9" x2="9" y2="15" />
                    <line x1="9" y1="9" x2="15" y2="15" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
