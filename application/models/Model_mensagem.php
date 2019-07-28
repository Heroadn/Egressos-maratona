<?php

class Model_mensagem extends CI_Model
{

    public function salva($mensagem)
    {
        $this->db->insert("mensagem", $mensagem);
        return $this->db->insert_id();
    }

    public function fetch_data($limit, $start, $idGrupo)
    {
        return (array_reverse($this->db->query("SELECT nome, ultimo_nome, conteudo, data, mensagem.id_usuario,mensagem.id_mensagem, file_name FROM mensagem, usuario, midia, midia_usuario WHERE usuario_id_usuario = usuario.id_usuario AND midia.status_id_status = 1 AND midia_file_ID = file_ID AND mensagem.id_grupo = $idGrupo ORDER BY mensagem.id_mensagem DESC LIMIT $limit OFFSET $start")->result_array()));
    }
}