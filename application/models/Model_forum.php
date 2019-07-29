<?php

class Model_forum extends CI_Model{

	public function save_forum($forum){
        $this->db->insert("forum", $forum);
	}

	public function save_categoria($categoria){
        $this->db->insert("categoria", $categoria);
	}

	public function save_topico($topico){
        $this->db->insert("topico", $topico);
	}

	public function save_publicacao($publicacao){
        $this->db->insert("publicacao", $publicacao);
        return $this->db->insert_id();
	}



	public function listar_foruns(){
		$lista = $this->db->get('forum')->result_array();

		return $lista;
	}

	public function listar_categorias($id_forum){
		$lista = $this->db->get_where('categoria', array('id_forum' => $id_forum))->result_array();

		return $lista;
	}

	public function listar_topicos($id_categoria){
		$lista = $this->db->get_where('topico', array('id_categoria' => $id_categoria))->result_array();

		return $lista;
	}

	public function listar_pubs($id_topico){
		$lista = $this->db->get_where('publicacao', array('id_topico' => $id_topico))->result_array();

		return $lista;
	}



	public function get_Forum($id){
		$forum = $this->db->get_where('forum', array('id_forum' => $id))->result();

		return $forum;
	}

	public function get_Topico($id){
		$topico = $this->db->get_where('topico', array('id_topico' => $id))->result();

		return $topico;
	}

	public function get_Publicacao($id){
		$publicacao = $this->db->get_where('publicacao', array('id_publicacao' => $id))->result();

		return $publicacao;
	}


    public function gera_form_publicacao($action){
        $posts["form_open"] = form_open_multipart($action, 'class="ui reply form segment" id="formPost"');
        $posts["label_titulo"] = form_label("Título", "titulo");
        $posts["input_titulo"] = form_input(array("name" => "titulo", "id" => "titulo", "class" => "", "maxlength" => "80", "value" => set_value('titulo')));
        $posts["label_conteudo"] = form_label("Conteúdo da publicação", "conteudo");
        $posts["input_conteudo"] = form_textarea(array("name" => "conteudo", "id" => "editor", "class" => "", "maxlength" => "2555", "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        $posts["label_foto"] = form_label("<i class='ui paperclip icon'></i>Add. Foto", "adicionar-foto", array('class' => 'ui green left floated labeled icon button', 'id' => 'labelFoto', "onchange" => "previewFiles()"));
        $posts["button_confirm"] = form_button(array("content" => "<i class=\"icon edit\"></i> Postar", "class" => "confirm ui primary submit right floated labeled icon button"));
        $posts["button_submit"] = form_button(array("type" => "submit", "content" => "<i class=\"icon edit\"></i> Publicar", "class" => "ui  blue submit labeled icon button", "onclick" => "$('#formPost').submit()"));
        $posts["form_close"] = form_close();

        return $posts;
    }
}