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

        if ($this->input->post("conteudo") == FALSE) {
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

            /*TEMPORARIO*/
            $this->twig->display('chat/conversar', $mensagens);
        }else{
            $usuario = $this->session->userdata("usuario_logado");

            $mensagem = array(
                "conteudo" => $this->input->post("conteudo"),
                'data' => date("Y-m-d H:i:s"),
                "id_usuario" => $usuario['id_usuario'],
                "id_grupo" =>   $usuario['id_grupo']
            );

            //Salvando mensagem
            $this->load->model("Model_mensagem");
            $idMensagem = $this->Model_mensagem->salva($mensagem);

            //Carregando mensagem salvar para adicionar na tela de usuario
            $this->load->model("Model_conversar");
            $card = $this->Model_conversar->build($idMensagem);

            
            $ch = curl_init('http://localhost:8081');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            $jsonData = json_encode([
                'card' => $card
            ]);
            $query = http_build_query(['data' => $jsonData]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
        }
    }

    public function fetch(){
        $usuario = $this->session->userdata("usuario_logado");
        $this->load->model('Model_conversar');
        $mensagem = $this->Model_conversar->fetch($usuario['id_grupo'],10,0 );
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
        $idMensagem = $this->Model_mensagem->salva($mensagem);

        // Send the HTTP request to the websockets server
        $ch = curl_init('http://localhost:3000');
        // It's POST
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        // we send JSON encoded data to the client
        $jsonData = json_encode([
            'nome' => 'Usuario:'+$usuario['id_usuario'],
            'conteudo' => $message
        ]);
        $query = http_build_query(['data' => $jsonData]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }

}