/* 
Page content updates every single expesne category modification (add new, edit, delete)in jQuery functions and executes its in AJAX request.
*/

/*	Onclick trash-button on the list of income categories. 
	Load 
*/
/*$('.income_del').on('click', function() {
	$button = $(this);
	var delValue = $button.attr("value");
	var delName = $button.attr("name");
	$('#deleteIncomeLabel').text(delName);
	$('#deleteIncomeId').val(delValue);
	$.ajax({
		url: '/settings/get-user-income-cats-ajax',
		method : "POST",
	}).done(function(response) {
		var array = JSON.parse(response);
		$('#deleteIncomeSelect').empty();
		$('#deleteIncomeSelect').append('<option></option>');
		$.each(array, function(){
			if(this['id'] != $button.attr('value')) {
				$('#deleteIncomeSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
			}
		});
	}).fail(function() {
		alert("fail");
	});
});*/

/* onclick pencil-button on the list of income categories */
$('.expense_edit').on('click', function() {
	var editValue = $(this).attr("value");
	var editName = $(this).attr("name");
	var editLimited = $(this).attr("data-limited");
	var editLimitValue = $(this).attr("data-limit-value");
	$('#editExpenseLabel').val(editName);
	$('#editExpenseId').val(editValue);
	if (editLimited == 1) {
		$('#editExpenseLimited').prop("checked", true);
		$('#editExpenseLimitValue').prop("readonly", false);
	} else {
		$('#editExpenseLimited').prop("checked", false);
		$('#editExpenseLimitValue').prop("readonly", true);
	}
	$('#editExpenseLimitValue').val(editLimitValue);
});


$('#editExpenseLimited').change(function() {
	$('#editExpenseLimitValue').prop("readonly", !$(this).prop('checked'));
});


/* new expense-category: ajax-request to database and page-update */
const $addExpenseForm = $('#addExpenseCategory');
const $expenseSubmitBtn = $("#addExpenseCategoryBtn");
const $expenseInfo = $("#expenseCategoryInfo");
/*$addIncomeForm.on("submit", function(e) {
    e.preventDefault();
    $incomeSubmitBtn.prop('disabled', true);
	$incomeInfo.empty().hide();
	$.ajax({
        url: '/settings/add-income-category-ajax',
        method : "POST",
        dataType : "json",
        data: $(this).serialize()
    }).done(function(response) {
			if(response.success) {
			var categoryId = response.id;
			var categoryName = response.name;
			$('#incomeCategoryList').append(
				$('<li id="income'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
					$('<div class="modal_cell col-12"  style="position: relative;">').append(
						$('<span id="income'+categoryId+'name">'+categoryName+'</span>'),
						$('<div class="btn-group vertical_center right">').append(
							$('<button id="income'+categoryId+'editBtn" type="button" class="btn btn_record income_edit" href="#incomeEditModal" data-toggle="modal" data-target="#incomeEditModal">').
								attr('value', categoryId).
								attr('name', categoryName).
								on('click', function() {
									var editValue = $(this).attr("value");
									var editName = $(this).attr("name");
									$('#editIncomeLabel').val(editName);
									$('#editIncomeId').val(editValue);
								}).
								append($('<i class="fa fa-pencil fa-fw">')),
							$('<button id="income'+categoryId+'delBtn" type="button" class="btn btn_record bg_record_del income_del" href="#incomeRemoveModal" data-toggle="modal" data-target="#incomeRemoveModal">').
								attr('value', categoryId).
								attr('name', categoryName).
								on('click', function() {
									$button = $(this);
									var delValue = $button.attr("value");
									var delName = $button.attr("name");
									$('#deleteIncomeLabel').text(delName);
									$('#deleteIncomeId').val(delValue);
									$.ajax({
										url: '/settings/get-user-income-cats-ajax',
										method : "POST",
									}).done(function(response) {
										var array = JSON.parse(response);
										$('#deleteIncomeSelect').empty();
										$('#deleteIncomeSelect').append('<option></option>');
										$.each(array, function(){
											if(this['id'] != $button.attr('value')) {
												$('#deleteIncomeSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
											}
										});
									}).fail(function() {
										alert("fail");
									});
								}).
								append($('<i class="fa fa-trash fa-fw">'))
			))));
		} else {
			$incomeInfo.addClass('error').removeAttr('hidden').show().html(response.message);
		}
    }).fail(function() {
		$incomeInfo.addClass('error').removeAttr('hidden').show().html('Błąd połączenia z bazą danych.');
	}).always(function() {
		$incomeSubmitBtn.prop('disabled', false);
		$('#addIncomeCategoryInput').val('');
	});
});
*/
/* edit income category: ajax-request to database and page-update */
const $expenseEditForm = $('#expenseEditForm');
const $expenseEditBtn = $("#editExpenseBtn");
$expenseEditForm.on("submit", function(e) {
	e.preventDefault();
    $expenseEditBtn.prop('disabled', true);
	$expenseInfo.empty().hide();
	$.ajax({
		url: '/settings/edit-expense-category-ajax',
		method : "POST",
		dataType : "json",
		data: $(this).serialize({ checkboxesAsBools: true })
	}).done(function(response) {
		if(response.success) {
			var editName = $('#editExpenseLabel').val();
			var editId = $('#editExpenseId').val();
			$('#expense'+editId+'name').empty().html(editName);
			$('#expense'+editId+'editBtn').attr('name', editName);
			$('#expense'+editId+'delBtn').attr('name', editName);
		} else {
			$expenseInfo.removeAttr('hidden').addClass('error').empty().show();
			$expenseInfo.html(response.message);
		}
	}).fail(function() {
		alert("edit fail");
	}).always(function() {
		$('#expenseEditModal').modal('toggle');
		$expenseEditBtn.prop('disabled', false);
	});
});

/* delete income category: ajax-request to database and page-update */
/*const $incomeRemoveForm = $('#incomeRemoveForm');
const $incomeRemoveBtn = $("#deleteIncomeBtn");
$incomeRemoveForm.on("submit", function(e) {
	e.preventDefault();
    $incomeRemoveBtn.prop('disabled', true);
	$incomeInfo.removeAttr('hidden').removeClass('error').empty().show();
	$.ajax({
		url: '/settings/remove-income-category-ajax',
		method : "POST",
		dataType : "json",
		data: $(this).serialize()
	}).done(function(response) {
		if(response.success) {
			$('#income'+response.deleteId+'Record').remove();
			$incomeInfo.html(response.message);
		} else {
			$incomeInfo.addClass('error').html(response.message);
		}
	}).fail(function() {
		$incomeInfo.addClass('error').html('Błąd połączenia z bazą danych.');
	}).always(function() {
		$('#incomeRemoveModal').modal('toggle');
		$incomeRemoveBtn.prop('disabled', false);
	});
});
*/