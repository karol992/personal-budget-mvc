/* 
Page content updates every single payment category modification (add new, edit, delete) in jQuery functions and executes its in AJAX request.
*/
$(document).ready(function() {
	
	const $addPaymentForm = $('#addPaymentCategory');
	const $paymentSubmitBtn = $("#addPaymentCategoryBtn");
	const $paymentInfo = $("#paymentCategoryInfo");
	const $paymentEditForm = $('#paymentEditForm');
	const $paymentEditBtn = $("#editPaymentBtn");
	const $paymentRemoveForm = $('#paymentRemoveForm');
	const $paymentRemoveBtn = $("#deletePaymentBtn");

	/* Fill <select> in #expenseRemoveModal (AjAX request) */
	function fillDeletePaymentSelect() {
		$button = $(this);
		let delValue = $button.attr("value");
		let delName = $button.attr("name");
		$('#deletePaymentLabel').text(delName);
		$('#deletePaymentId').val(delValue);
		$.ajax({
			url: '/settings/get-user-payment-cats-ajax',
			method : "POST",
		}).done(function(response) {
			let array = JSON.parse(response);
			$('#deletePaymentSelect').empty();
			$('#deletePaymentSelect').append('<option></option>');
			$.each(array, function(){
				if(this['id'] != $button.attr('value')) {
					$('#deletePaymentSelect').append('<option value="'+this['id']+'">'+this['name']+'</option>');
				}
			});
		}).fail(function() {
			alert("payment delete fail");
		});
	};

	/* Fill category properties in #paymentEditModal*/
	function passPaymentCategory() {
		let editValue = $(this).attr("value");
		let editName = $(this).attr("name");
		$('#editPaymentLabel').val(editName);
		$('#editPaymentId').val(editValue);
	};

	/*	Onclick trash-button on the list of payment categories. */
	$('.payment_del').on('click', fillDeletePaymentSelect);

	/* Onclick pencil-button on the list of payment categories */
	$('.payment_edit').on('click', passPaymentCategory);

	/* Append new payment category to #paymentToggleGroup*/
	function appendPaymentToList(categoryId, categoryName) {
		$('#paymentCategoryList').append(
			$('<li id="payment'+categoryId+'Record" class="modal_line modal_cell row shadow">').append(
				$('<div class="modal_cell col-12"  style="position: relative;">').append(
					$('<span id="payment'+categoryId+'name">'+categoryName+'</span>'),
					$('<div class="btn-group vertical_center right">').append(
						$('<button id="payment'+categoryId+'editBtn" type="button" class="btn btn_record payment_edit" href="#paymentEditModal" data-toggle="modal" data-target="#paymentEditModal">').
							attr('value', categoryId).
							attr('name', categoryName).
							on('click', passPaymentCategory).
							append($('<i class="fa fa-pencil fa-fw">')),
						$('<button id="payment'+categoryId+'delBtn" type="button" class="btn btn_record bg_record_del payment_del" href="#paymentRemoveModal" data-toggle="modal" data-target="#paymentRemoveModal">').
							attr('value', categoryId).
							attr('name', categoryName).
							on('click', fillDeletePaymentSelect).
							append($('<i class="fa fa-trash fa-fw">'))
	))))};

	/* New payment-category: ajax-request to database and page-update */
	$addPaymentForm.on("submit", function(e) {
		e.preventDefault();
		$paymentSubmitBtn.prop('disabled', true);
		$paymentInfo.empty().hide();
		$.ajax({
			url: '/settings/add-payment-category-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
				if(response.success) {
				var categoryId = response.id;
				var categoryName = response.name;
				appendPaymentToList(categoryId, categoryName);
			} else {
				$paymentInfo.addClass('error').removeAttr('hidden').show().html(response.message);
			}
		}).fail(function() {
			$paymentInfo.addClass('error').removeAttr('hidden').show().html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$paymentSubmitBtn.prop('disabled', false);
			$('#addPaymentCategoryInput').val('');
		});
	});

	/* Edit payment category: ajax-request to database and page-update */
	$paymentEditForm.on("submit", function(e) {
		e.preventDefault();
		$paymentEditBtn.prop('disabled', true);
		$paymentInfo.empty().hide();
		$.ajax({
			url: '/settings/edit-payment-category-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				var editName = $('#editPaymentLabel').val();
				var editId = $('#editPaymentId').val();
				$('#payment'+editId+'name').empty().html(editName);
				$('#payment'+editId+'editBtn').attr('name', editName);
				$('#payment'+editId+'delBtn').attr('name', editName);
			} else {
				$paymentInfo.removeAttr('hidden').addClass('error').empty().show();
				$paymentInfo.html(response.message);
			}
		}).fail(function() {
			paymentInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#paymentEditModal').modal('toggle');
			$paymentEditBtn.prop('disabled', false);
		});
	});

	/* Delete payment category: ajax-request to database and page-update */
	$paymentRemoveForm.on("submit", function(e) {
		e.preventDefault();
		$paymentRemoveBtn.prop('disabled', true);
		$paymentInfo.removeAttr('hidden').removeClass('error').empty().show();
		$.ajax({
			url: '/settings/remove-payment-category-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				$('#payment'+response.deleteId+'Record').remove();
				$paymentInfo.html(response.message);
			} else {
				$paymentInfo.addClass('error').html(response.message);
			}
		}).fail(function() {
			$paymentInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#paymentRemoveModal').modal('toggle');
			$paymentRemoveBtn.prop('disabled', false);
		});
	});
	
});