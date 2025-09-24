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
$selected_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$selected_year  = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$transactions   = Expenses::get_user_transactions(null, $selected_month, $selected_year);
$family_totals  = Expenses::get_family_totals(null, $selected_month, $selected_year);

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
global $wpdb;
$table = $wpdb->prefix . 'exp_family_members';
$family_members  = $wpdb->get_results(
            $wpdb->prepare("SELECT id, name, role FROM $table WHERE user_id = %d", $current_user_id),
            ARRAY_A
        );
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

<hr class="my-4">

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <h4 class="fw-bold mb-3">👨‍👩‍👧 Family Members</h4>

        <!-- Family Members List -->
        <div id="family-members-list">
            <p class="text-muted">Loading family members...</p>
        </div>

        <!-- Add Member Form -->
        <form id="add-family-member-form" class="mt-3">
            <?php wp_nonce_field('exp_family_nonce', 'family_nonce'); ?>
            <div class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" placeholder="Name" required>
                </div>
                <div class="col-md-4">
                    <input type="email" name="email" class="form-control" placeholder="Email (optional)">
                </div>
                <div class="col-md-3">
                    <select name="role" class="form-select" required>
                        <option value="">Select Role</option>
                        <option value="parent">Parent</option>
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">+</button>
                </div>
            </div>
        </form>
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

<form method="get" class="mb-4">
    <label for="month">Month:</label>
    <select name="month" id="month">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?php echo $m; ?>" <?php selected($selected_month, $m); ?>>
                <?php echo date('F', mktime(0,0,0,$m,1)); ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="year">Year:</label>
    <select name="year" id="year">
        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
            <option value="<?php echo $y; ?>" <?php selected($selected_year, $y); ?>>
                <?php echo $y; ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">Filter</button>
</form>
<h3>All Transactions</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Category</th>
            <th>Payment Method</th>
            <th>Family Member</th>
            <th>Comment</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty( $transactions ) ) : ?>
            <?php foreach ( $transactions as $t ) : ?>
                <tr>
                    <td><?php echo esc_html( $t['date'] ); ?></td>
                    <td><?php echo ucfirst( esc_html( $t['type'] ) ); ?></td>
                    <td><?php echo esc_html( $t['amount'] ); ?></td>
                    <td><?php echo esc_html( $t['category'] ); ?></td>
                    <td><?php echo esc_html( $t['payment_method'] ); ?></td>
                    <td>
                        <?php 
                        if ( $t['family_member_id'] ) {
                            global $wpdb;
                            $fm_table = $wpdb->prefix . 'exp_family_members';
                            $name = $wpdb->get_var( $wpdb->prepare("SELECT name FROM $fm_table WHERE id = %d", $t['family_member_id']) );
                            echo esc_html( $name );
                        } else {
                            echo "—";
                        }
                        ?>
                    </td>
                    <td><?php echo esc_html( $t['comment'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7">No transactions yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<h3>Family Totals</h3>
<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>Family Member</th>
            <th>Role</th>
            <th>Total Income</th>
            <th>Total Expense</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php if ( ! empty( $family_totals ) ) : ?>
            <?php foreach ( $family_totals as $f ) : ?>
                <tr>
                    <td><?php echo esc_html( $f['name'] ); ?></td>
                    <td><?php echo esc_html( ucfirst($f['role']) ); ?></td>
                    <td><?php echo number_format( $f['total_income'], 2 ); ?></td>
                    <td><?php echo number_format( $f['total_expense'], 2 ); ?></td>
                    <td>
                        <?php 
                        $balance = $f['total_income'] - $f['total_expense'];
                        echo number_format( $balance, 2 );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="5">No family members added yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
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
                <option value="<?php echo esc_attr($member['id']); ?>"><?php echo esc_html($member['name']); ?></option>
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
                <option value="<?php echo esc_attr($member['id']); ?>"><?php echo esc_html($member['name']); ?></option>
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
<script>
// Load family members
function loadFamilyMembers() {
    jQuery.post(exp_ajax.ajaxurl, {
        action: 'exp_get_family_members',
        nonce: exp_ajax.nonce
    }, function(response) {

        // Try to parse response if it's a JSON string
        let members = response;
        if (typeof response === 'string') {
            try {
                members = JSON.parse(response);
            } catch (e) {
                console.error('Failed to parse family members response:', response);
                jQuery('#family-members-list').html('<p class="text-danger">Error loading family members.</p>');
                return;
            }
        }
        if (members && members.success && Array.isArray(members.data) && members.data.length) {
            let html = '<ul class="list-group">';
            members.data.forEach(member => {
          html += `
              <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
                <strong>${member.name}</strong> (${member.role})
                ${member.email ? `<small class="text-muted"> - ${member.email}</small>` : ''}
            </div>
            <button class="btn btn-sm btn-outline-danger delete-family" data-id="${member.id}">Remove</button>
              </li>
          `;
            });
            html += '</ul>';
            jQuery('#family-members-list').html(html);
        } else {
            jQuery('#family-members-list').html('<p class="text-muted">No family members yet.</p>');
        }
    });
}

// Add member form
jQuery('#add-family-member-form').on('submit', function(e) {
    e.preventDefault();
    let data = jQuery(this).serializeArray();
    data.push({ name: 'action', value: 'exp_add_family_member' });
    data.push({ name: 'nonce', value: exp_ajax.nonce });

    jQuery.post(exp_ajax.ajaxurl, data, function(response) {
        if (response.success) {
            loadFamilyMembers();
            jQuery('#add-family-member-form')[0].reset();
        } else {
            alert(response.data || 'Failed to add member');
        }
    });
});

// Delete member
jQuery(document).on('click', '.delete-family', function() {
    if (!confirm('Remove this family member?')) return;
    const id = jQuery(this).data('id');
    jQuery.post(exp_ajax.ajaxurl, {
        action: 'exp_delete_family_member',
        id: id,
        nonce: exp_ajax.nonce
    }, function(response) {
        if (response.success) {
            loadFamilyMembers();
        } else {
            alert(response.data || 'Failed to delete');
        }
    });
});
jQuery(document).ready(function() {
  // Set up exp_ajax.nonce if not already set
  if (typeof exp_ajax === 'undefined') {
    window.exp_ajax = { nonce: '<?php echo wp_create_nonce('exp_family_nonce'); ?>', ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>' };
  }
  loadFamilyMembers();
});
</script>


<?php get_footer(); ?>
