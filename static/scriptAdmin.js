$(document).ready(function () {
    breadcrumb();

    $('.limparEnvioEmail').on('click','', function () {
        let id = $(this).attr('id');
        if(id == 'listarGrupo' || id == 'listarUsuario'){
            $('#buscarUsuario').val();
            $('#bsucarGrupo').val();
            $('.buscarUsuarioView').empty();
            $('.buscarGrupoView').empty();
        }
    });

});
    /////BREADCRUMB
    function breadcrumb(){
            $('.limparEnvioEmail').click(function () {
                let caminho = $(this).text();
                $('.caminho').text(caminho);
            });
    }
    /////END BREADCRUMB