<?php

class Chat extends CI_Controller
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
        redirect('Chat/conversar');
    }

    public function conversar(){
        $this->form_validation->set_rules('conteudo','Mensagem', 'required');

        if ($this->form_validation->run() == FALSE) {
            $usuario = $this->session->userdata("usuario_logado");
            $this->load->model("Model_usuario");
            $this->load->model("Model_cadastro");
            $this->load->model("Model_timeline");
            $this->load->model("Model_conversar");
            $this->load->model("Model_mensagem");

            $idUsuario = $usuario["id_usuario"];
            
            $mensagens = $this->Model_conversar->gera_form("chat/conversar");
            $mensagens["erros_validacao"] = array(
                "erros_conteudo" => form_error('conteudo')
            );

            $this->twig->display('chat/conversar', $mensagens);
        }
    }

    public function fetch(){
        $this->load->model('Model_grupo');
        $this->load->model('Model_conversar');

        $grupo = $this->Model_grupo->buscaGrupo();
        $mensagem = $this->Model_conversar->fetch($grupo['id_grupo']);
        echo $mensagem;
    }

    public function salvar(){
        $usuario = $this->session->userdata("usuario_logado");

        $mensagem = array(
            "conteudo" => $this->input->post("conteudo"),
            'data' => date("Y-m-d H:i:s"),
            "id_usuario" => $usuario['id_usuario'],
            "id_grupo" =>   $usuario['id_grupo']
        );

        $this->load->model("Model_mensagem");
        $idMensagem = $this->Model_mensagem ->salva($mensagem);
    }

}