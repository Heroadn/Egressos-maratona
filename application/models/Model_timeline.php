<?php


class Model_timeline extends CI_Model
{
    public function gera_form($action){
        $posts["form_open"] = form_open_multipart($action, 'class="ui reply form segment" id="formPost"');
        $posts["label_titulo"] = form_label("Titulo", "titulo");
        $posts["input_titulo"] = form_input(array("name" => "titulo", "id" => "titulo", "class" => "", "maxlength" => "80", "value" => set_value('titulo')));
        $posts["label_conteudo"] = form_label("Conteudo da Postagem", "conteudo");
        $posts["input_conteudo"] = form_textarea(array("name" => "conteudo", "id" => "editor", "class" => "", "maxlength" => "2555", "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        $posts["input_upload"] = form_upload('file[]', null, array("multiple" => '', "id" => "adicionar-foto", "hidden" => "true", "accept" => "image/*", "onchange" => "previewFiles()"));
        $posts["label_foto"] = form_label("<i class='ui paperclip icon'></i>Add. Foto", "adicionar-foto", array('class' => 'ui green left floated labeled icon button', 'id' => 'labelFoto', "onchange" => "previewFiles()"));
        $posts["button_confirm"] = form_button(array("content" => "<i class=\"icon edit\"></i> Postar", "class" => "confirm ui primary submit right floated labeled icon button"));
        $posts["button_submit"]  = form_button(array("type" => "submit", "content" => "<i class=\"icon edit\"></i> Postar", "class" => "ui  blue submit labeled icon button", "onclick" => "$('#formPost').submit()"));
        $posts["form_close"] = form_close();

        return $posts;
    }

    public function gera_form_comentario($action){
        $posts["form_open"] = form_open($action, 'class="ui reply form" id="formPost"');
        $posts["label_conteudo"] = form_label("Conteudo do comentário", "conteudo");
        $posts["input_conteudo"] = form_textarea(array("name" => "conteudo", "id" => "editor", "class" => "", "maxlength" => "2555", "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        $posts["button_confirm"] = form_button(array("content" => "<i class=\"icon edit\"></i> Postar", "class" => "confirm ui primary submit right floated labeled icon button"));
        $posts["button_submit"] = form_button(array("type" => "submit", "content" => "<i class=\"icon edit\"></i> Postar", "class" => "ui  blue submit labeled icon button", "onclick" => "$('#formPost').submit()"));
        $posts["form_close"] = form_close();

        return $posts;
    }

    public function publicacaoComImagens($file, $idPost){
        $this->load->model("Model_postagem");
        foreach ($file as $f) {
            $image = array(
                'file_name' => base_url() . 'static/images/' . $f['file_name'],
                'file_size' => $f['file_size'],
                'data_insercao' => date("Y-m-d H:i:s"),
                "status_id_status" => 1,
            );
            $idMidia = $this->Model_postagem->CadastroMidia($image);
            $this->Model_postagem->cadastroPostMidia($idPost, $idMidia);
        }
    }

    public function cadastrarComentario($comentario, $idUsuario, $idPost){
        $cadastro = array(
            'comentario' => $comentario,
            'id_usuario' => $idUsuario,
            'id_post'    => $idPost,
            'data_comentario' => date("Y-m-d H:i:s"),
        );

        $this->db->insert("comentario", $cadastro);
    }

    public function buscarComentario($idPost){
        $comentario = $this->db->query("select usuario.nome, usuario.id_usuario, comentario, data_comentario, midia.file_name as foto_comentario 
                                        FROM comentario, usuario, midia, midia_usuario
                                        WHERE id_post = $idPost
                                        AND comentario.id_usuario = usuario.id_usuario
                                        AND midia_usuario.usuario_id_usuario = comentario.id_usuario
                                        and midia.file_ID = midia_usuario.midia_file_ID
                                        AND midia.status_id_status=1;")->result_array();
        return $comentario;
    }


}