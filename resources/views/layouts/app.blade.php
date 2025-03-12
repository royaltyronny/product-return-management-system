<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Return Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #00bfa5, #00796b);
            color: white;
            min-height: 100vh;
            margin: 0;
        }
        /* Fixed Navbar */
        .navbar {
            background-color: #004d40;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1050;
        }
        /* Push content down so it doesn't hide behind the fixed navbar */
        main {
            margin-top: 70px;
        }
        .navbar .navbar-brand {
            font-weight: bold;
            color: #b2dfdb;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
        }
        .navbar .navbar-brand i {
            margin-right: 10px;
            color: #b2dfdb;
        }
        .navbar-nav .nav-link {
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }
        .navbar-nav .nav-link i {
            margin-right: 8px;
        }
        .navbar-nav .nav-link:hover {
            color: #b2dfdb;
        }
        .search-input {
            border-radius: 20px;
            max-width: 300px;
            padding: 0.5rem;
            margin-right: 10px;
        }
        .profile-icon {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            object-fit: cover;
            border: 2px solid #b2dfdb;
        }
        .dropdown-menu {
            background-color: #004d40;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .dropdown-item {
            color: white;
            display: flex;
            align-items: center;
        }
        .dropdown-item i {
            margin-right: 8px;
        }
        .dropdown-item:hover {
            background-color: #00796b;
            color: #b2dfdb;
        }
        .divider {
            width: 1px;
            height: 30px;
            background: white;
            margin: 0 15px;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <!-- Brand -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    <i class="fas fa-undo-alt"></i> PRMS
                </a>

                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navbar Links -->
                <div class="collapse navbar-collapse" id="navbarContent">
                    <!-- Search Bar -->
                    <form class="d-flex ms-auto me-auto" role="search">
                        <input class="form-control search-input" type="search" placeholder="Search returns..." aria-label="Search">
                        <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
                    </form>

                    <ul class="navbar-nav">
                        <!-- About Us -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('about') }}">
                                <i class="fas fa-info-circle"></i> About
                            </a>
                        </li>

                        <!-- Services -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-cogs"></i> Services
                            </a>
                        </li>

                        <!-- Contact -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="fas fa-envelope"></i> Contact
                            </a>
                        </li>

                        <!-- Profile Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown">
                                <img src="/images/profile.png" class="profile-icon" alt="Profile Icon">
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.view') }}">
                                        <i class="fas fa-user-circle"></i> View Profile
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> Logout
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
