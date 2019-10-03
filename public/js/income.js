/* Add income: AJAX request */
$(document).ready(function() {
	
	const $form = $('#income_form');
	const $submitBtn = $("#submitBtn");
	const $info = $("#info_ribbon");
	$form.on("submit", function(e) {
		e.preventDefault();
		$submitBtn.prop('disabled', true);
		$.ajax({
			url: '/income/add-income-ajax',
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
				$submitBtn.prop('disabled', false);
			} else {
				location.reload(true);
			}
		}).fail(function() {
			$info.removeClass('alert-success');
			$info.addClass('alert-danger');
			$info.html('Błąd połączenia z bazą danych.');
			$submitBtn.prop('disabled', false);
		});
	});
	
});