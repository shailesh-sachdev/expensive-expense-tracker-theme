  jQuery(document).ready(function($) {
    $('#add-expense-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serializeArray();
        data.push({name: 'action', value: 'exp_add_expense'}); // wp_ajax action
        $.post(ajaxurl, data, function(response) {
            if(response.success) {
                alert('Expense saved!');
                location.reload(); // or update dashboard dynamically
            } else {
                alert(response.data);
            }
        });
    });

    $('#add-income-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serializeArray();
        data.push({name: 'action', value: 'exp_add_income'});
        $.post(ajaxurl, data, function(response) {
            if(response.success) {
                alert('Income saved!');
                location.reload();
            } else {
                alert(response.data);
            }
        });
    });
});
