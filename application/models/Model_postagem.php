<?php

class Model_postagem extends CI_Model
{
    public function salva($post)
    {
        $this->db->insert("post", $post);
        return $this->db->insert_id();
    }

    public function buscaPosts($idGrupo, $qtdPosts=10){
        if($idGrupo == 0){
            return ($this->db->query("SELECT nome, ultimo_nome, id_post, post.descricao, titulo, data, post.id_usuario, post.id_status, midia.file_name FROM post, usuario, midia, midia_usuario WHERE post.id_usuario = usuario.id_usuario AND midia_usuario.usuario_id_usuario = usuario.id_usuario AND post.id_status = 1 AND post.id_grupo IS NULL ORDER BY post.data DESC LIMIT $qtdPosts")->result_array());
        }
        else {
            return ($this->db->query("SELECT nome, ultimo_nome, id_post, post.descricao, titulo, data, post.id_usuario, post.id_status, midia.file_name FROM post, usuario, midia, midia_usuario WHERE midia.status_id_status = 1 AND midia.file_ID = midia_usuario.midia_file_ID AND post.id_usuario = usuario.id_usuario AND midia_usuario.usuario_id_usuario = usuario.id_usuario AND post.id_grupo = $idGrupo")->result_array());
        }
    }

    public function postagemIntegra($idPost)
    {

        return ($this->db->query("SELECT nome, ultimo_nome, post.id_post, post.descricao, post.titulo,data, post.id_usuario, post.id_status, midia.file_name as foto_usuario_post FROM post, usuario, midia, midia_usuario WHERE midia.status_id_status = 1 AND midia.file_ID = midia_usuario.midia_file_ID  and post.id_usuario = usuario.id_usuario AND post.id_post = $idPost")->row_array());

    }

    public function qtd_posts($idGrupo)
    {
        if($idGrupo == 0){
            return ($this->db->query("SELECT id_post FROM post WHERE id_status = 1 and id_grupo IS NULL")->num_rows());
        }
        else {
            return ($this->db->query("SELECT id_post FROM post WHERE id_status = 1 and id_grupo = $idGrupo")->num_rows());
        }
    }


    public function countCurtidas($arrayPosts)
    {
        $postArray = array();
        foreach ($arrayPosts as $idPost) {
            $postArray[$idPost] = $this->db->query("SELECT id_curtida FROM curtidas WHERE id_status = 1 AND id_post = $idPost")->num_rows();
        }
        return $postArray;
    }

    public function insereCurtida($idUsuario, $idPost)
    {
        $data = date('Y-m-d H:i:s');
        $dados_curtida = array(
            "id_post" => $idPost,
            "id_user" => $idUsuario,
            "id_status" => 1,
            "data_like" => $data,
        );
        $this->db->insert('curtidas', $dados_curtida);
    }

    public function updateCurtida($idUser, $idPost)
    {
        $data = date('Y-m-d H:i:s');
        $dados_curtida = array(
            "id_user" => $idUser,
            "id_post" => $idPost,
            "id_status" => 3,
            "data_dislike" => $data,
        );
        $this->db->where('id_post', $idPost);
        $this->db->where('id_user', $idUser);
        $this->db->update('curtidas', $dados_curtida);
    }

    public function minhasCurtidas($idUser)
    {
        return ($this->db->query("SELECT id_post FROM curtidas WHERE id_user = $idUser AND id_status = 1 ORDER BY id_post ASC")->result_array());
    }

    public function verificaCurtida($idUser, $idPost)
    {
        $post = $this->db->query("SELECT id_status FROM curtidas WHERE id_user = $idUser AND id_post = $idPost ORDER BY id_status ASC")->row_array();
        if ($post == NULL) {
            $post['id_status'] = 0;
            return $post;
        } else {
            return $post;
        }
    }

    public function retornaArray($ids)
    {
        $arrayIds = explode(',', $ids);
        return $arrayIds;
    }

    public function fetch_data($limit, $start, $idGrupo)
    {
        if ($idGrupo == 0) {
            return ($this->db->query("SELECT nome, ultimo_nome, id_post, post.descricao, titulo, data, post.id_usuario, post.id_status, file_name FROM post, usuario, midia, midia_usuario WHERE post.id_usuario = usuario.id_usuario AND usuario_id_usuario = usuario.id_usuario AND midia_file_ID = file_ID AND midia.status_id_status = 1 AND post.id_status = 1 AND post.id_grupo IS NULL ORDER BY post.data DESC LIMIT $limit OFFSET $start")->result_array());
        }elseif($idGrupo != 0){
            return ($this->db->query("SELECT nome, ultimo_nome, id_post, post.descricao, titulo, data, post.id_usuario, post.id_status, file_name FROM post, usuario, midia, midia_usuario WHERE post.id_usuario=usuario.id_usuario AND usuario_id_usuario = usuario.id_usuario AND midia_file_ID = file_ID AND midia.status_id_status = 1 AND post.id_status = 1 AND post.id_grupo = $idGrupo  ORDER BY post.data DESC LIMIT $limit OFFSET $start")->result_array());
        }
    }

    public function buscarFotoPost($idPost){
        $where = "file_ID = midia_file_ID";
        $fotos = $this->db->where("post_id_post", $idPost)
            ->where($where)
            ->select("file_name")
            ->get("midia, midia_post")->result_array();
//        $fotos = $this->db->query("SELECT file_name FROM midia, midia_post WHERE post_id_post = $idPost AND file_ID = midia_file_ID;");
        $arrayFotos = Array();
        $totalFotos = count($fotos);
        $i = 0;
        if($fotos == NULL){
            $arrayFotos = 0;
        }else{
            while ($i < $totalFotos){
                $arrayFotos[$idPost][$i] = $fotos[$i]['file_name'];
                $i++;
            }
        }
        return $arrayFotos;
    }

    public function cadastroMidia($array){
        $array['status_id_status'] = 1;

        var_dump($array);
        die();
        $this->db->insert("midia", $array);
        return $this->db->insert_id();
    }

    public function cadastroPostMidia($idPost, $idMidia){
        $array = array(
            "midia_file_ID" => $idMidia,
            "post_id_post" => $idPost,
            "data_alteracao" => date("Y-m-d H:i:s"),
        );
        $this->db->insert("midia_post", $array);
    }


}