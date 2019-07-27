<?php

class Model_forum extends CI_Model{

	public function save_forum($forum){
        $this->db->insert("forum", $forum);
	}

	public function save_categoria($categoria){
        $this->db->insert("categoria", $categoria);
	}

	public function listar_foruns(){
		$lista = $this->db->get('forum')->result_array();

		return $lista;
	}

	public function listar_categorias($id_forum){
		$lista = $this->db->get_where('categoria', array('id_forum' => $id_forum))->result_array();

		return $lista;
	}

	public function get_Forum($id){
		$forum = $this->db->get_where('forum', array('id_forum' => $id))->result();

		return $forum;
	}



}