/* Add expense: AJAX request, limit information */
$(document).ready(function() {
	
	const $form = $('#expense_form');
	const $submitBtn = $("#submitBtn");
	const $info = $("#info_ribbon");
	/* Add expense (AJAX request) */
	$form.on("submit", function(e) {
		e.preventDefault();
		$submitBtn.prop('disabled', true);
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
	
	/* Prototype: show ribbon with data for limit request */
	const $expenseValue = $("#expense_value");
	const $limit = $("#limit_ribbon");
	$expenseValue.on("change", function() {
		let value = $(this).val();
		let category_id = $("input[name='expense_category']:checked").val();
		$limit.addClass('alert-success');
		$limit.html("value:"+value+" cat:"+category_id);
	});
	const $category = $("input[name='expense_category']");
	$category.on("change", function() {
		let value = $expenseValue.val();
		let category_id = $(this).val();
		$limit.addClass('alert-success');
		$limit.html("value:"+value+" category:"+category_id);
	});
	/* Things to do:
		- (ajax/twig[expense_cats]) get limited of [category_id] category expense
		if (limited==true) {
			- (ajax) get limit_value of [category_id] expenses
			- (ajax) get sum of [category_id] expenses 
		}
	*/
});

