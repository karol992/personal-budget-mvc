$inc_btn = $('.income_category_button');
$exp_btn = $('.expense_category_button');

$inc_label=$('#incomeModalLabel');
$inc_id_label=$('#incomeModalIdLabel');
$inc_list=$('#incomeCategoryList');

$exp_label=$('#expenseModalLabel');

function editIncomeRecord() {
	$form = $(this).closest('form');
	$.ajax({
		url: '/balance/update-income-record-ajax',
		method : "POST",
		dataType : "json",
		data: $form.serialize()
	}).done(function(response) {
		console.log(response.new_sum);
		$form.next('div.error').empty().attr('hidden', true);
		if(!response.success) {
			$form.next('div.error').attr('hidden', false);
			$.each(response.errors, function(){
				$form.next('div.error').append('<p>'+this+'</p>');
			});
		} else {
			$('#income'+$inc_id_label.val()+'Sum').html(response.new_sum);
		}
	}).fail(function() {
		alert("income edit fail");
	});
	
}

function loadIncomeRecord(id, amount, date, comment) { $inc_list.append($('<li class="container">').append(
	$('<form class="modal_line row shadow">').append(
		$('<input type="hidden" name="income_id" value="" />').attr('value', id),
		$('<input type="hidden" name="category_id" value="" />').attr('value', $inc_id_label.val()),
		$('<input type="text" name="amount" class="modal_cell col-6 col-sm-6 col-lg-2" step="0.01" value="" min="0.01">').attr('value', amount),
		$('<input type="date" name="income_date" class="modal_cell col-6 col-sm-6 col-lg-3" value="">').attr('value', date),		
		$('<input type="text" name="comment" class="modal_cell col-8 col-sm-9 col-lg-5" value="">').attr('value', comment).attr('placeholder','Notatki...'),	
		$('<div class="container modal_cell col-4 col-sm-3 col-lg-2" style="padding: 0;">').append(
			$('<button type="button" class="btn_record modal_button edit_record_button">').on('click', editIncomeRecord).html('<i class="fa fa-floppy-o fa-fw"></i>'),
			$('<button type="button" class="btn_record bg_del modal_button delete_record_button">').html('<i class="fa fa-trash fa-fw"></i>'))),
	$('<div class="error bot10" hidden>')
))};

$inc_btn.on('click' , function(e) {
	e.preventDefault();
	$inc_label.html($(this).attr('data-category-name'));
	$inc_id_label.val($(this).attr('data-category-id'));
	$.ajax({
		url: '/balance/get-income-records-ajax',
		method : "POST",
		dataType : "json",
		data: {
			"category_id" : $(this).attr("data-category-id")
		}
	}).done(function(response) {
		$inc_list.empty();
		$.each(response, function(){
			loadIncomeRecord(this['id'],this['amount'],this['date_of_income'],this['income_comment']);
		});
	}).fail(function() {
		alert("income load fail");
	});
});

$exp_btn.on('click' , function(e) {
	e.preventDefault();
	$exp_label.html($(this).attr('data-category-name'));
});
