$(document).ready(function () {
    $('textarea#froala-editor').froalaEditor({height: '300px'});
    $('form').on('submit', function () {
       var content = $('.fr-element').html();
       document.getElementById("editor").value = content;
    });
});