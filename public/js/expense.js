/* Add expense: AJAX request, limit information */
$(document).ready(function() {
	const $form = $('#expense_form');
	const $submitBtn = $("#submitBtn");
	const $info = $("#info_ribbon");
	const $expenseValue = $("#expense_value");
	const $limit = $("#limit_ribbon");
	const $category = $("input[name='expense_category']");
	const $dateInput = $('#expense_date');
	
	function alertInfo(text) {
		$info.removeClass('alert-success');
		$info.addClass('alert-danger');
		$info.html(text);
	}
	
	function successInfo(text) {
		$info.removeClass('alert-danger');
		$info.addClass('alert-success');
		$info.html(text);
	}
	
	function infoText(name, limit_value, beforeSum, afterSum) {
		let message = '<div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;">'+name+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;">Limit: '+limit_value+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;"> W miesiącu: '+beforeSum+'</div><div class="col-md-3 col-sm-6 col-12" style="float: left; text-align: left;"> Łącznie: '+afterSum+'</div>';
		return message;
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
	
	function validateValueWithInfo(input_value) {
		if (input_value != '') {
			if (input_value.length > 9) {
				alertInfo("Za długa wartości wydatku.");
				$expenseValue.val("");
				return false;
			} else if ((input_value <= 0) || (input_value.match(/^0+[,.]0+$/) != null)){
				alertInfo(input_value+' (Wartość wydatku musi być większa od zera.)');
				return false;
			} else if (input_value >= 1000000){
				alertInfo(input_value+' (Wartość wydatku musi być mniejsza niż milion.)');
				return false;
			} else if ((input_value.match(/^\d{1,6}[,.]?\d{0,2}$/) != null) 
							&& (input_value.match(/^\d{1,6}[,.]$/) == null)) {
				return true;
			}  else {
				alertInfo(input_value+'<br/> (Niepoprawny format wartości wydatku.)');
				return false;
			}
		}
		alertInfo("Nie podano wartości wydatku.");
		return false;
	}
	
	function validateValue(input_value) {
		console.log('validateValue');
		if (input_value===0) return true;
		if (input_value != '') {
			if (input_value.length > 9) {
				return false;
			} else if ((input_value < 0) || (input_value.match(/^0+[,.]0+$/) != null)){
				return false;
			} else if (input_value >= 1000000){
				return false;
			} else if ((input_value.match(/^\d{1,6}[,.]?\d{0,2}$/) != null) 
							&& (input_value.match(/^\d{1,6}[,.]$/) == null)) {
				return true;
			}  else {
				return false;
			}
		}
		return false;
	}
	
	function ajaxGetLimit() {
		;
	}
	
	function updateInfo($valueInput, $checked) {
		let amount = $valueInput.val();
		if (!amount) amount=0;
		console.log("val: "+amount);
		if(validateValue(amount)) {
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
				if (response.success) {
					let name = $checked.attr("data-name");
					let text = infoText(name,limit_value,response.beforeSum,response.afterSum);
					response.limit_reached ? alertInfo(text) : successInfo(text);
				} else {
					alertInfo(response.error);
				}
			}).fail(function() {
				alertInfo('Błąd połączenia z bazą danych.');
			});
		} else {
			$info.empty();
		}
	}

	/* Add expense (AJAX request) */
	$form.on("submit", function(e) {
		e.preventDefault();
		$submitBtn.prop('disabled', true);
		if(validateValueWithInfo($expenseValue.val())) {
			$.ajax({
				url: '/expense/add-expense-ajax',
				method : "POST",
				dataType : "json",
				data: $(this).serialize()
			}).done(function(response) {
				$(".alert-warning").hide();
				if(response.success) {
					successInfo(response.message);
					$(".alert-success").show();
				} else {
					location.reload(true);
				}
			}).fail(function() {
				alertInfo('Błąd połączenia z bazą danych.');
			});
		}
		$expenseValue.val("");
		setTimeout(function() {$submitBtn.prop('disabled', false);}, 1000);
		$expenseValue.focus();
	});
	
	$expenseValue.keyup(function() {
		$(this).prop('disabled', true);
		($(this).val().length>9) ? $(this).val($(this).val().slice(0, -1)) : false;
		$(this).val() ? showEstimation($(this)) : false;
		$(this).prop('disabled', false);
		$(this).focus();
	});
	
	$expenseValue.keydown(function() {
		($(this).val().length>10) ? $(this).val($(this).val().slice(0, -1)) : false;
	});
	
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
	
	updateInfo($expenseValue, $checked)
	
});

