/* Add expense: AJAX request, limit information */
$(document).ready(function() {
	const $form = $('#expense_form');
	const $submitBtn = $("#submitBtn");
	const $info = $("#info_ribbon");
	const $expenseValue = $("#expense_value");
	const $limit = $("#limit_ribbon");
	const $category = $("input[name='expense_category']");
	const $dateInput = $('#expense_date');
	
	function infoText(name, limit_value, beforeSum, afterSum) {
		let message = '<div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;">'+name+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;">Limit: '+limit_value+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;"> Teraz: '+beforeSum+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;"> Łącznie: '+afterSum+'</div>';
		return message;
	}
	
	function updateInfo($valueInput, $checked) {
		let amount = $valueInput.val();
		let category_id = $checked.val();
		let limit_value = $checked.attr("data-limit-value");
		let date = $dateInput.val();
		$.ajax({
			url: '/expense/get-perioded-sum-ajax',
			method : "POST",
			dataType : "json",
			data: {
				"cat_id" : category_id,
				"date" : date,
				"amount" : amount
			}
		}).done(function(response) {
			console.log(response.success);
			if (response.success) {
				if(response.limit_reached) {
					$info.removeClass('alert-success');
					$info.addClass('alert-danger');
				} else {
					$info.removeClass('alert-danger');
					$info.addClass('alert-success');
				}
				let name = $checked.attr("data-name");
				$info.html(infoText(name,limit_value,response.beforeSum,response.afterSum));
			} else {
				$info.removeClass('alert-success');
				$info.addClass('alert-danger');
				$info.html(response.error);
			}
		}).fail(function() {
			$info.removeClass('alert-success');
			$info.addClass('alert-danger');
			$info.html('Błąd połączenia z bazą danych.');
		});
	}
	
	function showEstimation($valueInput) {
		$checked = $("input[name='expense_category']:checked");
		let limited = $checked.attr("data-limited"); // 0 or 1
		if (limited==1) {
			updateInfo($valueInput, $checked);
		} else {
			$info.empty();
		}
	}
	
	/* Add expense (AJAX request) */
	$form.on("submit", function(e) {
		e.preventDefault();
		$submitBtn.prop('disabled', true);
		if($expenseValue.val()) {
			$.ajax({
				url: '/expense/add-expense-ajax',
				method : "POST",
				dataType : "json",
				data: $(this).serialize()
			}).done(function(response) {
				$(".alert-warning").hide();
				if(response.success) {
					$info.removeClass('alert-danger');
					$info.addClass('alert-success');
					$info.html(response.message);
					$(".alert-success").show();
					$expenseValue.empty();
					$expenseValue.focus();
				} else {
					location.reload(true);
				}
			}).fail(function() {
				$info.removeClass('alert-success');
				$info.addClass('alert-danger');
				$info.html('Błąd połączenia z bazą danych.');
			});
		}
		setTimeout(function() {$submitBtn.prop('disabled', false);}, 1000);
	});
	
	/* Prototype: show ribbon with data for limit request */
	
	
	$expenseValue.keyup(function() {
		$(this).prop('disabled', true);
		($(this).val().length>9) ? $(this).val($(this).val().slice(0, -1)) : false;
		$(this).val() ? showEstimation($(this)) : $info.empty();
		$(this).prop('disabled', false);
		$(this).focus();
	});
	//$expenseValue.keydown(amountEvents);
	
	$category.on("change", function() {
		$checked = $(this);
		let limited = $checked.attr("data-limited"); // 0 or 1
		if (limited==1) {
			updateInfo($expenseValue, $checked);
		} else {
			$info.empty();
		}
	});
	
	$dateInput.on("change", function() {
		showEstimation($expenseValue);
	});
});

