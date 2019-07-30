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
        
        $cat_top = [];
        foreach ($categorias as $c){
            $c['topicos'] = $this->Model_forum->listar_topicos($c['id_categoria']);
            array_push($cat_top, $c);
        }

		$this->twig->display('forum/ver', array('forum' => $forum[0], 'categorias' => $cat_top));	
	}

    public function ver_topico($id){
        $this->load->model("Model_forum");

        $topico = $this->Model_forum->get_Topico($id);
        $pubs = $this->Model_forum->listar_pubs($id);

        $this->twig->display('forum/ver_topico', array('topico' => $topico[0], 'publicacoes' => $pubs));
    }

    public function ver_publicacao($id){
        $this->load->model("Model_forum");
        #$this->load->model("Model_postagem");

        $publicacao = $this->Model_forum->get_Publicacao($id);

        $this->twig->display('forum/ver_publicacao', array('publicacao' => $publicacao[0]));
    }

    public function cadastrar_pub($id_topico=0){

        if(!$id_topico){
            redirect("/Forum/");
        }

        $this->load->model("Model_forum");

        $this->form_validation->set_rules('titulo', 'Titulo', array('required', 'min_length[5]', 'max_length[50]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]'));
        $this->form_validation->set_rules('conteudo', 'Conteúdo', array('required', 'min_length[10]'));

        if ($this->form_validation->run() == FALSE){
            $action = "/Forum/cadastrar_pub/".$id_topico;
            $publicacao_form = $this->Model_forum->gera_form_publicacao($action);

            $publicacao_form["erros_validacao"] = array(
                "erros_titulo" => form_error('titulo'),
                "erros_conteudo" => form_error('conteudo')
            );

            $this->twig->display('forum/cadastrar_publicacao', $publicacao_form);
        }
        else{
            //$this->load->model("Model_timeline");

            $usuario = $this->session->userdata("usuario_logado");
            $publicacao = array(
                "titulo" => $this->input->post("titulo"),
                "conteudo" => $this->input->post("conteudo"),
                "data_publicacao" => date("Y-m-d H:i:s"),
                "id_usuario" => $usuario['id_usuario'],
                "id_topico" => $id_topico
            );

            $idPub = $this->Model_forum->save_publicacao($publicacao);

            /*
            $file = $this->custom_upload->multiple_upload('file', array(
                'upload_path' => 'static/images',
                'allowed_types' => 'jpg|jpeg|bmp|png|gif',
                'max_size' => '2048'
            ));

            $this->Model_timeline->publicacaoComImagens($file, $idPub);*/

            redirect('/Forum/ver_topico/'.$id_topico);
        }
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

    public function cadastrar_topico($id_forum=0, $id_categoria=0){
        if(!$id_forum || !$id_categoria){
            redirect('/Forum/');
        }

        $this->load->model("Model_forum");

        $this->form_validation->set_rules('nome', 'Nome', array('required', 'min_length[3]', 'max_length[200]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/] '));

        if ($this->form_validation->run() == FALSE){
            $form["form_open"] = form_open("Forum/cadastrar_topico/$id_forum/$id_categoria", 'class="ui form segment"');
            $form["label_nome"] = form_label("Nome","nome");
            $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => set_value('nome')));
            $form["button_submit"] = form_button(array("type" => "submit", "content" => "Cadastrar", "class" => "ui green button color-button right floated enviar"));
            $form["form_close"] = form_close();

            $form["erros_validacao"] = array(
                "nome" => form_error('nome')
            );

            $form["id_forum"] = $id_forum;

            $this->twig->display('forum/cadastrar_topico', $form);

        }else {
            $topico['nome'] = $this->input->post("nome");
            $topico['id_categoria'] = $id_categoria;

           $this->Model_forum->save_topico($topico);

            redirect('/Forum/ver/'.$id_forum);
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