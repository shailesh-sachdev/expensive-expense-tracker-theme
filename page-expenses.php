<?php
/* Template Name: Dashboard */
get_header();

if ( ! is_user_logged_in() ) {
    wp_redirect( site_url('/login') );
    exit;
}
?>

<div class="container-fluid">
  <div class="row vh-100">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 bg-dark text-white d-flex flex-column p-3">
      <h4 class="fw-bold mb-4">💸 Expensive</h4>
      <nav class="nav flex-column">
        <a href="<?php echo site_url('/dashboard'); ?>" class="nav-link text-white">Dashboard</a>
        <a href="<?php echo site_url('/expenses'); ?>" class="nav-link text-white">Expenses</a>
        <a href="<?php echo site_url('/cards'); ?>" class="nav-link text-white">Cards</a>
        <a href="<?php echo site_url('/loans'); ?>" class="nav-link text-white">Loans</a>
      </nav>
      <div class="mt-auto">
        <a href="<?php echo wp_logout_url( site_url('/login') ); ?>" class="btn btn-outline-light w-100">Logout</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-5">
      <h2 class="fw-bold mb-4">Dashboard</h2>
      <p class="text-muted">Welcome back, <?php echo wp_get_current_user()->display_name; ?> 👋</p>

      <div class="row g-4">
        <div class="col-md-4">
          <div class="card shadow-sm p-4">
            <h5>💰 Total Income</h5>
            <p class="fs-4 fw-bold">₹0</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm p-4">
            <h5>📉 Total Expenses</h5>
            <p class="fs-4 fw-bold">₹0</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card shadow-sm p-4">
            <h5>💳 Active Cards</h5>
            <p class="fs-4 fw-bold">0</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
