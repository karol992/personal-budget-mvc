<?php


?>

<form class="form" id="formSubmit">
    <div class="form-row">
            <label for="inpTitle">Tytuł postu</label>
            <input type="text" id="inpTitle">
    </div>
    <div class="form-row">
            <label for="inpBody">Treść postu</label>
            <textarea id="inpBody"></textarea>
    </div>
    <div class="form-row">
            <button type="submit" class="button">Wyślij i sprawdź w konsoli odpowiedź</button>
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.1.1.min.js" integrity="sha256-hVVnYaiADRTO2PzUGmuLJr8BLUSjGIZsDYGmIJLv2b8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script>

const apiUrl = "https://jsonplaceholder.typicode.com";

//pobieramy wszystkie niezbędne elementy
const $form = $('.form');
const $inputTitle = $('#inpTitle');
const $inputBody = $('#inpBody');
const $submitBtn = $form.find(":submit");

//podpinamy się pod wysłany formularz
$form.on("submit", function(e) {
    e.preventDefault();

    //po kliknięciu wyłączam submit i dodaję mu loading
    $submitBtn.addClass('loading');
    $submitBtn.prop('disabled', true);

    //wysyłamy dane
    $.ajax({
        url: apiUrl + '/posts',
        method : "POST",
        dataType : "json",
        data : {
            userId : 1, //przykładowy user
            title : $inputTitle.val(), //wartości danych pobieram z pól
            body : $inputBody.val()
        }
    });
    /*.done(function(res) {
        console.log("Użytkownik został dodany do bazy", res);
    })
    .always(function() {
        //po zakończeniu połączenia włączam submit i wyłączam klasę loading
        $submitBtn.removeClass('loading');
        $submitBtn.prop('disabled', false);
    });*/
});

</script>