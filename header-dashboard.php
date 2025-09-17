<?php
/**
 * Dashboard Header
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-light dashboard-body' ); ?>>
    <?php wp_body_open(); ?>

    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-dark text-white p-3 vh-100" style="min-width:220px;">
            <h5 class="fw-bold mb-4"><?php bloginfo( 'name' ); ?></h5>
            <ul class="nav flex-column">
                <li class="nav-item"><a class="nav-link text-white" href="<?php echo site_url('/dashboard'); ?>">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="<?php echo site_url('/expenses'); ?>">Expenses</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="<?php echo site_url('/loans'); ?>">Loans</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="<?php echo site_url('/credit-cards'); ?>">Credit Cards</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <div class="flex-grow-1">
            <header class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white">
                <button class="btn btn-outline-primary d-lg-none" id="sidebarToggle">☰</button>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="userDropdown" data-bs-toggle="dropdown">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode( wp_get_current_user()->display_name ); ?>&background=random" class="rounded-circle me-2" width="32" height="32" alt="User Avatar">
                        <span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?php echo site_url('/profile'); ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?php echo wp_logout_url( site_url('/login') ); ?>">Logout</a></li>
                    </ul>
                </div>
            </header>

            <main class="container-fluid py-4">
