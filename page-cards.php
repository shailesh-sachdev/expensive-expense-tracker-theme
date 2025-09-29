<?php
/* Template Name: Cards */
get_header();

use Expensive\Cards;

if ( ! is_user_logged_in() ) {
    wp_redirect( site_url('/login') );
    exit;
}

$cards_class = Cards::get_instance();
$user_cards  = $cards_class->get_cards();

// Fetch family members for assignment dropdown
global $wpdb;
$fm_table = $wpdb->prefix . 'exp_family_members';
$family_members = $wpdb->get_results(
    $wpdb->prepare("SELECT id, name FROM $fm_table WHERE user_id = %d", get_current_user_id()),
    ARRAY_A
);
?>

<div class="container py-4">
    <h2 class="mb-4">💳 Credit Cards</h2>

    <!-- Quick Action Buttons -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <button class="btn btn-outline-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#addCardModal">
                + Add Credit Card
            </button>
        </div>
        <div class="col-md-3">
            <button class="btn btn-outline-warning w-100 py-3" data-bs-toggle="modal" data-bs-target="#uploadPDFModal">
                Upload Statement
            </button>
        </div>
    </div>

    <!-- Cards List -->
    <div class="row g-3">
        <?php if (!empty($user_cards)): ?>
            <?php foreach ($user_cards as $card): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?php echo esc_html($card['card_name']); ?></strong><br>
                                <small class="text-muted">Bill: <?php echo esc_html($card['billing_date']); ?> | Due: <?php echo esc_html($card['due_date']); ?></small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger delete-card" data-id="<?php echo esc_attr($card['id']); ?>">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="card shadow-sm border-0 p-3 text-center text-muted">
                    No credit cards added yet.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Card Modal -->
<div class="modal fade" id="addCardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="add-card-form">
                <?php wp_nonce_field('exp_card_nonce', 'exp_card_nonce_field'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add Credit Card</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Card Name</label>
                        <input type="text" name="card_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last 4 Digits</label>
                        <input type="text" name="last_digits" class="form-control" maxlength="4" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bill Date (1-31)</label>
                        <input type="number" name="billing_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Due Date (1-31)</label>
                        <input type="number" name="due_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Card Limit</label>
                        <input type="number" name="card_limit" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Save Card</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload PDF Modal -->
<div class="modal fade" id="uploadPDFModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="upload-statement-form" enctype="multipart/form-data">
                <?php wp_nonce_field('exp_statement_nonce', 'exp_statement_nonce_field'); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Upload Credit Card Statement (PDF)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Select Credit Card</label>
                        <select name="card_id" class="form-select" required>
                            <option value="">-- Select Card --</option>
                            <?php
                            // Fetch user's credit cards from wp_exp_credit_cards
                            $cc_table = $wpdb->prefix . 'exp_credit_cards';
                            $user_id = get_current_user_id();
                            $credit_cards = $wpdb->get_results(
                                $wpdb->prepare("SELECT id, card_name, last_digits FROM $cc_table WHERE user_id = %d", $user_id),
                                ARRAY_A
                            );
                            foreach ($credit_cards as $cc) {
                                echo '<option value="' . esc_attr($cc['id']) . '">' . esc_html($cc['card_name']) . ' (****' . esc_html($cc['last_digits']) . ')</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <input type="file" name="pdf_file" class="form-control mb-3" accept=".pdf" required>
                    <input type="password" name="pdf_password" class="form-control" placeholder="Enter PDF password" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning w-100">Upload & Parse</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transactions Assignment Modal -->
<div class="modal fade" id="transactionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="assign-transactions-form">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Transactions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>To</th>
                                <th>Assign To</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-body">
                            <!-- Filled dynamically via AJAX -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success w-100">Save Transactions</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($){
    // Upload Statement
    $('#upload-statement-form').on('submit', function(e){
        e.preventDefault();
        var formData = new FormData(this);
        formData.append('action', 'exp_upload_statement');

        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                if(response.success){
                    console.log("success on "+ response.data);
                    var rows = '';
                    $.each(response.data, function(i, txn){
                        rows += `
                            <tr>
                                <td>${txn.date}</td>
                                <td>${txn.amount}</td>
                                <td>${txn.to}</td>
                                <td>
                                    <select name="assign[${i}]" class="form-select">
                                        <option value="">-- Select --</option>
                                        <option value="0">Self</option>
                                        <?php foreach($family_members as $fm): ?>
                                            <option value="<?php echo esc_attr($fm['id']); ?>"><?php echo esc_html($fm['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="txn[${i}][date]" value="${txn.date}">
                                    <input type="hidden" name="txn[${i}][amount]" value="${txn.amount}">
                                    <input type="hidden" name="txn[${i}][to]" value="${txn.to}">
                                </td>
                            </tr>
                        `;
                    });
                    $('#transactions-body').html(rows);
                    $('#uploadPDFModal').modal('hide');
                    $('#transactionsModal').modal('show');
                } else {
                    alert(response.data || 'Failed to parse statement');
                }
            }
        });
    });
// $('#transactionsModal').on('shown.bs.modal', function () {
//     $(this).find('button, input, select').filter(':visible:first').focus();
// });

    // Save Assigned Transactions
    $('#assign-transactions-form').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({name:'action', value:'exp_save_transactions'});

        $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response){
            if(response.success){
                alert('Transactions saved successfully!');
                location.reload();
            } else {
                alert(response.data || 'Failed to save transactions');
            }
        });
    });

    // Add Card
    $('#add-card-form').on('submit', function(e){
        e.preventDefault();
        var data = $(this).serializeArray();
        data.push({name:'action', value:'exp_add_card'});
        data.push({name:'nonce', value: $('#exp_card_nonce_field').val() });

        $.post('<?php echo admin_url("admin-ajax.php"); ?>', data, function(response){
            if(response.success){
                location.reload();
            } else {
                alert(response.data || 'Failed to add card');
            }
        });
    });

    // Delete Card
    $('.delete-card').on('click', function(){
        if(!confirm('Delete this card?')) return;
        var btn = $(this);
        var id = btn.data('id');

        $.post('<?php echo admin_url("admin-ajax.php"); ?>', {
            action: 'exp_delete_card',
            nonce: '<?php echo wp_create_nonce("exp_card_nonce"); ?>',
            id: id
        }, function(response){
            if(response.success){
                btn.closest('.col-md-4').remove();
            } else {
                alert(response.data || 'Delete failed');
            }
        });
    });
});
</script>

<?php get_footer(); ?>
