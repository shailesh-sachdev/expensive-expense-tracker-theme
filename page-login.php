<?php
/* Template Name: Login Page */
get_header();
?>

<div class="row justify-content-center">
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-dark text-white">Login</div>
      <div class="card-body">
        <?php
        $args = array(
            'redirect' => home_url(), 
            'form_id' => 'loginform',
            'label_username' => __( 'Username' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in' => __( 'Login' ),
            'remember' => true
        );
        wp_login_form($args);
        ?>
      </div>
    </div>
  </div>
</div>

<?php get_footer(); ?>
