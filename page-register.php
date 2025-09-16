<?php
/* Template Name: Register Page */
get_header();

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['username']) && !empty($_POST['email']) ) {
    $userdata = array(
        'user_login' => sanitize_text_field($_POST['username']),
        'user_email' => sanitize_email($_POST['email']),
        'user_pass'  => $_POST['password'],
        'role'       => 'member'
    );

    $user_id = wp_insert_user($userdata);

    if (!is_wp_error($user_id)) {
        echo '<div class="alert alert-success">Registration successful. <a href="'.wp_login_url().'">Login here</a>.</div>';
    } else {
        echo '<div class="alert alert-danger">Error: '.$user_id->get_error_message().'</div>';
    }
}
?>

<div class="row justify-content-center">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-dark text-white">Register</div>
      <div class="card-body">
        <form method="post">
          <div class="mb-3">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
