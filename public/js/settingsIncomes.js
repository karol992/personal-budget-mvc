/* onclick trash-button on the list of income categories */
$('.income_del').on('click', function() {
	var delValue = $(this).attr("value");
	var delName = $(this).attr("name");
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
			$('#deleteIncomeSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
		});
	}).fail(function() {
		alert("fail");
	});
});

/* onclick pencil-button on the list of income categories */
$('.income_edit').on('click', function() {
	var editValue = $(this).attr("value");
	var editName = $(this).attr("name");
	$('#editIncomeLabel').val(editName);
	$('#editIncomeId').val(editValue);
});

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
			$('#incomeCategoryList').append(
				$('<li id="income'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
					$('<div class="modal_cell col-12"  style="position: relative;">').append(
						$('<span id="income'+categoryId+'name">'+categoryName+'</span>'),
						$('<div class="btn-group vertical_center right">').append(
							$('<button type="button" class="btn btn_record income_edit" href="#incomeEditModal" data-toggle="modal" data-target="#incomeEditModal">').
								attr('value', categoryId).
								attr('name', categoryName).
								on('click', function() {
									var editValue = $(this).attr("value");
									var editName = $(this).attr("name");
									$('#editIncomeLabel').val(editName);
									$('#editIncomeId').val(editValue);
								}).
								append($('<i class="fa fa-pencil fa-fw">')),
							$('<button type="button" class="btn btn_record bg_record_del income_del" href="#incomeRemoveModal" data-toggle="modal" data-target="#incomeRemoveModal">').
								attr('value', categoryId).
								attr('name', categoryName).
								on('click', function() {
									var delValue = $(this).attr("value");
									var delName = $(this).attr("name");
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
											$('#deleteIncomeSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
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
			$('#income'+$('#editIncomeId').val()+'name').empty().html($('#editIncomeLabel').val());
			$('#income'+$('#editIncomeId').val()+'editBtn').attr('name', $('#editIncomeLabel').val());
			$('#income'+$('#editIncomeId').val()+'delBtn').attr('name', $('#editIncomeLabel').val());
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
