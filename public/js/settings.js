
$('button.js_button').click(function(){
  $(this).next('div.js_toggle_group').toggle("slow");
});

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

$('.income_edit').on('click', function() {
	var editValue = $(this).attr("value");
	var editName = $(this).attr("name");
	$('#editIncomeLabel').val(editName);
	$('#editIncomeId').val(editValue);
});

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
			var limit = 0.00;
			$('#incomeCategoryList').append(
				$('<li id="income'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
					$('<div class="modal_cell col-12"  style="position: relative;">').append(
						$('<span>').append('<span>'+categoryName+' (Limit: <span id="income'+categoryId+'limit">'+limit.toPrecision(3)+'</span>)</span>'),
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

 //For the future
$('button.to_first').click(function(){
  $(this).closest('ul').prepend($(this).closest('li'));
});
$('.upbutton').on('click', function () {
    var previousObject = $(this).closest('li').prev('li');
	$(this).closest('li').insertBefore(previousObject);
});
$('.downbutton').on('click', function () {
    var nextObject = $(this).closest('li').next('li');
	$(this).closest('li').insertAfter(nextObject);
});

