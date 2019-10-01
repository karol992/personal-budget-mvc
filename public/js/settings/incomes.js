/* 
Page content updates every single income category modification (add new, edit, delete) in jQuery functions and executes its in AJAX request.
*/

/* Fill <select> in #incomeRemoveModal (AjAX request) */
function fillDeleteIncomeSelect() {
	$button = $(this);
	let delValue = $button.attr("value");
	let delName = $button.attr("name");
	$('#deleteIncomeLabel').text(delName);
	$('#deleteIncomeId').val(delValue);
	$.ajax({
		url: '/settings/get-user-income-cats-ajax',
		method : "POST",
	}).done(function(response) {
		let array = JSON.parse(response);
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
};

/* Fill category properties in #incomeEditModal*/
function passIncomeCategory() {
	let editValue = $(this).attr("value");
	let editName = $(this).attr("name");
	$('#editIncomeLabel').val(editName);
	$('#editIncomeId').val(editValue);
};

/*	Onclick trash-button on the list of income categories. 
*/
$('.income_del').on('click', fillDeleteIncomeSelect);

/* Onclick pencil-button on the list of income categories */
$('.income_edit').on('click', passIncomeCategory);

/* Append new income category to #incomeToggleGroup*/
function appendIncomeToList(categoryId, categoryName) {
	$('#incomeCategoryList').append(
		$('<li id="income'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
			$('<div class="modal_cell col-12"  style="position: relative;">').append(
				$('<span id="income'+categoryId+'name">'+categoryName+'</span>'),
				$('<div class="btn-group vertical_center right">').append(
					$('<button id="income'+categoryId+'editBtn" type="button" class="btn btn_record income_edit" href="#incomeEditModal" data-toggle="modal" data-target="#incomeEditModal">').
						attr('value', categoryId).
						attr('name', categoryName).
						on('click', passIncomeCategory).
						append($('<i class="fa fa-pencil fa-fw">')),
					$('<button id="income'+categoryId+'delBtn" type="button" class="btn btn_record bg_record_del income_del" href="#incomeRemoveModal" data-toggle="modal" data-target="#incomeRemoveModal">').
						attr('value', categoryId).
						attr('name', categoryName).
						on('click', fillDeleteIncomeSelect).
						append($('<i class="fa fa-trash fa-fw">'))
))))};

/* new income-category: ajax-request to database and page-update */
const $addIncomeForm = $('#addIncomeCategory');
const $incomeSubmitBtn = $("#addIncomeCategoryBtn");
const $incomeInfo = $("#incomeCategoryInfo");
$addIncomeForm.on("submit", function(e) {
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
			appendIncomeToList(categoryId, categoryName);
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

/* edit income category: ajax-request to database and page-update */
const $incomeEditForm = $('#incomeEditForm');
const $incomeEditBtn = $("#editIncomeBtn");
$incomeEditForm.on("submit", function(e) {
	e.preventDefault();
    $incomeEditBtn.prop('disabled', true);
	$incomeInfo.empty().hide();
	$.ajax({
		url: '/settings/edit-income-category-ajax',
		method : "POST",
		dataType : "json",
		data: $(this).serialize()
	}).done(function(response) {
		if(response.success) {
			var editName = $('#editIncomeLabel').val();
			var editId = $('#editIncomeId').val();
			$('#income'+editId+'name').empty().html(editName);
			$('#income'+editId+'editBtn').attr('name', editName);
			$('#income'+editId+'delBtn').attr('name', editName);
		} else {
			$incomeInfo.removeAttr('hidden').addClass('error').empty().show();
			$incomeInfo.html(response.message);
		}
	}).fail(function() {
		alert("fail");
	}).always(function() {
		$('#incomeEditModal').modal('toggle');
		$incomeEditBtn.prop('disabled', false);
	});
});

/* delete income category: ajax-request to database and page-update */
const $incomeRemoveForm = $('#incomeRemoveForm');
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