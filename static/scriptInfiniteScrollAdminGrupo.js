/////INFINITE SCROLLING
$(document).ready(function () {
    $(window).scrollTop(0);
    let limit = 5;
    let actionG = 'inactive';

    ////QUANTIDADE CARDS GRUPO
    function quantidadeCardsPagG() {

        let limitStaticArrayG = new Array();
        $(".buscarGrupoView .Grupo-card").each(function (index) {
            limitStaticArrayG.push(index);
        });

        return limitStaticArrayG.length;
    }
    ////END QUANTIDADE CARDS GRUPO


    ////LAZZY_LOADER

    /// REMOVE
    function lazzy_loader_removeG(){
        $('.buscarGrupoView #loader-img').remove();
    }
    ///END REMOVE

    ///LOAD
    function lazzy_loaderG(){
        let outputG = '';
        outputG += '<span id="loader-img"><img class="ui tiny image centered" src="../static/images/spinner.gif" alt=""></span>';
        $('.buscarGrupoView').append(outputG);
    }
    ///END LOAD

    ////END LAZZY_LOADER


    if(actionG == 'inactive') {
        actionG = 'active';


        ////BUSCAR GRUPO
        $('#buscarGrupo').on('keyup', function () {
            $('#buscarGrupoView').html('');
            let nomeG = $('#buscarGrupo').val();
            $('#buscarGrupoValInput').text(nomeG);
            if (nomeG == '') {
                $.post('getGrupo', {
                    nome: '',
                    limit: limit,
                    start: 0
                }, function () {
                    $('.buscarGrupoView').append('<h4 class="ui header center aligned"><br>Digite um nome válido</h4>');
                    actionG = 'inactive';
                });
            } else {
                $.post('getGrupo', {
                    nome: nomeG,
                    limit: limit,
                    start: 0
                }, function (data) {
                    $('.buscarGrupoView').append(data);
                    let conditionG = $('.Grupo-null').attr('id');
                    if(conditionG == 'inactive'){
                        actionG = 'inactive';
                    }else {
                        actionG = 'active';
                    }
                });
            }
        });
    ////END BUSCAR GRUPO


    }


    ////LOAD DATA AFTER SCROLL FUNCTION
    function forScrollG(limit, startG) {
        let valG = $('#buscarGrupoValInput').text();
        $.post('getGrupo', {
            nome : valG,
            limit: limit,
            start: startG
        }, function(data){
            $('.buscarGrupoView').append(data);
            let conditionG = $('.Grupo-null').attr('id');
            if(conditionG == 'inactive'){
                actionG= 'inactive';
            }else {
                actionG = 'active';
            }
        });
        $('.Grupo-null').remove();
    }
    ////END LOAD DATA AFTER SCROLL FUNCTION


    $(window).scroll(function() {
        let qtd_bdG = $('#qtd_bd_Grupos').text();
        if (qtd_bdG >= quantidadeCardsPagG()) {
            let documentHeightG = '';
            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                // console.log('MOBILE');
                documentHeightG = $(document).height() - 31;
            } else {
                // console.log('DESKTOP');
                documentHeightG = $(document).height();
            }
            if ($(window).scrollTop() + $(window).height() >= documentHeightG && actionG == 'active') {

                actionG = 'inactive';

                lazzy_loaderG();

                let startG = quantidadeCardsPagG();

                setTimeout(function () {
                    if (startG > 0){
                        forScrollG(limit, startG);
                        lazzy_loader_removeG();
                    }
                    lazzy_loader_removeG()
                }, 1000);
            }
        }
    });

});
/////END INFINITE SCROLLING