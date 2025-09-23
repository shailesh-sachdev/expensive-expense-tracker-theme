<?php
/**
 * Template Name: Register
 */
get_header();

if ( is_user_logged_in() ) {
    wp_redirect( site_url( '/dashboard' ) );
    exit;
}

$errors = get_transient('expensive_register_errors');
if ($errors) delete_transient('expensive_register_errors');
?>

<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm border-0 p-4" style="max-width: 450px; width: 100%;">
        <h3 class="fw-bold mb-3 text-center">Create Account</h3>

        <?php if ( ! empty($errors) ) : ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error) : ?>
                    <div><?php echo esc_html($error); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <?php wp_nonce_field('exp_register_action', 'exp_register_nonce'); ?>

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <!-- Family Members Repeater -->
            <h5 class="fw-bold">Add Family Members</h5>
            <p class="text-muted small">You can add family members now or later from the dashboard.</p>
            <div id="family-members-wrapper"></div>
            <button type="button" class="btn btn-sm btn-outline-primary mt-2 mb-4" id="add-family-member">
                + Add Family Member
            </button>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <div class="text-center mt-3">
            <a href="<?php echo wp_login_url(); ?>" class="text-decoration-none">Already have an account? Sign in</a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const wrapper = document.getElementById("family-members-wrapper");
    const button = document.getElementById("add-family-member");

    button.addEventListener("click", function() {
        const index = wrapper.children.length;
        const html = `
            <div class="border rounded p-3 mb-2 bg-light family-member-item">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Family Member ${index + 1}</h6>
                    <button type="button" class="btn-close remove-family-member"></button>
                </div>
                <div class="mb-2">
                    <label class="form-label">Name</label>
                    <input type="text" name="family_members[${index}][name]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email (optional)</label>
                    <input type="email" name="family_members[${index}][email]" class="form-control">
                </div>
                <div class="mb-2">
                    <label class="form-label">Role</label>
                    <select name="family_members[${index}][role]" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="parent">Parent</option>
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        `;
        wrapper.insertAdjacentHTML("beforeend", html);

        wrapper.querySelectorAll(".remove-family-member").forEach(btn => {
            btn.addEventListener("click", function() {
                this.closest(".family-member-item").remove();
            });
        });
    });
});
</script>

<?php get_footer(); ?>
