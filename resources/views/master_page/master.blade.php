<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Small POS - @yield('title')</title>

    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            color: white;
            padding: 20px;
            transition: all 0.3s;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background: #3498db;
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        .stat-card .stat-label {
            color: #6c757d;
        }

        .language-switcher {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .language-switcher .btn {
            width: 100%;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h3 class="mb-4">POS System</h3>
        <nav class="nav flex-column">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bx bxs-dashboard me-2"></i> {{ __('messages.dashboard') }}
            </a>
            <a href="{{ route('sales.index') }}" class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="bx bxs-cart me-2"></i> {{ __('messages.sales') }}
            </a>
            @if (auth()->user()->role == 'admin')
                <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.index') || request()->routeIs('products.create') || request()->routeIs('products.show') || request()->routeIs('products.edit') ? 'active' : '' }}">
                    <i class="bx bxs-box me-2"></i> {{ __('messages.products') }}
                </a>
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.index') || request()->routeIs('customers.create') || request()->routeIs('customers.show') || request()->routeIs('customers.edit') ? 'active' : '' }}">
                    <i class="bx bxs-user me-2"></i> {{ __('messages.customers') }}
                </a>
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="bx bxs-report me-2"></i> {{ __('messages.reports') }}
                </a>
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}">
                    <i class="bx bxs-cog me-2"></i> {{ __('messages.settings') }}
                </a>
            @endif
        </nav>

        <!-- Language Switcher -->
        <div class="language-switcher">
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle w-100" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {{app()->getLocale() == 'en' ? 'English' : 'ភាសាខ្មែរ'}}
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}"
                           href="{{ route('language.switch', 'en') }}">
                            <i class="bx bxs-flag-alt me-2"></i> {{ __('messages.english') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'km' ? 'active' : '' }}"
                           href="{{ route('language.switch', 'km') }}">
                            <i class="bx bxs-flag-alt me-2"></i> {{ __('messages.khmer') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ __(session('success')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ __(session('error')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>
