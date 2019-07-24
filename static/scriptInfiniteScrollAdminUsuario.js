/////INFINITE SCROLLING
$(document).ready(function () {
    $(window).scrollTop(0);
    let limit = 5;
    let actionU = 'inactive';

    ////QUANTIDADE CARDS GRUPO
    function quantidadeCardsPagU() {

        let limitStaticArrayU = new Array();
        $(".buscarUsuarioView .Usuario-card").each(function (index) {
            limitStaticArrayU.push(index);
        });

        return limitStaticArrayU.length;
    }
    ////END QUANTIDADE CARDS USUARIO


    ////LAZZY_LOADER

    /// REMOVE
    function lazzy_loader_removeU(){
        $('.buscarUsuarioView #loader-img').remove();
    }
    ///END REMOVE

    ///LOAD
    function lazzy_loaderU(){
        let outputU = '';
        outputU += '<span id="loader-img"><img class="ui tiny image centered" src="../static/images/spinner.gif" alt=""></span>';
        $('.buscarUsuarioView').append(outputU);
    }
    ///END LOAD

    ////END LAZZY_LOADER


    if(actionU == 'inactive') {
        actionU = 'active';


        ////BUSCAR USUARIO
        $('#buscarUsuario').on('keyup', function () {
            $('#buscarUsuarioView').html('');
            let nomeU = $('#buscarUsuario').val();
            $('#buscarUsuarioValInput').text(nomeU);
            if (nomeU == '') {
                $.post('getUsuario', {
                    nome: '',
                    limit: limit,
                    start: 0
                }, function () {
                    $('.buscarUsuarioView').append('<h4 class="ui header center aligned"><br>Digite um nome válido</h4>');
                    actionU = 'inactive';
                });
            } else {
                $.post('getUsuario', {
                    nome: nomeU,
                    limit: limit,
                    start: 0
                }, function (data) {
                    $('.buscarUsuarioView').append(data);
                    let conditionU = $('.Usuario-null').attr('id');
                    if(conditionU == 'inactive'){
                        actionU = 'inactive';
                    }else {
                        actionU = 'active';
                    }
                });
            }
        });
        ////END BUSCAR USUARIO


    }


    ////LOAD DATA AFTER SCROLL FUNCTION
    function forScrollU(limit, startU) {
        console.log("action: "+actionU+" qtd_user_pag:"+quantidadeCardsPagU());
        let valU = $('#buscarUsuarioValInput').text();
        $.post('getUsuario', {
            nome : valU,
            limit: limit,
            start: startU
        }, function(data){
            $('.buscarUsuarioView').append(data);
            let conditionU = $('.Usuario-null').attr('id');
            if(conditionU == 'inactive'){
                actionU = 'inactive';
            }else {
                actionU = 'active';
            }
        });
        $('.Usuario-null').remove();
    }
    ////END LOAD DATA AFTER SCROLL FUNCTION


    $(window).scroll(function() {
        let qtd_bdU = $('#qtd_bd_Usuarios').text();
        if (qtd_bdU >= quantidadeCardsPagU()) {
            let documentHeightU = '';
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                // console.log('MOBILE');
                documentHeightU = $(document).height() - 31;
            } else {
                // console.log('DESKTOP');
                documentHeightU = $(document).height();
            }
            if ($(window).scrollTop() + $(window).height() >= documentHeightU && actionU == 'active') {

                actionU = 'inactive';

                lazzy_loaderU();

                let startU = quantidadeCardsPagU();

                setTimeout(function () {
                    if (startU > 0){
                        forScrollU(limit, startU);
                        lazzy_loader_removeU();
                    }
                    lazzy_loader_removeU();
                }, 1000);
            }
        }
    });

});
/////END INFINITE SCROLLING