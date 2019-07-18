//MATOMO (Analytics)
var _paq = window._paq || [];
/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
_paq.push(['trackPageView']);
_paq.push(['enableLinkTracking']);
(function() {
    var u="https://analytics.fabricadesoftware.ifc.edu.br/";
    _paq.push(['setTrackerUrl', u+'matomo.php']);
    _paq.push(['setSiteId', '2']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
})();
//END MATOMO

$(document).ready(function () {

    let url_atual = window.location.href;
    let arrayurl  = url_atual.split("/");
    let parametroDaUrl = arrayurl[(arrayurl.length) - 1];
    let controlador = arrayurl[(arrayurl.length) -2];


    ////CAPTAR CLIQUE CURTIDAS
    $(document).on("click", '.love', function() {
        $(this).removeClass("red");
        // noinspection JSValidateTypes
        let idPost = $(this).parent().children('.curtidas').attr("id");
        verificarCurtida(idPost);
    });
    ////END CAPTAR CLIQUE CURTIDAS


    ////BUSCAR USUARIO
    if (parametroDaUrl === 'perfil') {

        $('#buscarUsuario').on('keyup', function () {
            $('#buscarUsuarioView').html('');
            let nome = $('#buscarUsuario').val();
            $('#buscarUsuarioValInput').text(nome);
            if (nome === '') {
                $.post('getUsuario', {
                    nome: '',
                }, function () {
                    $('.buscarUsuarioView').append('<h4 class="ui header center aligned"><br>Digite um nome válido</h4>');
                });
            } else {
                $.post('getUsuario', {
                    nome: nome,
                }, function (data) {
                    $('.buscarUsuarioView').append(data);
                });
            }
        });
    }
    ////END BUSCAR USUARIO


    ////MODAL DE CONFIRMAÇÃO
    $('.ui.basic.modal').modal('attach events', '.confirm.button', 'show');
    ////END MODAL DE CONFIRMAÇÃO


    ////INFINITE-SCROLL

    if(controlador === 'Timeline' || controlador === 'Grupo' || controlador === 'Usuario' || controlador === 'visu_post' && $.isNumeric(parametroDaUrl)) {
        atualiza();
    }

    $(window).scrollTop(0);
    let limit = 10;
    let action = 'inactive';

    function quantidadePostsPag() {

        let limitStaticArray = [];

        $(".posts .timeline-post").each(function (index) {
            limitStaticArray.push(index);
        });

        return limitStaticArray.length;
    }

    function lazzy_loader_remove() {
        $('.posts #loader-img').remove();
    }

    function lazzy_loader()
    {

        let output = '';
        output += '<span id="loader-img"><img class="ui tiny image centered" src="../static/images/spinner.gif"></span>';
        $('.posts').append(output);
    }

    function load_data(limit, start)
    {
        let id_grupo = $('#id_grupo').text();
        if(id_grupo === ''){
            id_grupo = 0;
        }

        $.ajax({
            url:urlComp()+"fetch",
            method:"POST",
            data:{limit:limit, start:start, id_grupo:id_grupo},
            cache: false,
            success:function(data)
            {
                if(data === '')
                {
                    $('.posts').append('<h4 class="ui header center aligned">Não foram encotrados mais resultados</h4>');
                    action = 'inactive';
                }
                else
                {
                    $('.posts').append(data);
                    action = 'active';
                }
            }
        });
    }
    let start = quantidadePostsPag();

    if(action === 'inactive')
    {
        action = 'active';
        load_data(limit, start);
    }

    $(window).scroll(function(){
        if ($('#qtd_bd').text() >= quantidadePostsPag()) {

    // let soma = $(window).scrollTop() + $(window).height();
    // let dife = $(document).height() - soma;
    // console.log(' Dife.:' + dife + ' Action:'+action);

            let documentHeight = 0;

            if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                // console.log('MOBILE');
               documentHeight = $(document).height() - 31;
            } else {
                // console.log('DESKTOP');
               documentHeight = $(document).height();
            }

            if ($(window).scrollTop() + $(window).height() >= documentHeight && action === 'active') {

                action = 'inactive';

                lazzy_loader();

                start = quantidadePostsPag();

                setTimeout(function () {
                    load_data(limit, start);
                    lazzy_loader_remove()
                }, 1000);
            }
        }
    });
    ////END INFINITE-SCROLL

    $('.accordion')
        .accordion({
            selector: {
                trigger: '.title'
            }
        })
    ;

    $('.ui.sticky')
        .sticky({
            context: '#example1'
        })
    ;

    $("#idCampus").change(function(){
        let id_campus = $("#idCampus").val();

        $.post('getCurso',{
            id_campus : id_campus
        }, function(data){
            $('#idCurso').html(data);
        });
    });

    $("#idCurso").change(function(){
        let id_curso = $("#idCurso").val();
        $.post('getTurma',{
            id_curso : id_curso
        }, function(data){
            $('#idTurma').html(data);
        });
    });

    $('.image')
        .dimmer({
            on: 'hover'
        });

    $('.ui.dropdown')
        .dropdown()
    ;
    var clicado = 0;
    $(".checkbox").on("click", function(){
        if(clicado === 0){
            $('.enviar').removeAttr('disabled');
            clicado = 1;
        }
        else{
            $('.enviar').attr('disabled', function () {
                return 'disabled';
            });
            clicado = 0;
        }
    });

    $('.item').tab();

    $("#selecionar-foto").on('change', function () {

        if (typeof (FileReader) != "undefined") {

            let image_holder = $("#image-holder");
            image_holder.empty();
            let reader = new FileReader();
            reader.onload = function (e) {
                $("#imagem-usuario").attr("src", e.target.result).appendTo(image_holder);
            }
            image_holder.show();
            reader.readAsDataURL($(this)[0].files[0]);
        } else{
            alert("Este navegador não suporta FileReader.");
        }
    });

    //var nome_cookie = Cookies.get("href");
    // if(Cookies.get("href") == 'amigos'){
    //     $("#Amgs").show();
    // }else if(Cookies.get("href") == 'notif'){
    //     $("#Notif").show();
    // }else{
    //     $("#Geral").show();
    // }


    $("#Amgsbt").click(function(){
        $("#Amgs").toggle();
        $("#Geral").hide();
        $("#Notif").hide();
    });

    $("#Amgshref").click(function () {
       $.post('url', function (data) {
           window.location.href = data+"Usuario/perfil";
           Cookies.set("href", "amigos");
       });
    });

    $("#Geralbt").click(function(){
        $("#Geral").toggle();
        $("#Amgs").hide();
        $("#Notif").hide();
    });

    $("#Notifbt").click(function(){
        $("#Notif").toggle();
        $("#Geral").hide();
        $("#Amgs").hide();
    });

    $("#Notifhref").click(function () {
        $.post('url', function (data) {
            window.location.href = data+"Usuario/perfil";
            Cookies.set("href", "notif");
        });
    });


    $('#botaomenu').on('click',function () {
        $('#menusumir').sidebar('toggle').removeClass('disabled');
        // $('#menusumir')
    });

    $('#modifyFoto').on("click", function(){
        $('.ui.modal').modal('show');
    });

    $('#botaoEnviarFoto').on("click", function(){
        $('#botaoInputFoto').trigger('click');
    });

});

////CURTIDAS
function urlComp() {
    let url_atual = window.location.href;
    let pos = url_atual.split("/");
    let parametroDaUrl  = pos[(pos.length) - 1];
    let parametroDaUrl2 = pos[(pos.length) -2];
    let textComp = '';
    if($.isNumeric(parametroDaUrl)) {
        textComp = '../';
        return textComp;
    }else if(parametroDaUrl2 === 'Grupo'){
        textComp = '';
        return textComp;
    }else{
        textComp = '';
        return textComp;
    }
}

function pegarid() {
    let arrayIdsPost = [];
    $('.curtidas').each(function () {
        arrayIdsPost.push($(this).attr("id"));
    });
    return arrayIdsPost;
}

function countCurtidas() {
    let idsPost = pegarid();
    $.post(urlComp()+'contadorCurtidas', {
        idsPost : idsPost
    }, function (data) {
        $('.curtidas').each(function () {
            $(this).text(data[$(this).attr("id")]);
        });
    }), "json";
}

function minhascurtidas() {
    $.post(urlComp()+'minhasCurtidas',{
    }, function (data) {
        $.each(data, function (index, id) {
            $(".curtidas").each(function () {
                if ($(this).attr("id") == id['id_post']) {
                    $(this).parent().children('.love').addClass("red");
                }
            });
        });

    }), "json";
}

function verificarCurtida(idPost) {
    $.post(urlComp() + 'verificaCurtida', {
        idPost: idPost,
    }, function (data) {
        if (data['id_status'] == 1) {
            $.post(urlComp() + 'descurtirPost', {
                idPost: idPost
            }, function () {
                minhascurtidas();
            });
        } else {
            $.post(urlComp() + 'curtirPost', {
                idPost: idPost
            }, function () {
                minhascurtidas();
            });
        }
    });
}

function atualiza() {
        pegarid();
        minhascurtidas();
        countCurtidas();
        setTimeout('atualiza()',1500);
}
////END CURTIDAS


function previewFiles() {

    let preview = document.querySelector('#imagens-carregadas');
    let files = document.querySelector('#adicionar-foto').files;

    function readAndPreview(file) {

        let reader = new FileReader();
        reader.addEventListener("load", function () {
            let image = new Image();
            image.height = 100;
            image.title = file.name;
            // noinspection JSValidateTypes
            image.src = this.result;
            preview.appendChild(image);
        }, false);

        reader.readAsDataURL(file);

    }

    if (files) {

        [].forEach.call(files, readAndPreview);
    }
}