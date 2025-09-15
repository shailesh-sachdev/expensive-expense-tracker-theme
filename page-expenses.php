<?php
/* Template Name: Expenses */
get_header();
?>

<h1 class="mb-4">My Expenses</h1>

<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Example: fetch expenses via shortcode/plugin
            echo do_shortcode('[expense_tracker_table]');
            ?>
        </tbody>
    </table>
</div>

<?php get_footer(); ?>
