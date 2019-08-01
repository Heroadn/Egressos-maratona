<?php
/**
 * Created by PhpStorm.
 * User: aluno
 * Date: 25/06/18
 * Time: 14:25
 */

class Timeline extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);

        if ($usuario == NULL){
            redirect('PortalEgresso');
        }
    }

    public function index(){
        redirect('Timeline/postagem');

    }

    public function postagem(){
        $this->form_validation->set_rules('titulo', 'Titulo', array('required', 'min_length[5]', 'max_length[80]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$%]+$/]'));
        if ($this->form_validation->run() == FALSE) {
            $usuario = $this->session->userdata("usuario_logado");
            $this->load->model("Model_usuario");
            $this->load->model("Model_cadastro");
            $this->load->model("Model_timeline");
            $this->load->model("Model_postagem");

            $dados_usuario["form_open"] = form_open_multipart("Usuario/mudarFoto", 'class=""');
            $js = 'onchange="if(this.value != null){$(\'#labelFoto\').toggle();$(\'#enviar-foto\').removeAttr(\'style\');$(\'#cancelar-foto\').removeAttr(\'style\');}"';
            $jsBuscarUsuario = 'onkeyup="buscarUsuario()"';

            $dados_usuario["input_upload"] = form_upload(array("name" => "imagem", "id" => "selecionar-foto", "class" => "", "accept" => "image/*", "hidden" => "true"), '', $js);
            $dados_usuario["label_foto"] = form_label("Alterar Foto", "selecionar-foto", array('class' => 'ui small green inverted button', 'id' => 'labelFoto'));
            $dados_usuario["button_submit_ft"] = form_button(array("type" => "submit", "id" => "enviar-foto", "content" => "Enviar", "class" => "ui small green inverted button", "style" => "display:none;"));
            $dados_usuario["anchor_cancelar"] = anchor('Usuario/perfil','Cancelar',array("href" => "Usuario/perfil", "id" => "cancelar-foto", "class" => "ui small red inverted button", "style" => "display:none;"));
            $dados_usuario["form_close"] = form_close();
            $dados_usuario["username"] = $usuario["nome"];

            $idUsuario = $usuario["id_usuario"];
            $dados_usuario["file_name"] = $this->getCaminhoFoto($idUsuario);
            $dados_usuario["membro_desde"] = $this->Model_usuario->buscaAnoDeIngresso($idUsuario);

            $dados_usuario["notificacoes"] = $this->Model_usuario->buscarNotificacoes();
            $dados_usuario["nr_notificacoes"] = strval(count($dados_usuario['notificacoes']));
            $dados_usuario["url_perfil"] = base_url("Usuario/perfil");

            $data["totalPosts"] = $this->getCountPost(0);
            $posts = $this->Model_timeline->gera_form("timeline/postagem");

            $posts["erros_validacao"] = array(
                "erros_titulo" => form_error('titulo'),
                "erros_conteudo" => form_error('conteudo')
            );

            $posts+= $dados_usuario;
            $posts+= $data;
            $this->twig->display('usuario/timeline', $posts);

        }else{
            $usuario = $this->session->userdata("usuario_logado");
            $post = array(
                "titulo" => $this->input->post("titulo"),
                "descricao" => $this->input->post("conteudo"),
                "id_status" => 1,
                'data' => date("Y-m-d H:i:s"),
                "id_usuario" => $usuario['id_usuario']
            );

            $this->load->model("Model_postagem");
            $this->load->model("Model_timeline");

            $idPost = $this->Model_postagem->salva($post);

            $file = $this->custom_upload->multiple_upload('file', array(
                'upload_path' => 'static/images',
                'allowed_types' => 'jpg|jpeg|bmp|png|gif',
                'max_size' => '2048'
            ));

            $this->Model_timeline->publicacaoComImagens($file, $idPost);

            redirect('Timeline/');
        }
    }

    public function visu_post(){
        $idPost = $this->uri->segment(3);
        $this->load->model("Model_postagem");
        $post = $this->Model_postagem->postagemIntegra($idPost);
        $dado_postagem['id_post'] = $idPost;

//            Temporario
        $usuario = $this->session->userdata("usuario_logado");
        $this->load->model("Model_usuario");
        $this->load->model("Model_timeline");
        $dado_postagem['membro_desde'] = $this->Model_usuario->buscaAnoDeIngresso($post['id_usuario']);

        $this->load->model("Model_cadastro");
        $dados_usuario["file_name"] = $this->getCaminhoFoto($usuario['id_usuario']);
        $dados_usuario["file_name_post"] = $this->getCaminhoFoto($post['id_usuario']);
        $dados_usuario["username"] = $usuario["nome"];

        $dados_usuario += $this->Model_timeline->gera_form_comentario('Timeline/processaComentario');
        $dados_usuario["notificacoes"] = $this->Model_usuario->buscarNotificacoes();
        $dados_usuario["nr_notificacoes"] = strval(count($dados_usuario['notificacoes']));
        //Temporario
        $post['comentarios'] = $this->Model_timeline->buscarComentario($idPost);
        $post+= $dado_postagem;
        $post+= $dados_usuario;
        
        $this->twig->display('usuario/visualizarPost', $post);  
    }

    public function contadorCurtidas(){
        $posts = $this->input->post('idsPost');
        $this->load->model("Model_postagem");
//        $arrayPosts = $this->Model_postagem->retornaArray($posts);
        $arrayCurtidas= $this->Model_postagem->countCurtidas($posts);
        $jsonCurtidas = json_encode($arrayCurtidas);
        header('Content-type:application/json;charset=utf-8');
        print($jsonCurtidas);
    }

    public function curtirPost(){
        $idPost = $this->input->post('idPost');
        $this->load->model("Model_postagem");
        $usuario = $this->session->userdata("usuario_logado");
        $idUsuario = $usuario["id_usuario"];
        $this->Model_postagem->insereCurtida($idUsuario, $idPost);
    }

    public function descurtirPost(){
        $idPost = $this->input->post('idPost');
        $this->load->model("Model_postagem");
        $usuario = $this->session->userdata("usuario_logado");
        $idUsuario = $usuario["id_usuario"];
        $this->Model_postagem->updateCurtida($idUsuario, $idPost);
    }

    public function minhasCurtidas(){
        $this->load->model("Model_postagem");
        $usuario = $this->session->userdata("usuario_logado");
        $idUsuario = $usuario["id_usuario"];
        $minhasCurtidas = $this->Model_postagem->minhasCurtidas($idUsuario);
        $jsonCurtidas = json_encode($minhasCurtidas);
        header('Content-type:application/json;charset=utf-8');
        print($jsonCurtidas);
    }

    public function verificaCurtida(){
        $idPost = $this->input->post('idPost');
        $this->load->model("Model_postagem");
        $usuario = $this->session->userdata("usuario_logado");
        $idUsuario = $usuario["id_usuario"];
        $idStatus = $this->Model_postagem->verificaCurtida($idUsuario,$idPost);
        $jsonCurtidas = json_encode($idStatus);
        header('Content-type:application/json;charset=utf-8');
        print($jsonCurtidas);
    }

    public function fetch(){
        $this->load->model("Model_post");
        $this->Model_post->fetch();
    }

    public function getCaminhoFoto($idUser){
        $this->load->model("Model_Usuario");
        $foto = $this->Model_usuario->buscarFoto($idUser);
        return $foto["file_name"];
    }

    public function getCountPost($idGrupo){
        $this->load->model("Model_postagem");
        $quantidadePosts = $this->Model_postagem->qtd_posts($idGrupo);
        return $quantidadePosts;
    }

    public function teste2(){
        $this->load->model("Model_postagem");
        $quantidadePosts = $this->Model_postagem->qtd_posts();
        $quantidadePostsArray = array('quantidade' => $quantidadePosts);
        $jsonQuantidadePosts = json_encode($quantidadePostsArray);
        header('Content-type:application/json;charset=utf-8');
        print($jsonQuantidadePosts);
    }

    public function teste3(){
        $this->load->model("Model_postagem");
        $posts = $this->Model_postagem->buscarFotoPost(16);
        print_r($posts);
    }

    public function teste(){
        $this->load->model("Model_postagem");
        $retorno = $this->Model_postagem->buscarFotoPost(30);
        print_r($retorno);
    }

    public function url(){
        print(base_url());
    }

    public function processaComentario(){
        $this->load->model('Model_timeline');
        $usuario = $this->session->userdata("usuario_logado");
        $conteudo = $this->input->post('conteudo');

        $idPost = $this->input->post('idPost');

        $this->Model_timeline->cadastrarComentario($conteudo, $usuario['id_usuario'], $idPost);

        redirect("Timeline/visu_post/$idPost");

    }
}
