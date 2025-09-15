<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="<?php echo home_url(); ?>">
      <?php bloginfo('name'); ?>
    </a>
    <?php
        wp_nav_menu(array(
    'theme_location' => 'primary',
    'container' => false,
    'menu_class' => 'navbar-nav ms-auto',
));

    ?>
  </div>
</nav>

<div class="container mt-4">
