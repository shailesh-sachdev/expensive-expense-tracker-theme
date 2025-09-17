<?php
/* Template Name: Login */
get_header();
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="max-width: 420px; width: 100%; border-radius: 1rem;">
    <h3 class="text-center mb-4 fw-bold">Welcome Back</h3>

    <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>">
      <div class="mb-3">
        <label for="user_login" class="form-label">Email / Username</label>
        <input type="text" name="log" id="user_login" class="form-control form-control-lg" required>
      </div>

      <div class="mb-3">
        <label for="user_pass" class="form-label">Password</label>
        <input type="password" name="pwd" id="user_pass" class="form-control form-control-lg" required>
      </div>

      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-dark btn-lg">Login</button>
      </div>

      <div class="text-center">
        <a href="<?php echo esc_url( wp_registration_url() ); ?>">Create an account</a>
      </div>
    </form>
  </div>
</div>

<?php get_footer(); ?>
