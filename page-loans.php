<?php
/* Template Name: Loans */
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
        <a href="<?php echo site_url('/loans'); ?>" class="nav-link text-white active">Loans</a>
      </nav>
      <div class="mt-auto">
        <a href="<?php echo wp_logout_url( site_url('/login') ); ?>" class="btn btn-outline-light w-100">Logout</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10 p-5">
      <h2 class="fw-bold mb-4">Loans</h2>

      <div class="card shadow-sm p-4 mb-4">
        <h5 class="fw-bold">Add Loan</h5>
        <form>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Loan Amount</label>
              <input type="number" class="form-control" placeholder="₹0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Bank</label>
              <input type="text" class="form-control" placeholder="e.g. SBI">
            </div>
            <div class="col-md-4">
              <label class="form-label">EMI Amount</label>
              <input type="number" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Due Date</label>
              <input type="date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Months Pending</label>
              <input type="number" class="form-control">
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-dark">Save Loan</button>
          </div>
        </form>
      </div>

      <h5 class="fw-bold mb-3">Active Loans</h5>
      <table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>Bank</th>
            <th>Amount</th>
            <th>EMI</th>
            <th>Due Date</th>
            <th>Months Left</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>SBI</td>
            <td>₹2,00,000</td>
            <td>₹10,000</td>
            <td>2025-10-05</td>
            <td>18</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php get_footer(); ?>
