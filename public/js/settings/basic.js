$('button.js_button').click(function(){
  $(this).parent().next('div.js_toggle_group').slideToggle(50);
  $(this).parent().next('div.js_toggle_group').find('.js_settings_info').empty().hide();
});

 //For the future
$('button.to_first').click(function(){
  $(this).closest('ul').prepend($(this).closest('li'));
});
$('.upbutton').on('click', function () {
    var previousObject = $(this).closest('li').prev('li');
	$(this).closest('li').insertBefore(previousObject);
});
$('.downbutton').on('click', function () {
    var nextObject = $(this).closest('li').next('li');
	$(this).closest('li').insertAfter(nextObject);
});

