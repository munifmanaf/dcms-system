<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Pengurusan Koleksi Digital - Jabatan Mufti Brunei')</title>
    
    <!-- Islamic-inspired fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Noto+Naskh+Arabic:wght@400;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    
    <!-- Custom JMB CSS -->
    <style>
        :root {
            --jmb-green: #1a472a;
            --jmb-gold: #d4af37;
            --jmb-cream: #fef9f0;
            --jmb-dark: #2c5530;
            --jmb-light: #4a7c59;
        }
        
        body {
            font-family: 'Inter', 'Amiri', sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Islamic-inspired header */
        .main-header {
            background: linear-gradient(135deg, var(--jmb-green) 0%, var(--jmb-dark) 100%);
            border-bottom: 4px solid var(--jmb-gold);
        }
        
        .navbar-brand {
            font-family: 'Amiri', serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
        }
        
        /* Sidebar styling */
        .sidebar-dark-primary {
            background: linear-gradient(180deg, var(--jmb-dark) 0%, var(--jmb-green) 100%);
        }
        
        .nav-sidebar > .nav-item > .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-left: 3px solid transparent;
            margin: 2px 0;
        }
        
        .nav-sidebar > .nav-item > .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            border-left: 3px solid var(--jmb-gold);
        }
        
        .nav-sidebar > .nav-item > .nav-link.active {
            background-color: rgba(212, 175, 55, 0.2);
            color: white;
            border-left: 3px solid var(--jmb-gold);
        }
        
        .nav-sidebar .nav-icon {
            color: var(--jmb-gold);
        }
        
        /* Card headers with Islamic theme */
        .card-header {
            background: linear-gradient(135deg, var(--jmb-green) 0%, var(--jmb-dark) 100%);
            color: white;
            border-bottom: 2px solid var(--jmb-gold);
            font-family: 'Amiri', serif;
        }
        
        /* Buttons with Islamic theme */
        .btn-primary {
            background: linear-gradient(135deg, var(--jmb-green) 0%, var(--jmb-dark) 100%);
            border: 1px solid var(--jmb-green);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, var(--jmb-dark) 0%, var(--jmb-green) 100%);
            border: 1px solid var(--jmb-gold);
        }
        
        /* Badge styling */
        .badge-primary {
            background-color: var(--jmb-green);
        }
        
        /* Table styling */
        .table thead {
            background-color: var(--jmb-green);
            color: white;
        }
        
        /* Role badges */
        .role-admin { background-color: #dc3545; }
        .role-manager { background-color: #fd7e14; }
        .role-reviewer { background-color: #20c997; }
        .role-user { background-color: #6f42c1; }
        
        /* Islamic geometric pattern background */
        .content-wrapper {
            background-image: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%231a472a' fill-opacity='0.03' fill-rule='evenodd'/%3E%3C/svg%3E");
        }
        
        /* Arabic calligraphy style for important headings */
        .islamic-title {
            font-family: 'Noto Naskh Arabic', 'Amiri', serif;
            font-weight: 700;
            color: var(--jmb-green);
        }
        
        .brand-link {
            border-bottom: 2px solid var(--jmb-gold);
        }
        
        .nav-header {
            color: var(--jmb-gold) !important;
            font-family: 'Amiri', serif;
            font-weight: 700;
        }
    </style>
    
    <!-- Load Chart.js in the head -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Breeze Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                @auth
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ url('/dashboard') }}" class="nav-link">Main Page</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('items.index') }}" class="nav-link">Item</a>
                </li>
                @endauth
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Log In</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">Register</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user mr-1"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" 
                            {{-- href="{{ route('profile.edit') }}" --}}
                            >
                                <i class="fas fa-user-edit mr-2"></i> Profile
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        @auth
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/dashboard') }}" class="brand-link">
                <i class="fas fa-mosque brand-icon" style="color: var(--jmb-gold);"></i>
                <span class="brand-text font-weight-light">JMB DCMS</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle img-circle elevation-2" style="color: var(--jmb-gold);"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        <small class="text-light badge role-{{ Auth::user()->role }}">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <!-- Administration Section - Admin/Manager only -->
                        @if(Auth::user()->hasAnyRole(['admin', 'manager']))
                        <li class="nav-header">ADMINISTRATION</li>
                        
                        <li class="nav-item">
                            <a href="{{ route('communities.index') }}" class="nav-link {{ request()->is('communities*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>Communities</p>
                                <span class="badge badge-primary right">{{ \App\Models\Community::count() }}</span>
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a href="{{ route('collections.index') }}" class="nav-link {{ request()->is('collections*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-folder"></i>
                                <p>Collections</p>
                                <span class="badge badge-primary right">{{ \App\Models\Collection::count() }}</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->is('categories*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tags"></i>
                                <p>Categories</p>
                                <span class="badge badge-primary right">{{ \App\Models\Category::count() }}</span>
                            </a>
                        </li>
                        @endif
                        
                        <!-- Content Section - All authenticated users -->
                        <li class="nav-header">CONTENT</li>
                        
                        <!-- Main Items Count -->
                        <li class="nav-item">
                            <a href="{{ route('items.index') }}" class="nav-link {{ request()->is('items*') && !request()->is('items/create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file"></i>
                                <p>Item</p>
                                <span class="badge badge-info right">
                                    @if(Auth::user()->hasRole('user'))
                                        {{ Auth::user()->items()->count() }}
                                    @elseif(Auth::user()->hasRole('reviewer'))
                                        {{ \App\Models\Item::whereIn('workflow_state', ['submitted', 'under_review'])->count() }}
                                    @elseif(Auth::user()->hasRole('manager') || Auth::user()->hasRole('admin'))
                                        {{ \App\Models\Item::count() }}
                                    @else
                                        {{ Auth::user()->items()->count() }}
                                    @endif
                                </span>
                            </a>
                        </li>
                        
                        <!-- Pending Review (for reviewers, managers, admins) -->
                        @if(Auth::user()->hasAnyRole(['reviewer', 'manager', 'admin']))
                            @php
                                $pendingReviewCount = \App\Models\Item::whereIn('workflow_state', ['draft', 'pending_review'])->count();
                            @endphp
                            @if($pendingReviewCount > 0)
                            <li class="nav-item">
                                <a href="{{ route('items.index') }}?status=pending_review" class="nav-link">
                                    <i class="nav-icon fas fa-clock text-warning"></i>
                                    <p>Pending Review</p>
                                    <span class="badge badge-warning right">{{ $pendingReviewCount }}</span>
                                </a>
                            </li>
                            @endif
                        @endif

                        <!-- User Drafts (for regular users) -->
                        @if(Auth::user()->hasRole('user'))
                            @php
                                $userDraftCount = Auth::user()->items()->where('workflow_state', 'draft')->count();
                            @endphp
                            @if($userDraftCount > 0)
                            <li class="nav-item">
                                <a href="{{ route('items.index') }}?status=draft" class="nav-link">
                                    <i class="nav-icon fas fa-edit text-info"></i>
                                    <p>My Draft</p>
                                    <span class="badge badge-info right">{{ $userDraftCount }}</span>
                                </a>
                            </li>
                            @endif
                        @endif
                        
                        <li class="nav-item">
                            <a href="{{ route('items.create') }}" class="nav-link {{ request()->is('items/create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-plus"></i>
                                <p>Add Item</p>
                            </a>
                        </li>

                        <!-- 🔄 BATCH OPERATIONS -->
                        @if(auth()->user()->hasAnyRole(['admin', 'manager']))
                        <li class="nav-item {{ request()->is('batch*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ request()->is('batch*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-layer-group"></i>
                                <p>
                                    Batch Operation
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('batch.export.form') }}" class="nav-link {{ request()->is('batch/export*') ? 'active' : '' }}">
                                        <i class="fas fa-file-export nav-icon"></i>
                                        <p>Export Data</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('batch.import.form') }}" class="nav-link {{ request()->is('batch/import*') ? 'active' : '' }}">
                                        <i class="fas fa-file-import nav-icon"></i>
                                        <p>Import Data</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('batch.bulk-update.form') }}" class="nav-link {{ request()->is('batch/bulk-update*') ? 'active' : '' }}">
                                        <i class="fas fa-edit nav-icon"></i>
                                        <p>Batch Edit</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        <!-- Repository Menu -->
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-book"></i>
                                <p>
                                    Repositories
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{ route('repository.index') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Main Page</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('repository.browse') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Browse ALL</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('repository.statistics') }}" class="nav-link">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Statistics</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Reports and Analytics -->
                        <li class="nav-header">REPORTS & SEARCHING</li>
                        <li class="nav-item">
                            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-chart-bar"></i>
                                <p>Analitics & Reports</p>
                            </a>
                        </li>

                        <!-- Advanced Search -->
                        <li class="nav-item">
                            <a href="{{ route('items.search') }}" class="nav-link {{ request()->is('search*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-search-plus"></i>
                                <p>Advance Searching</p>
                            </a>
                        </li>

                        <!-- User Management -->
                        
                        @if(auth()->user()->hasAnyRole(['admin']))
                        <li class="nav-header">SYSTEM MANAGEMENT</li>
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>User Management</p>
                            </a>
                        </li>
                        @endif

                        @if(auth()->user()->role === 'admin')
                        <li class="nav-item">
                            <a href="{{ route('system.settings') }}" class="nav-link">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>System Settings</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>
        @endauth

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0 islamic-title">
                                <i class="fas fa-mosque mr-2"></i>
                                @yield('page_title', 'Dashboard')
                            </h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Main Page</a></li>
                                <li class="breadcrumb-item active">@yield('breadcrumb', 'Dashboard')</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
            @stack('scripts')
        </div>

        <!-- Main Footer -->
        @auth
        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2024 <a href="#">Jabatan Mufti Brunei</a>.</strong>.
        </footer>
        @endauth
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables AFTER jQuery -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    <!-- Breeze Scripts -->
    @vite('resources/js/app.js')
    
    @stack('scripts')
</body>
</html>