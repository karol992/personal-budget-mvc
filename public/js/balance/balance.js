$inc_btn = $('.income_category_button');
$exp_btn = $('.expense_category_button');
$inc_label=$('#incomeModalLabel');
$exp_label=$('#expenseModalLabel');

$inc_btn.on('click' , function() {
	$inc_label.html($(this).attr('data-category-name'));
});
$exp_btn.on('click' , function() {
	$exp_label.html($(this).attr('data-category-name'));
});
