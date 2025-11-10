<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DCMS - Document Content Management System</title>
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- Custom CSS -->
    <link href="{{ url('/css/custom.css') }}" rel="stylesheet">
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
                    <a href="{{ url('/dashboard') }}" class="nav-link">Home</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ route('items.index') }}" class="nav-link">Items</a>
                </li>
                @endauth
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
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
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </nav>

        <!-- Main Sidebar Container -->
        <!-- Main Sidebar Container -->
        @auth
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ url('/dashboard') }}" class="brand-link">
                <i class="fas fa-file-alt brand-icon"></i>
                <span class="brand-text font-weight-light">DCMS</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle img-circle elevation-2"></i>
                    </div>
                    <div class="info">
                        <a href="#" class="d-block">{{ Auth::user()->name }}</a>
                        <!-- In the user panel section, replace the small tag -->
                        <small class="text-light badge role-{{ Auth::user()->role }}">{{ ucfirst(Auth::user()->role) }}</small>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <li class="nav-item">
                                <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li>
                            {{-- <li class="nav-item">
                                <a href="{{ route('dashboard') }}" class="nav-link">
                                    <i class="nav-icon fas fa-tachometer-alt"></i>
                                    <p>Dashboard</p>
                                </a>
                            </li> --}}
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
                                    <p>Items</p>
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
                                    $pendingReviewCount = \App\Models\Item::where('workflow_state', 'submitted')->count();
                                @endphp
                                @if($pendingReviewCount > 0)
                                <li class="nav-item">
                                    <a href="{{ route('items.index') }}?status=submitted" class="nav-link">
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
                                        <p>My Drafts</p>
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
                            {{-- Or group them under a menu --}}
                            <li class="nav-item">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>
                                        Repository
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="{{ route('repository.index') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Homepage</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="{{ route('repository.browse') }}" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Browse All</p>
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
                            <!-- User Management - Admin only -->
                            @if(auth()->user()->hasAnyRole(['admin', 'manager']))
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
                            <h1 class="m-0">@yield('page_title', 'Dashboard')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
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
            <strong>Copyright &copy; 2024 DCMS.</strong> All rights reserved.
        </footer>
        @endauth
    </div>


    <!-- Breeze Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables AFTER jQuery -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
    <!-- AdminLTE App -->
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    <!-- Breeze Scripts -->
    @vite('resources/js/app.js')
    
    @stack('scripts')
</body>
</html>