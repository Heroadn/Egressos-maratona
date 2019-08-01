<?php
/**
 * Created by PhpStorm.
 * User: aluno
 * Date: 31/08/18
 * Time: 16:16
 */
class Grupo extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);

        if ($usuario == NULL) {
            redirect('PortalEgresso');
        }
    }

    public function index(){
        $this->postagemGrupo();
    }

    public function postagemGrupo(){
        $this->form_validation->set_rules('titulo', 'Titulo', array('required', 'min_length[5]', 'max_length[50]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]'));
        $this->form_validation->set_rules('conteudo', 'Conteudo', array('required', 'min_length[10]', 'max_length[500]')); #liberado caracteres especiais para teste

        if ($this->form_validation->run() == FALSE) {

            $this->load->model("Model_postagem");
            $this->load->model("Model_grupo");
            $this->load->model("Model_amigo");
            $this->load->model("Model_timeline");

            $usuario = $this->session->userdata("usuario_logado");

            $dados_usuario["username"] = $usuario["nome"];
            $idUsuario = $usuario["id_usuario"];
            $dados_usuario["file_name"] = $this->getCaminhoFoto($idUsuario);

            $grupo = $this->Model_grupo->buscaGrupo();
            $id_grupo = $this->session->userdata("usuario_logado")['id_grupo'];

            $dados_usuario["id_grupo_user"] = $id_grupo;

            $posts = array(
                "posts" => $this->Model_postagem->buscaPosts($id_grupo)
            );

            $data["totalPosts"] = $this->getCountPost($id_grupo);
            $posts = $this->Model_timeline->gera_form("Grupo/postagemGrupo");

            $posts["grupo"] = $grupo;
            $posts['membros'] = $this->Model_grupo->buscaMembros($id_grupo);
            $posts['qtd_membros'] = strval(count($posts['membros']));
            $posts['amigos'] = $this->Model_amigo->buscarAmizades();

            $posts["erros_validacao"] = array(
                "erros_titulo" => form_error('titulo'),
                "erros_conteudo" => form_error('conteudo')
            );
            $posts += $dados_usuario;
            $posts += $data;

            $this->twig->display('grupo/inicio', $posts);

        }else{
            $this->load->model("Model_grupo");

            $grupo = $this->Model_grupo->buscaGrupo();
            $usuario = $this->session->userdata("usuario_logado");
            $post = array(
                "titulo" => $this->input->post("titulo"),
                "descricao" => $this->input->post("conteudo"),
                'data' => date("Y-m-d H:i:s"),
                "id_status" => 1,
                "id_usuario" => $usuario['id_usuario'],
                "id_grupo" => $grupo['id_grupo']
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

            redirect('Grupo/');
        }
    }

    public function contadorCurtidas(){
        $posts = $this->input->post('idsPost');
        $this->load->model("Model_postagem");
        //$arrayPosts = $this->Model_postagem->retornaArray($posts);
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
        $this->load->model('Model_grupo');

        $grupo = $this->Model_grupo->buscaGrupo();

        $this->load->model("Model_post");
        $this->Model_post->fetch($grupo['id_grupo']);
    }

    public function getCountPost($idGrupo){
        $this->load->model("Model_postagem");
        $quantidadePosts = $this->Model_postagem->qtd_posts($idGrupo);
        return $quantidadePosts;
    }

    public function getCaminhoFoto($idUser){
        $this->load->model("Model_usuario");
        $foto = $this->Model_usuario->buscarFoto($idUser);
        return $foto["file_name"];
    }

}
