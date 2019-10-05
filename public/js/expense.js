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
	const date = $('#expense_date').val();
	
	$expenseValue.on("change", function() {
		$checked = $("input[name='expense_category']:checked");
		let limited = $checked.attr("data-limited"); // 0 or 1
		if (limited==1) {
			let value = $(this).val();
			let category_id = $checked.val();
			let limit_value = $checked.attr("data-limit-value");
			$limit.html("value:"+value+" cat:"+category_id+" limit_value:"+limit_value+" date:"+date);
			$.ajax({
				url: '/expense/get-perioded-sum-ajax',
				method : "POST",
				dataType : "json",
				data: {
					"cat_id" : category_id,
					"date" : date
				}
			}).done(function(response) {
				console.log("ajax done: "+response);
			}).fail(function() {
				console.log("ajax fail");
			});
		} else {
			$limit.empty();
		}
	});
	
	const $category = $("input[name='expense_category']");
	
	$category.on("change", function() {
		$checked = $(this);
		let limited = $checked.attr("data-limited"); // 0 or 1
		if (limited==1) {
			let value = $expenseValue.val();
			let category_id = $checked.val();
			let limit_value = $checked.attr("data-limit-value");
			$limit.html("value:"+value+" cat:"+category_id+" limit_value:"+limit_value+" date:"+date);
		} else {
			$limit.empty();
		}
	});
	/* Things to do:
		-
		- (ajax) get sum of [category_id] expenses
	*/
});

