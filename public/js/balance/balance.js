$inc_btn = $('.income_category_button');
$exp_btn = $('.expense_category_button');
$inc_label=$('#incomeModalLabel');
$exp_label=$('#expenseModalLabel');
$inc_list=$('#incomeCategoryList');

function addIncomeRecord(id, amount, date, comment) { $inc_list.append($('<li class="container">').append(
	$('<form class="modal_line row shadow">').append(
		$('<input type="hidden" name="income_id" value="" />').attr('value', id),
		$('<input type="number" name="amount" class="modal_cell col-6 col-sm-6 col-lg-2" step="0.01" value="" min="0.01">').attr('value', amount),
		$('<input type="date" name="income_date" class="modal_cell col-6 col-sm-6 col-lg-3" value="">').attr('value', date),		
		$('<input type="text" name="comment" class="modal_cell col-8 col-sm-9 col-lg-5" value="">').attr('value', comment).attr('placeholder','Notatki...').on("focus", function(){this.attr('placeholder','Notatki...');}).on("blur", function(){this.attr('placeholder','Notatki...')}),		
		$('<div class="container modal_cell col-4 col-sm-3 col-lg-2" style="padding: 0;">').append(
			$('<button name="action" value="update" type="submit" class="btn_record modal_button edit_record_button">').html('<i class="fa fa-floppy-o fa-fw"></i>'),
			$('<button name="action" value="delete" type="submit" class="btn_record bg_del modal_button delete_record_button">').html('<i class="fa fa-trash fa-fw"></i>')
))))};

$inc_btn.on('click' , function(e) {
	e.preventDefault();
	$inc_label.html($(this).attr('data-category-name'));
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
			addIncomeRecord(this['id'],this['amount'],this['date_of_income'],this['income_comment']);
		});
	}).fail(function() {
		alert("income loading fail");
	});
	
});
$exp_btn.on('click' , function(e) {
	e.preventDefault();
	$exp_label.html($(this).attr('data-category-name'));
});
