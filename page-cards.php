<?php
/* Template Name: Cards */
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
        <a href="<?php echo site_url('/cards'); ?>" class="nav-link text-white active">Cards</a>
        <a href="<?php echo site_url('/loans'); ?>" class="nav-link text-white">Loans</a>
      </nav>
      <div class="mt-auto">
        <a href="<?php echo wp_logout_url( site_url('/login') ); ?>" class="btn btn-outline-light w-100">Logout</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-5">
      <h2 class="fw-bold mb-4">Credit Cards</h2>

      <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold">Register New Card</h5>
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Card Name</label>
              <input type="text" class="form-control" placeholder="e.g. HDFC Visa">
            </div>
            <div class="col-md-3">
              <label class="form-label">Bill Date</label>
              <input type="date" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Due Date</label>
              <input type="date" class="form-control">
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-dark">Save Card</button>
          </div>
        </form>
      </div>

      <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold">Upload CC Statement (PDF)</h5>
        <form enctype="multipart/form-data">
          <input type="file" class="form-control mb-3">
          <button class="btn btn-outline-dark">Upload & Parse</button>
        </form>
      </div>

      <h5 class="fw-bold mb-3">Your Cards</h5>
      <ul class="list-group">
        <li class="list-group-item d-flex justify-content-between align-items-center">
          HDFC Visa
          <span class="badge bg-dark rounded-pill">Due: 2025-09-30</span>
        </li>
      </ul>
    </div>
  </div>
</div>

<?php get_footer(); ?>
