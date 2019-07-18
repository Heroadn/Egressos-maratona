<?php
class Model_amigo extends CI_Model{

    public function enviaSolicitacao($solicitacao){

        $this->db->insert("amigos", $solicitacao);
        $row_array = $this->db->where("id_usuario1", $solicitacao['id_usuario1'])
                              ->where("id_usuario2", $solicitacao['id_usuario2'])
                              ->where("id_status", $solicitacao['id_status'])
                              ->select("id_amigos")
                              ->get("amigos")->row_array();

        $id_amigos = $row_array["id_amigos"];
        $usuario = $this->session->userdata("usuario_logado");

        $dados = array(
            'texto_notificacao' => $usuario['nome'].' '.$usuario['ultimo_nome'].' quer te adicionar como amigo!',
            'tipo_notificacao_id_tipo' => 1,
            'id_usuario_de' => $usuario['id_usuario'],
            'id_usuario_para' => $solicitacao['id_usuario2'],
            'id_origem' => $id_amigos,
            'id_status' => 7,
        );

        $this->db->insert("notificacao", $dados);

    }

    public function removerAmigo($solicitacao, $dados){

        $this->db->where('id_usuario_de', $solicitacao['id_usuario1'])
                 ->or_where('id_usuario_de', $solicitacao['id_usuario2'])
                 ->where('id_usuario_para', $solicitacao['id_usuario2'])
                 ->or_where('id_usuario_para', $solicitacao['id_usuario1'])
                 ->where('id_status', 8)
                 ->set('id_status', 3)
                 ->update("notificacao");

        $this->db->where('id_usuario1', $solicitacao['id_usuario1'])
                 ->or_where('id_usuario1', $solicitacao['id_usuario2'])
                 ->where('id_usuario2', $solicitacao['id_usuario2'])
                 ->or_where('id_usuario2', $solicitacao['id_usuario1'])
                 ->update("amigos", $dados);

    }

    public function recusarConvite($solicitacao){

        $this->db->where('id_origem', $solicitacao['id_origem'])
                 ->where('id_status', 7)
                 ->set('id_status', 9)
                 ->update("notificacao");

        $this->db->where('id_amigos', $solicitacao['id_origem'])
                 ->where('id_status', 7)
                 ->set('id_status', 9)
                 ->update("amigos");

    }

    public function buscarAmizades(){
        $userdata = $this->session->userdata("usuario_logado");
        $id_usuario = $userdata['id_usuario'];
        $this->db->select('id_usuario2');
        $this->db->where('id_usuario1', $id_usuario);
        $this->db->where('id_status', 7);

        $amigos = $this->db->get('amigos')->row_array();

        return $amigos;
    }

    public function buscarSolicitacoes($id_usuario2){
        $userdata = $this->session->userdata("usuario_logado");
        $id_usuario = $userdata['id_usuario'];
        $this->db->select('id_amigos')
                 ->where('id_usuario1', $id_usuario)
                 ->where('id_usuario2', $id_usuario2)
                 ->where('id_status', 7)
                 ->or_where('id_usuario2', $id_usuario)
                 ->where('id_usuario1', $id_usuario)
                 ->where('id_status', 7);

        $amigos = $this->db->get('amigos')->row_array();

        return $amigos['id_amigos'];
    }

    public function aceitaSolicitacao($solicitacao){
        $data = date('Y-m-d H:i:s');
        $this->db->where('id_origem', $solicitacao['id_amigos'])
                 ->where('id_status', 7)
                 ->set('id_status', 8)
                 ->update("notificacao");

        $dados = array(
            "id_status" => 8,
            "data_aceitado" => $data,
        );

        $this->db->where('id_amigos', $solicitacao['id_amigos'])
                 ->update('amigos', $dados);

    }
}