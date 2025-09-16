<?php
/* Template Name: Home Page */

// Redirect logged-in users to the homepage (site root)
if (is_user_logged_in()) {
    wp_redirect(home_url('/dashboard'));
    exit;
}

get_header();
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <title><?php wp_title(); ?></title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 80vh;">
        <h1 class="mb-4">Welcome</h1>
        <div class="d-flex gap-3">
            <a href="<?php echo home_url("/login"); ?>" class="btn btn-primary btn-lg">Login</a>
            <a href="<?php echo home_url("/register"); ?>" class="btn btn-success btn-lg">Register</a>
        </div>
    </div>
    <?php wp_footer(); ?>
    <!-- Bootstrap JS CDN (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php get_footer(); ?>