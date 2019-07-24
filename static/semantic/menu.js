$(document).ready(function () {
    $('select.ui.dropdown').dropdown({});
    $('span.ui.dropdown').dropdown({});
    $('.message .close')
        .on('click', function () {
            $(this).closest('.message').transition('fade');
        });

    largura = 500;

    if ($(document).width() < largura) {
        $('.botaos').addClass('ui vertical buttons');
    }

    $(window).resize(function () {
        if ($(document).width() < largura) {
            $('.botaos').addClass('ui vertical buttons');
        }
        else {
            $('.botaos').removeClass('ui vertical buttons');
        }
    })

});
