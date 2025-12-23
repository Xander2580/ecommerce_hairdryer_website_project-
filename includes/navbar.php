<!-- Navigation -->
<nav class="navbar">
    <div class="container-fluid">
        <a class="navbar-brand" href="../customer/index.php">
            StylePro Essentials
        </a>
        
        <div class="navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php" data-page="dashboard">
                        Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="products.php" data-page="products">
                        Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="cart.php" data-page="cart">
                        Cart
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown">
                        User
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="dashboard.php" data-page="dashboard">Dashboard</a></li>
                        <li><a class="dropdown-item" href="profile.php" data-page="profile">My Profile</a></li>
                        <li><a class="dropdown-item" href="orders.php" data-page="orders">My Orders</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        
        <button class="navbar-toggler" type="button" onclick="toggleNavbar()">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
</nav>