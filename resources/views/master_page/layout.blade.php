<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'POS System')</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 250px;
        }

        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            background-color: #2c3e50;
            color: white;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            background-color: #1a252f;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu a {
            color: #ecf0f1;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            display: block;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover {
            background-color: #34495e;
            color: white;
        }

        .sidebar-menu a.active {
            background-color: #3498db;
            color: white;
        }

        .sidebar-menu i {
            width: 20px;
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1rem;
            min-height: 100vh;
        }

        /* Top Navigation */
        .top-nav {
            background-color: white;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .user-dropdown img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }

            .sidebar.active {
                margin-left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.active {
                margin-left: var(--sidebar-width);
            }
        }

        /* Language Switcher Styles */
        .language-switcher {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
        }

        .language-switcher .btn {
            width: 100%;
            margin-bottom: 5px;
            background-color: #34495e;
            color: white;
            border: none;
        }

        .language-switcher .btn:hover {
            background-color: #2c3e50;
        }

        .language-switcher .dropdown-menu {
            width: 100%;
            background-color: #34495e;
        }

        .language-switcher .dropdown-item {
            color: white;
        }

        .language-switcher .dropdown-item:hover {
            background-color: #2c3e50;
            color: white;
        }

        .language-switcher .dropdown-item.active {
            background-color: #3498db;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">POS System</h4>
        </div>

        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard') }}
            </a>
            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> {{ __('messages.sales') }}
            </a>
            @if (auth()->user()->role == 'admin')
                <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i> {{ __('messages.products') }}
                </a>
                <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> {{ __('messages.customers') }}
                </a>
                <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> {{ __('messages.reports') }}
                </a>
                <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> {{ __('messages.settings') }}
                </a>
            @endif
        </div>

        <!-- Language Switcher -->
        <div class="language-switcher">
            <div class="dropdown">
                <button class="btn dropdown-toggle w-100" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    {{app()->getLocale() == 'en' ? 'English' : 'ភាសាខ្មែរ'}}
                </button>
                <ul class="dropdown-menu w-100" aria-labelledby="languageDropdown">
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                            {{ __('messages.english') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item {{ app()->getLocale() == 'km' ? 'active' : '' }}" href="{{ route('language.switch', 'km') }}">
                            {{ __('messages.khmer') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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

        <!-- Top Navigation -->
        <div class="top-nav d-flex justify-content-between align-items-center">
            <button class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-dropdown dropdown">
                <a class="dropdown-toggle d-flex align-items-center text-decoration-none" href="#" role="button" data-bs-toggle="dropdown">
                    <img src="https://ui-avatars.com/api/?name=User&background=random" alt="User" class="me-2">
                    <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'User' }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('settings.profile') }}"><i class="fas fa-user me-2"></i>Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        @yield('content')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Sidebar Toggle Script -->
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.main-content').classList.toggle('active');
        });
        function switchLang(lang){
                window.location.href=`/${lang}`
            }
    </script>

    @stack('scripts')
</body>
</html>
