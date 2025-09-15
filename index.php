<?php get_header(); ?>
<div class="row">
    <div class="col-md-8">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title"><?php the_title(); ?></h2>
                        <p class="card-text"><?php the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            <?php endwhile;
        else :
            echo '<p>No posts found.</p>';
        endif;
        ?>
    </div>

    <div class="col-md-4">
        <?php get_sidebar(); ?>
    </div>
</div>
<canvas id="expenseChart" height="100"></canvas>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('expenseChart').getContext('2d');
    var expenseChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Food", "Travel", "Bills", "Others"],
            datasets: [{
                label: 'Expenses',
                data: [500, 1200, 800, 300],
                backgroundColor: 'rgba(54, 162, 235, 0.7)'
            }]
        }
    });
});
</script>

<?php get_footer(); ?>
