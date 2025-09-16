<?php
/* Template Name: Dashboard */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

$current_user = wp_get_current_user();
?>

<h1>Welcome, <?php echo $current_user->display_name; ?>!</h1>

<ul class="list-group">
  <li class="list-group-item">Email: <?php echo $current_user->user_email; ?></li>
  <li class="list-group-item">Role: <?php echo implode(', ', $current_user->roles); ?></li>
</ul>

<a href="<?php echo wp_logout_url(home_url()); ?>" class="btn btn-danger mt-3">Logout</a>

<?php get_footer(); ?>
