$(document).ready(function () {
    $('.accordion.grupo')
        .accordion({
            selector: {
                trigger: '.title'
            }
        })
    ;

    $('.accordion.aluno')
        .accordion({
            selector: {
                trigger: '.title'
            }
        })
    ;

    $('.menu .item')
        .tab()
    ;

    $('.enviarEmail').click(function () {
        var email = $(this).val();
        console.log(''+email+' foi visto');
        $.post('formEmail', {
            email : email
        }, function(data){
            $('#formEmail').html(data);
        })
    });

    $('.enviarEmailGrupo').click(function () {
        var idGrupo = $(this).val();
        console.log(''+idGrupo+' foi visto');
        $.post('formEmailGrupo', {
            idGrupo : idGrupo
        }, function(data){
            $('#formEmail').html(data);
        })
    });

    $('.limparEnvioEmail').click(function () {
        $('#formEmail').empty();
    })
});