/* 
Page content updates every single expesne category modification (add new, edit, delete)in jQuery functions and executes its in AJAX request.
*/

/* Fill category properties in #incomeEditModal*/
function passExpenseCategory() {
	let editValue = $(this).attr("value");
	let editName = $(this).attr("name");
	let editLimited = $(this).attr("data-limited");
	let editLimitValue = $(this).attr("data-limit-value");
	$('#editExpenseLabel').val(editName);
	$('#editExpenseId').val(editValue);
	console.log("passExpenseCategory->editLimit: "+editLimited);
	if (editLimited == "true") {
		$('#editExpenseLimited').prop("checked", true);
		$('#editExpenseLimitValue').prop("readonly", false).show();
		$('#editExpenseLimitEmpty').hide();
	} else {
		$('#editExpenseLimited').prop("checked", false);
		$('#editExpenseLimitValue').prop("readonly", true).hide();
		$('#editExpenseLimitEmpty').show();
	}
	$('#editExpenseLimitValue').val(editLimitValue);
};

/* onclick pencil-button on the list of income categories */
$('.expense_edit').on('click', passExpenseCategory);


$('#editExpenseLimited').change(function() {
	$('#editExpenseLimitValue').prop("readonly", !$(this).prop('checked'));
	$('#editExpenseLimitValue').toggle();
	$('#editExpenseLimitEmpty').toggle();
});


/* new expense-category: ajax-request to database and page-update */
const $addExpenseForm = $('#addExpenseCategory');
const $expenseSubmitBtn = $("#addExpenseCategoryBtn");
const $expenseInfo = $("#expenseCategoryInfo");

/* edit income category: ajax-request to database and page-update */
const $expenseEditForm = $('#expenseEditForm');
const $expenseEditBtn = $("#editExpenseBtn");
$expenseEditForm.on("submit", function(e) {
	e.preventDefault();
    $expenseEditBtn.prop('disabled', true);
	$expenseInfo.empty().hide();
	$.ajax({
		url: '/settings/edit-expense-category-ajax',
		method : "POST",
		dataType : "json",
		data: $(this).serialize({ checkboxesAsBools: true })
	}).done(function(response) {
		if(response.success) {
			let editName = $('#editExpenseLabel').val();
			let editId = $('#editExpenseId').val();
			let editLimited = $('#editExpenseLimited').prop('checked');
			let editLimitValue =parseInt($('#editExpenseLimitValue').val()).toFixed(2);
			$('#expense'+editId+'name').html(editName);
			$('#expense'+editId+'editBtn').attr('name', editName);
			$('#expense'+editId+'editBtn').attr('data-limited', editLimited);
			$('#expense'+editId+'editBtn').attr('data-limit-value', editLimitValue);
			$('#expense'+editId+'delBtn').attr('name', editName);
			$('#expense'+editId+'limitValue').html(editLimitValue);
			//console.log("editLimited: "+editLimited);
			if (editLimited) {
				$('#expense'+editId+'limit').show();
			} else {
				$('#expense'+editId+'limit').hide();
			}
		} else {
			$expenseInfo.removeAttr('hidden').addClass('error').html(response.message).show();
		}
	}).fail(function() {
		alert("edit fail");
	}).always(function() {
		$('#expenseEditModal').modal('toggle');
		$expenseEditBtn.prop('disabled', false);
	});
});
