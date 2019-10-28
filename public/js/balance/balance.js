$inc_btn = $('.income_category_button');
$exp_btn = $('.expense_category_button');

$inc_label=$('#incomeModalLabel');
$inc_id_label=$('#incomeModalIdLabel');
$inc_list=$('#incomeCategoryList');

$exp_label=$('#expenseModalLabel');
$exp_id_label=$('#expenseModalIdLabel');
$exp_list=$('#expenseCategoryList');
$balance=$('#balance_value');
$motivation=$('#b_motivation');

function loadPaymentCategories($payment_id) {
	let text = "";
	$.each($payments, function(){
		text += '<option name="payment" value="'+this['id']+'"';
		if (this['id']==$payment_id) text+= ' selected';
		text +='>'+this['name']+'</option>';
	});
	return text;
}

function reloadChartData(sums=[]) {
	chart.data=[];
	$.each(sums, function(){
		chart.data.push({"category": this['name'], "value": this['sum']});
	});
	chart.validateData();
}

function changeBg() {
	$(this).css("background-color", "#E0FFFF");
}

function updateMotivation(balance_value) {
	$balance.html(balance_value);
	if (balance_value>=0) {
		$motivation.html('<div class="inB"><span>Gratulacje. </span></div><div class="inB"><span>Świetnie zarządzasz finansami!</span></div>');
		$motivation.css('color','');
	} else {
		$motivation.html('<div class="inB"><span>Uważaj, </span></div><div class="inB"><span>wpadasz w długi!</span></div>');
		$motivation.css('color','red');
	}
}

function editIncomeRecord() {
	$form = $(this).closest('form');
	$.ajax({
		url: '/balance/update-income-record-ajax',
		method : "POST",
		dataType : "json",
		data: $form.serialize()
	}).done(function(response) {
		$form.next('div.error').empty().attr('hidden', true);
		if(!response.success) {
			$form.next('div.error').attr('hidden', false);
			$.each(response.errors, function(){
				$form.next('div.error').append('<p>'+this+'</p>');
			});
		} else {
			$('#income'+$inc_id_label.val()+'Sum').html(response.new_sum);
			$form.find('input').css("background-color", "");
			updateMotivation(parseFloat(response.balance).toFixed(2));
		}
	}).fail(function() {
		alert("Income record editing fail.");
	});
}

function deleteIncomeRecord() {
	$form = $(this).closest('form');
	$.ajax({
		url: '/balance/delete-income-record-ajax',
		method : "POST",
		dataType : "json",
		data: $form.serialize()
	}).done(function(response) {
		$form.next('div.error').empty().attr('hidden', true);
		if(!response.success) {
			$form.next('div.error').attr('hidden', false);
			$form.next('div.error').append('<p>Nie udało się usunąć dochodu.</p>');
		} else {
			$('#income'+$inc_id_label.val()+'Sum').html(response.new_sum);
			$form.closest('li').remove();
			updateMotivation(parseFloat(response.balance).toFixed(2));
			if(!$inc_list.children().length) {
				$('#income'+$inc_id_label.val()+'Line').remove();
				$('#incomeListModal').modal('toggle');
			}
		}
	}).fail(function() {
		alert("Income record deleting fail.");
	});
}

function editExpenseRecord() {
	$form = $(this).closest('form');
	$.ajax({
		url: '/balance/update-expense-record-ajax',
		method : "POST",
		dataType : "json",
		data: $form.serialize()
	}).done(function(response) {
		console.log(response);
		$form.next('div.error').empty().attr('hidden', true);
		if(!response.success) {
			$form.next('div.error').attr('hidden', false);
			$.each(response.errors, function(){
				$form.next('div.error').append('<p>'+this+'</p>');
			});
		} else {
			$('#expense'+$exp_id_label.val()+'Sum').html(response.new_sum);
			reloadChartData(response.all_sums);
			$form.find('input').css("background-color", "");
			$form.find('select').css("background-color", "");
			updateMotivation(parseFloat(response.balance).toFixed(2));
		}
	}).fail(function() {
		alert("Expense record editing fail.");
	});
}

function deleteExpenseRecord() {
	$form = $(this).closest('form');
	$.ajax({
		url: '/balance/delete-expense-record-ajax',
		method : "POST",
		dataType : "json",
		data: $form.serialize()
	}).done(function(response) {
		$form.next('div.error').empty().attr('hidden', true);
		if(!response.success) {
			$form.next('div.error').attr('hidden', false);
			$form.next('div.error').append('<p>Nie udało się usunąć wydatku.</p>');
		} else {
			$('#expense'+$exp_id_label.val()+'Sum').html(response.new_sum);
			$form.closest('li').remove();
			updateMotivation(parseFloat(response.balance).toFixed(2));
			reloadChartData(response.all_sums);
			if(!$exp_list.children().length) {
				$('#expense'+$exp_id_label.val()+'Line').remove();
				$('#expenseListModal').modal('toggle');
			}
		}
	}).fail(function() {
		alert("Expense record deleting fail.");
	});
}

function loadIncomeRecord(record) { $inc_list.append($('<li class="container">').append(
	$('<form class="modal_line row shadow">').append(
		$('<input type="hidden" name="income_id" value="" />').attr('value', record['id']),
		$('<input type="hidden" name="category_id" value="" />').attr('value', $inc_id_label.val()),
		$('<input type="number" name="amount" class="modal_cell col-6 col-sm-6 col-lg-2" step="0.01" value="" min="0.01">').attr('value', record['amount']).on('change', changeBg),
		$('<input type="date" name="income_date" class="modal_cell col-6 col-sm-6 col-lg-3" value="">').attr('value', record['date_of_income']).on('change', changeBg),		
		$('<input type="text" name="comment" class="modal_cell col-8 col-sm-9 col-lg-5" value="">').attr('value', record['income_comment']).attr('placeholder','Notatki...').on('change', changeBg),	
		$('<div class="container modal_cell col-4 col-sm-3 col-lg-2" style="padding: 0;">').append(
			$('<button type="button" class="btn_record modal_button edit_record_button">').on('click', editIncomeRecord).html('<i class="fa fa-floppy-o fa-fw"></i>'),
			$('<button type="button" class="btn_record bg_del modal_button delete_record_button">').on('click', deleteIncomeRecord).html('<i class="fa fa-trash fa-fw"></i>'))),
	$('<div class="error bot10" hidden>')
))};

function loadExpenseRecord(record) { $exp_list.append($('<li class="container">').append(
	$('<form class="modal_line row shadow">').append(
		$('<input type="hidden" name="expense_id" value="" />').attr('value', record['id']),
		$('<input type="hidden" name="category_id" value="" />').attr('value', $exp_id_label.val()),
		$('<input type="number" name="amount" class="modal_cell col-12 col-sm-4 col-lg-2" step="0.01" value="" min="0.01">').attr('value', record['amount']).on('change', changeBg),
		$('<input type="date" name="expense_date" class="modal_cell col-6 col-sm-4 col-lg-3" value="">').attr('value', record['date_of_expense']).on('change', changeBg),
		$('<select name="payment" class="modal_cell col-6 col-sm-4 col-lg-2">').append(loadPaymentCategories(record['payment_method_assigned_to_user_id'])).on('change', changeBg),	
		$('<input type="text" name="comment" class="modal_cell col-8 col-lg-3" value="">').attr('value', record['expense_comment']).attr('placeholder','Notatki...').on('change', changeBg),	
		$('<div class="container modal_cell col-4 col-lg-2" style="padding: 0;">').append(
			$('<button type="button" class="btn_record modal_button edit_record_button">').on('click', editExpenseRecord).html('<i class="fa fa-floppy-o fa-fw"></i>'),
			$('<button type="button" class="btn_record bg_del modal_button delete_record_button">').on('click', deleteExpenseRecord).html('<i class="fa fa-trash fa-fw"></i>'))),
	$('<div class="error bot10" hidden>')
))};

/* Load income records for category in period time. */
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
			loadIncomeRecord(this);
		});
	}).fail(function() {
		alert("Income records loading fail.");
	});
});

/* Load expense records for category in period time. */
$exp_btn.on('click' , function(e) {
	e.preventDefault();
	$exp_label.html($(this).attr('data-category-name'));
	$exp_id_label.val($(this).attr('data-category-id'));
	$.ajax({
		url: '/balance/get-expense-records-ajax',
		method : "POST",
		dataType : "json",
		data: {
			"category_id" : $(this).attr("data-category-id")
		}
	}).done(function(response) {
		$exp_list.empty();
		$.each(response, function(){
			loadExpenseRecord(this);
		});
	}).fail(function() {
		alert("Expense records loading fail.");
	});
});
