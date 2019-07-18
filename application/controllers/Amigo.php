<?php
/**
 * Created by PhpStorm.
 * User: kammer
 * Date: 24/09/18
 * Time: 14:01
 */

class Amigo extends CI_Controller{

    public function index(){

        redirect('PortalEgressos');

    }

    public function adicionarAmigo(){

        $this->load->model("Model_amigo");
        $usuario = $this->session->userdata("usuario_logado");

        $solicitacao = array(

            "id_usuario1" => $usuario['id_usuario'],
            "id_usuario2" => $this->input->post("id_amigo"),
            "id_status" => 7,
        );
        $this->Model_amigo->enviaSolicitacao($solicitacao);

        redirect('Usuario/perfil');

    }

    public function removerAmigo(){

        $this->load->model("Model_amigo");
        $usuario = $this->session->userdata("usuario_logado");
        $data = date('Y-m-d H:i:s');
        $solicitacao = array(

            "id_usuario1" => $usuario['id_usuario'],
            "id_usuario2" => $this->input->post("id_amigo"),
        );

        $dados = array(
            "id_status" => 3,
            "data_termino" => $data,
        );
        print_r($solicitacao);
        $this->Model_amigo->removerAmigo($solicitacao, $dados);

        redirect('Usuario/perfil');

    }

    public function recusarConvite(){

        $this->load->model("Model_amigo");
        $usuario = $this->session->userdata("usuario_logado");
        $idAmigos = $this->input->post("id_amigos");
        $idAmigo = $this->input->post("id_amigo");
        if(!empty($idAmigo)) {
            $id_origem = $this->buscaIdSolicitacao($idAmigo);
        }else{
            $id_origem = $idAmigos;
        }

        $solicitacao = array(
            "id_origem" => $id_origem,
        );
        $this->Model_amigo->recusarConvite($solicitacao);

        redirect('Usuario/perfil');

    }

    public function aceitarSolicitacao(){

        $this->load->model("Model_amigo");

        $solicitacao = array(

            "id_amigos" => $this->input->post("id_amigos"),

        );

        $this->Model_amigo->aceitaSolicitacao($solicitacao);

        redirect('Usuario/perfil');
    }

    public function buscaIdSolicitacao($idUsuario2){
        $this->load->model("Model_amigo");
        return $this->Model_amigo->buscarSolicitacoes($idUsuario2);
    }
}