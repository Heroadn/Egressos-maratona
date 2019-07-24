<?php

class Model_grupo extends CI_Model{

    public function buscaGrupo(){

        $id_usuario = $this->session->userdata("usuario_logado")['id_usuario'];

        return($this->db->query("SELECT grupo.id_grupo, midia_grupo.midia_file_ID, midia.file_ID, grupo.nome, grupo.descricao, grupo.ano, midia.file_name from grupo, midia, usuario, midia_grupo where usuario.id_grupo = grupo.id_grupo and midia.file_ID = midia_grupo.midia_file_ID and midia_grupo.grupo_id_grupo = grupo.id_grupo and midia.status_id_status = 1 and usuario.id_usuario =  $id_usuario")->row_array());    }

    public function buscaMembros(){

        $id_grupo = $this->session->userdata("usuario_logado")['id_grupo'];

        return($this->db->query("SELECT nome, ultimo_nome, usuario.id_usuario, midia.file_name, descricao, usuario.id_status FROM usuario, midia, midia_usuario WHERE midia.status_id_status = 1 AND midia.file_ID = midia_usuario.midia_file_ID and midia.status_id_status = 1 and midia_usuario.usuario_id_usuario = usuario.id_usuario AND usuario.id_grupo = $id_grupo")->result_array());

    }

    public function buscaGrupos(){

        return($this->db->query("SELECT grupo.id_grupo, grupo.descricao FROM grupo ")->result_array());

    }

    public function cadastroMidiaGrupo($idGrupo, $idMidia){
        $array = array(
            "midia_file_ID" => $idMidia,
            "grupo_id_grupo" => (int)$idGrupo,
            "data_alteracao" => date("Y-m-d H:i:s"),
        );
        $this->db->insert("midia_grupo", $array);
    }
}

