$.validator.addMethod('validPassword',
	function(value, element, param) {
		if (value != '') {
			if (value.match(/.*[a-z]+.*/i) == null) {
				return false;
			}
			if (value.match(/.*\d+.*/) == null) {
				return false;
			}
		}
		return true;
	},
	'Hasło musi zawierać jedną literę i jedną cyfrę.'
);

$(document).ready(function() {
	$('.error:empty').hide();
	
	$('#changeNameForm').validate({
		rules: {
			name: {
				required: true,
				minlength: 3,
				maxlength: 50
			}
		},
		messages: {
			name: {
				required: 'Wymagana nazwa użytkownika.',
				minlength: 'Minimalna długość nazwy użytkownika to 3 znaki.',
				maxlength: 'Maksymalna długość nazwy użytkownika to 50 znaków.'
			}
		}
	});
	
	$('#changePasswordForm').validate({
		rules: {
			password: {
				required: true,
				minlength: 6,
				maxlength: 50,
				validPassword: true
			}
		},
		messages: {
			password: {
				required: 'Wymagane hasło.',
				minlength: 'Hasło musi zawierać conajmniej 6 znaków.',
				maxlength: 'Hasło może zawierać najwyżej 50 znaków.'
			}
		}
	});
	
	$('#changeEmailForm').validate({
		rules: {
			email: {
				required: true,
				email: true,
				remote: '/account/validate-changing-email'
			}
		},
		messages: {
			email: {
				required: 'Wymagany adres email.',
				email: 'Wpisz poprawny adres email.',
				remote: 'Adres email zajęty.'
			}
		}
	});
	
	$('#deleteAccount').validate({
		rules: {
			deleteConfirmation: {
				required: true,
				remote: '/account/validate-delete-confirmation'
			}
		},
		messages: {
			deleteConfirmation: {
				required: 'Potwierdzenie wymagane.',
				remote: 'Potwierdzenie niewłaściwe.'
			}
		}
	});

});