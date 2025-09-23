<?php
/**
 * Template Name: Dashboard
 */
get_header();

if ( ! is_user_logged_in() ) {
    wp_redirect( site_url( '/login' ) );
    exit;
}
use Expensive\Expenses;

// Get all transactions for current user
$transactions = Expenses::get_user_transactions();

// Initialize totals
$total_income = 0;
$total_expense = 0;

foreach ( $transactions as $t ) {
    if ( $t['type'] === 'income' ) {
        $total_income += $t['amount'];
    } elseif ( $t['type'] === 'expense' ) {
        $total_expense += $t['amount'];
    }
}

$current_user_id = get_current_user_id();
$family_members  = get_user_meta( $current_user_id, 'family_members', true ) ?: [];
?>

<div class="container py-4">
    <h2 class="mb-4">Welcome, <?php echo wp_get_current_user()->display_name; ?>!</h2>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Income</h6>
                    <p class="card-text fs-4">₹<?php echo number_format($total_income, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Total Expenses</h6>
                    <p class="card-text fs-4">₹<?php echo number_format($total_expense, 2); ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Pending CC Payments</h6>
                    <p class="card-text fs-4">0</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <h6 class="card-title">Pending Loans</h6>
                    <p class="card-text fs-4">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
        <button class="btn btn-outline-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            + Add Expense
        </button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
            + Add Income
        </button>
    </div>
        <div class="col-md-3">
            <a href="#" class="btn btn-outline-warning w-100 py-3">Manage Credit Cards</a>
        </div>
        <div class="col-md-3">
            <a href="#" class="btn btn-outline-danger w-100 py-3">Manage Loans</a>
        </div>
    </div>

<!-- Family Members Card -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Family Members</h5>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addFamilyMemberModal">
            + Add Family Member
        </button>
    </div>
    <div class="card-body">
        <?php if ( empty( $family_members ) ) : ?>
            <p>No family members added yet.</p>
        <?php else : ?>
            <ul class="list-group list-group-flush">
                <?php foreach ( $family_members as $member ) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo esc_html( $member['name'] ); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo esc_html( ucfirst($member['role']) ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Add Family Member Modal -->
<div class="modal fade" id="addFamilyMemberModal" tabindex="-1" aria-labelledby="addFamilyMemberLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="add-family-member-form" method="post">
        <?php wp_nonce_field( 'exp_add_family_member', 'exp_add_family_member_nonce' ); ?>
        <div class="modal-header">
          <h5 class="modal-title" id="addFamilyMemberLabel">Add Family Member</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email (optional)</label>
            <input type="email" name="email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="">Select Role</option>
              <option value="parent">Parent</option>
              <option value="spouse">Spouse</option>
              <option value="child">Child</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Add Member</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="add-expense-form" method="post">
        <?php wp_nonce_field( 'exp_add_expense', 'exp_add_expense_nonce' ); ?>
        <div class="modal-header">
          <h5 class="modal-title">Add Expense</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assigned_to" class="form-select">
              <option value="">Self</option>
              <?php foreach ( $family_members as $member ) : ?>
                <option value="<?php echo esc_attr($member['name']); ?>"><?php echo esc_html($member['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <select name="payment_method" class="form-select">
              <option value="cash">Cash</option>
              <option value="credit_card">Credit Card</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Save Expense</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add Income Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="add-income-form" method="post">
        <?php wp_nonce_field( 'exp_add_income', 'exp_add_income_nonce' ); ?>
        <div class="modal-header">
          <h5 class="modal-title">Add Income</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" name="amount" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assigned_to" class="form-select">
              <option value="">Self</option>
              <?php foreach ( $family_members as $member ) : ?>
                <option value="<?php echo esc_attr($member['name']); ?>"><?php echo esc_html($member['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Comment</label>
            <textarea name="comment" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" name="date" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success w-100">Save Income</button>
        </div>
      </form>
    </div>
  </div>
</div>

</div>
<script>
jQuery(document).ready(function($) {

    $('#add-expense-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serializeArray();

        data.push({ name: 'action', value: 'exp_add_expense' });
        data.push({ name: 'nonce', value: exp_ajax.nonce_expense }); // Add nonce

        $.post(exp_ajax.ajaxurl, data, function(response) {
            if (response.success) {
                alert('Expense saved!');
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });

    $('#add-income-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serializeArray();

        data.push({ name: 'action', value: 'exp_add_income' });
        data.push({ name: 'nonce', value: exp_ajax.nonce_income }); // Add nonce

        $.post(exp_ajax.ajaxurl, data, function(response) {
            if (response.success) {
                alert('Income saved!');
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });

});


</script>

<?php get_footer(); ?>
