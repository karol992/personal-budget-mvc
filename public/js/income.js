/* Add income: AJAX request */
$(document).ready(function() {
	
	const $form = $('#income_form');
	const $submitBtn = $("#submitBtn");
	const $info = $("#info_ribbon");
	const $incomeValue = $("#income_value");
	
	/* Show alert message */
	function alertInfo(text) {
		$info.hide().removeClass('alert-success').addClass('alert-danger').html(text).show("fast");
	}
	
	/* Show non-alert message */
	function successInfo(text) {
		$info.hide().removeClass('alert-danger').addClass('alert-success').html(text).show("fast");
	}
	
	/* Show income value syntax errors. */
	function validateValueWithInfo(input_value) {
		if (input_value != '') {
			if (input_value.length > 9) {
				alertInfo("Za długa wartości dochodu.");
				$incomeValue.val("");
				return false;
			} else if ((input_value <= 0) || (input_value.match(/^0+[,.]0+$/) != null)){
				alertInfo(input_value+' (Wartość dochodu musi być większa od zera.)');
				return false;
			} else if (input_value >= 1000000){
				alertInfo(input_value+' (Wartość dochodu musi być mniejsza niż milion.)');
				return false;
			} else if ((input_value.match(/^\d{1,6}[,.]?\d{0,2}$/) != null) 
							&& (input_value.match(/^\d{1,6}[,.]$/) == null)) {
				return true;
			}  else {
				alertInfo(input_value+'<br/> (Niepoprawny format wartości dochodu.)');
				return false;
			}
		}
		alertInfo("Nie podano wartości dochodu.");
		return false;
	}
	
	$form.on("submit", function(e) {
		e.preventDefault();
		$submitBtn.prop('disabled', true);
		$incomeValue.attr('readonly', true);
		if(validateValueWithInfo($incomeValue.val())) {
			$.ajax({
				url: '/income/add-income-ajax',
				method : "POST",
				dataType : "json",
				data: $(this).serialize()
			}).done(function(response) {
				$(".alert-warning").hide();
				if(response.success) {
					successInfo(response.message);
				} else {
					location.reload(true);
				}
			}).fail(function() {
				alertInfo('Błąd połączenia z bazą danych.');
			});
		}
		$incomeValue.val('');
		setTimeout(function() {
			$submitBtn.prop('disabled', false);
			$incomeValue.attr('readonly', false);
		}, 1000);
		$incomeValue.focus();
	});
	
	$incomeValue.keyup(function() {
		$(this).prop('disabled', true);
		($(this).val().length>9) ? $(this).val($(this).val().slice(0, -1)) : false;
		$(this).prop('disabled', false);
		$(this).focus();
	});
	
	$incomeValue.on("change", function() {
		$(this).prop('disabled', true);
		($(this).val().length>9) ? $(this).val($(this).val().slice(0, -1)) : false;
		$(this).prop('disabled', false);
		$(this).focus();
	});
	
	$incomeValue.keydown(function() {
		($(this).val().length>10) ? $(this).val($(this).val().slice(0, -1)) : false; 
	});
	
	$incomeValue.focus();
});