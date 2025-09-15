<aside class="sidebar">
    <?php if (is_active_sidebar('main-sidebar')) : ?>
        <?php dynamic_sidebar('main-sidebar'); ?>
    <?php else : ?>
        <div class="alert alert-info">Add widgets from Appearance → Widgets</div>
    <?php endif; ?>
</aside>
