$(document).ready(function() {
	const $accountEditInfo=$('#accountEditInfo');
	const $changeNameForm= $('#changeNameForm');
	const $editNameBtn=$('#editNameBtn');
	const $changeEmailForm=$('#changeEmailForm');
	const $editEmailBtn=$('#editEmailBtn');
	const $changePasswordForm=$('#changePasswordForm');
	const $editPasswordBtn=$('#editPasswordBtn');
	
	/* Change name of user: ajax-request to database */
	$changeNameForm.on("submit", function(e) {
		e.preventDefault();
		$editNameBtn.prop('disabled', true);
		$accountEditInfo.removeAttr('hidden').removeClass('error').empty().show();
		$.ajax({
			url: '/settings/change-name-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				$accountEditInfo.html(response.message);
			} else {
				$accountEditInfo.addClass('error').html(response.message);
			}
		}).fail(function() {
			alert("fail");
			$accountEditInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#changeNameModal').modal('toggle');
			$editNameBtn.prop('disabled', false);
		});
	});
	
	/* Change email of user: ajax-request to database */
	$changeEmailForm.on("submit", function(e) {
		e.preventDefault();
		$editEmailBtn.prop('disabled', true);
		$accountEditInfo.removeAttr('hidden').removeClass('error').empty().show();
		$.ajax({
			url: '/settings/change-email-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				$accountEditInfo.html(response.message);
			} else {
				$accountEditInfo.addClass('error').html(response.message);
			}
		}).fail(function() {
			alert("fail");
			$accountEditInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#changeEmailModal').modal('toggle');
			$editEmailBtn.prop('disabled', false);
		});
	});
	
	/* Change email of user: ajax-request to database */
	$changePasswordForm.on("submit", function(e) {
		e.preventDefault();
		$editPasswordBtn.prop('disabled', true);
		$accountEditInfo.removeAttr('hidden').removeClass('error').empty().show();
		$.ajax({
			url: '/settings/change-password-ajax',
			method : "POST",
			dataType : "json",
			data: $(this).serialize()
		}).done(function(response) {
			if(response.success) {
				$accountEditInfo.html(response.message);
			} else {
				$accountEditInfo.addClass('error').html(response.message);
			}
		}).fail(function() {
			alert("fail");
			$accountEditInfo.addClass('error').html('Błąd połączenia z bazą danych.');
		}).always(function() {
			$('#inputPassword').val(null);
			$('#oldPassword').val(null);
			$('#changePasswordModal').modal('toggle');
			$editPasswordBtn.prop('disabled', false);
		});
	});
	
});