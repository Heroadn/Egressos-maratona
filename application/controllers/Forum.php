<?php

class Forum  extends CI_Controller{

    public function __construct(){
    	parent::__construct();

    	$usuario = $this->session->userdata("usuario_logado");
    	$this->twig->addGlobal('usuario_logado', $usuario);
		if (!isset($usuario)){
			redirect("/PortalEgresso/");
		}
    }

	public function index(){
		$this->load->model("Model_forum");

		$lista = $this->Model_forum->listar_foruns();

		$this->twig->display('forum/index', array('lista' => $lista));
	}

	public function ver($id){
		$this->load->model("Model_forum");

		$forum = $this->Model_forum->get_Forum($id);
		$categorias = $this->Model_forum->listar_categorias($id);

		$this->twig->display('forum/ver', array('forum' => $forum[0], 'categorias' => $categorias));	
	}

	public function cadastrar_categoria($id_forum=0){
		$this->load->model("Model_forum");

        $this->form_validation->set_rules('nome', 'Nome', array('required', 'min_length[3]', 'max_length[200]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/] '));

        if ($this->form_validation->run() == FALSE)
        {

        	if(!$id_forum){
        		redirect('/Forum/');
        	}

            $form["form_open"] = form_open("Forum/cadastrar_categoria", 'class="ui form segment"');
            $form["label_nome"] = form_label("Nome","nome");
            $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => set_value('nome')));
            $form["button_submit"] = form_button(array("type" => "submit", "content" => "Cadastrar", "class" => "ui green button color-button right floated enviar"));
            $form["form_close"] = form_close();

            $form["erros_validacao"] = array(
                "nome" => form_error('nome'),
                "descricao" => form_error('descricao'),
            );

            $form["id_forum"] = $id_forum;

            $this->twig->display('forum/cadastrar_categoria', $form);

        }else {
        	$categoria['nome'] = $this->input->post("nome");
        	$categoria['id_forum'] = $this->input->post("id_forum");

           $this->Model_forum->save_categoria($categoria);

            redirect('/Forum/ver/'.$categoria['id_forum']);
        }
	}

	public function cadastrar(){
		$this->load->model("Model_forum");

        $this->form_validation->set_rules('nome', 'Nome', array('required', 'min_length[3]', 'max_length[200]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/] '));
        $this->form_validation->set_rules('descricao', 'Descrição', array('required', 'min_length[1]', 'max_length[500]'));

        if ($this->form_validation->run() == FALSE)
        {

            $form["form_open"] = form_open("Forum/cadastrar", 'class="ui form segment"');
            $form["label_nome"] = form_label("Nome","nome");
            $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => set_value('nome')));
            $form["label_descricao"] = form_label("Descrição","descricao");
            $form["input_descricao"] = form_textarea(array("name" => "descricao", "id" => "descricao", "class" => "", "maxlength" => "255", "value" => set_value('Descrição')));
            $form["button_submit"] = form_button(array("type" => "submit", "content" => "Cadastrar", "class" => "ui green button color-button right floated enviar"));
            $form["form_close"] = form_close();

            $form["erros_validacao"] = array(
                "nome" => form_error('nome'),
                "descricao" => form_error('descricao'),
            );

            $this->twig->display('forum/cadastrar', $form);

        }else {
        	$forum['nome'] = $this->input->post("nome");
        	$forum['descricao'] = $this->input->post("descricao");
        	$forum['id_criador'] = $this->session->userdata("usuario_logado")['id_usuario'];

           $this->Model_forum->save_forum($forum);

            redirect('/Forum/');
        }
	}
}