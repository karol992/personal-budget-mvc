/* 
Page content updates every single expense category modification (add new, edit, delete)in jQuery functions and executes its in AJAX request.
*/

$(document).ready(function() {
	
	const $addExpenseForm = $('#addExpenseCategory');
	const $expenseSubmitBtn = $("#addExpenseCategoryBtn");
	const $expenseInfo = $("#expenseCategoryInfo");
	const $expenseEditForm = $('#expenseEditForm');
	const $expenseEditBtn = $("#editExpenseBtn");
	const $expenseRemoveForm = $('#expenseRemoveForm');
	const $expenseRemoveBtn = $("#deleteExpenseBtn");

	/* Fill <select> in #expenseRemoveModal (AjAX request) */
	function fillDeleteExpenseSelect() {
		$button = $(this);
		let delValue = $button.attr("value");
		let delName = $button.attr("name");
		$('#deleteExpenseLabel').text(delName);
		$('#deleteExpenseId').val(delValue);
		$.ajax({
			url: '/settings/get-user-expense-cats-ajax',
			method : "POST",
		}).done(function(response) {
			let array = JSON.parse(response);
			$('#deleteExpenseSelect').empty();
			$('#deleteExpenseSelect').append('<option></option>');
			$.each(array, function(){
				if(this['id'] != $button.attr('value')) {
					$('#deleteExpenseSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
				}
			});
		}).fail(function() {
			alert("delete fail");
		});
	};

	/* Fill category properties in #expenseEditModal */
	function passExpenseCategory() {
		let editValue = $(this).attr("value");
		let editName = $(this).attr("name");
		let editLimited = $(this).attr("data-limited");
		let editLimitValue = $(this).attr("data-limit-value");
		$('#editExpenseLabel').val(editName);
		$('#editExpenseId').val(editValue);
		if (editLimited == "true") {
			$('#editExpenseLimited').prop("checked", true);
			$('#editExpenseLimitValue').prop("readonly", false).show();
			$('#editExpenseLimitEmpty').hide();
		} else {
			$('#editExpenseLimited').prop("checked", false);
			$('#editExpenseLimitValue').prop("readonly", true).hide();
			$('#editExpenseLimitEmpty').show();
		}
		$('#editExpenseLimitValue').val(editLimitValue);
	};

	/*	Onclick trash-button on the list of expense categories. */
	$('.expense_del').on('click', fillDeleteExpenseSelect);

	/* Onclick pencil-button on the list of expense categories */
	$('.expense_edit').on('click', passExpenseCategory);

	/* Append new expense category to #expenseToggleGroup*/
	function appendExpenseToList(categoryId, categoryName) {
		$('#expenseCategoryList').append(
			$('<li id="expense'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
				$('<div class="modal_cell col-12"  style="position: relative;">').append(
					$('<span id="expense'+categoryId+'name">'+categoryName+'</span>'),
					$('<span id="expense'+categoryId
					+'limit" style="display:none;"> (Limit: <span id="expense'+categoryId
					+'limitValue">0.00</span>)</span>'),
					$('<div class="btn-group vertical_center right">').append(
						$('<button id="expense'+categoryId+'editBtn" type="button" class="btn btn_record expense_edit" href="#expenseEditModal" data-toggle="modal" data-target="#expenseEditModal" data-limited="false" data-limit-value="0.00">').
							attr('value', categoryId).
							attr('name', categoryName).
							on('click', passExpenseCategory).
							append($('<i class="fa fa-pencil fa-fw">')),
						$('<button id="expense'+categoryId+'delBtn" type="button" class="btn btn_record bg_record_del expense_del" href="#expenseRemoveModal" data-toggle="modal" data-target="#expenseRemoveModal">').
							attr('value', categoryId).
							attr('name', categoryName).
							on('click', fillDeleteExpenseSelect).
							append($('<i class="fa fa-trash fa-fw">'))
	))))};

	/* New income-category: ajax-request to database and page-update */
	$addExpenseForm.on("submit", function(e) {
		e.preventDefault();
		$expenseSubmitBtn.prop('disabled', true);
		$expenseInfo.empty().hide();
		$.ajax({
			url: '/settings/add-expense-category-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
				if(response.success) {
				var categoryId = response.id;
				var categoryName = response.name;
				appendExpenseToList(categoryId, categoryName);
			} else {
				$expenseInfo.addClass('error').removeAttr('hidden').show().html(response.message);
			}
		}).fail(function() {
			$expenseInfo.addClass('error').removeAttr('hidden').show().html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$expenseSubmitBtn.prop('disabled', false);
			$('#addExpenseCategoryInput').val('');
		});
	});

	/* Change expense limit checkbox in #expenseEditModal */
	$('#editExpenseLimited').change(function() {
		$('#editExpenseLimitValue').prop("readonly", !$(this).prop('checked'));
		$('#editExpenseLimitValue').toggle();
		$('#editExpenseLimitEmpty').toggle();
	});

	/* Edit expense category: ajax-request to database and page-update */
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
				let editName = $('#editExpenseLabel').val();
				let editId = $('#editExpenseId').val();
				let editLimited = $('#editExpenseLimited').prop('checked');
				let editLimitValue =parseInt($('#editExpenseLimitValue').val()).toFixed(2);
				$('#expense'+editId+'name').html(editName);
				$('#expense'+editId+'editBtn').attr('name', editName);
				$('#expense'+editId+'editBtn').attr('data-limited', editLimited);
				$('#expense'+editId+'editBtn').attr('data-limit-value', editLimitValue);
				$('#expense'+editId+'delBtn').attr('name', editName);
				$('#expense'+editId+'limitValue').html(editLimitValue);
				if (editLimited) {
					$('#expense'+editId+'limit').show();
				} else {
					$('#expense'+editId+'limit').hide();
				}
			} else {
				$expenseInfo.removeAttr('hidden').addClass('error').html(response.message).show();
			}
		}).fail(function() {
			alert("edit fail");
		}).always(function() {
			$('#expenseEditModal').modal('toggle');
			$expenseEditBtn.prop('disabled', false);
		});
	});

	/* Delete expense category: ajax-request to database and page-update */
	$expenseRemoveForm.on("submit", function(e) {
		e.preventDefault();
		$expenseRemoveBtn.prop('disabled', true);
		$expenseInfo.removeAttr('hidden').removeClass('error').empty().show();
		$.ajax({
			url: '/settings/remove-expense-category-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				$('#expense'+response.deleteId+'Record').remove();
				$expenseInfo.html(response.message);
			} else {
				$expenseInfo.addClass('error').html(response.message);
			}
		}).fail(function() {
			$expenseInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#expenseRemoveModal').modal('toggle');
			$expenseRemoveBtn.prop('disabled', false);
		});
	});
	
});