<?php
/* Template Name: Register */
get_header();
?>

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="card shadow-lg p-4" style="max-width: 480px; width: 100%; border-radius: 1rem;">
    <h3 class="text-center mb-4 fw-bold">Create Account</h3>

    <form method="post" action="<?php echo esc_url( site_url('wp-login.php?action=register', 'login_post') ); ?>">
      <div class="mb-3">
        <label for="user_login" class="form-label">Username</label>
        <input type="text" name="user_login" id="user_login" class="form-control form-control-lg" required>
      </div>

      <div class="mb-3">
        <label for="user_email" class="form-label">Email</label>
        <input type="email" name="user_email" id="user_email" class="form-control form-control-lg" required>
      </div>

      <div class="d-grid mb-3">
        <button type="submit" class="btn btn-dark btn-lg">Register</button>
      </div>

      <div class="text-center">
        <a href="<?php echo esc_url( wp_login_url() ); ?>">Already have an account?</a>
      </div>
    </form>
  </div>
</div>

<?php get_footer(); ?>
