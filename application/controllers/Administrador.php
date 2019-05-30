<?php

class Administrador extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);
        $this->load->model("Model_administrador");
    }

    public function painel()
    {

        $this->form_validation->set_rules('titulo', 'Titulo', array('required', 'min_length[5]', 'max_length[50]'));
        $this->form_validation->set_rules('conteudo', 'Conteudo', array('required',
                                                                        'min_length[10]',
                                                                        'max_length[500]')); #liberado caracteres especiais para teste

        if ($this->form_validation->run() == FALSE) {
            $usuario = $this->session->userdata("usuario_logado");
            $this->load->model("Model_usuario");
            $this->load->model("Model_grupo");
            $this->load->model("Model_timeline");

            $foto = $this->Model_usuario->buscarFoto($usuario['id_usuario']);
            $usuario['foto'] = $foto;
            $id_turma = $usuario['id_turma'];

            $this->load->model("Model_cadastro");
            $dados_usuario["file_name"] = $foto["file_name"];
            $dados_usuario["form_open"] = form_open_multipart("Usuario/mudarFoto", 'class=""');
            $js = 'onchange="if(this.value != null){$(\'#labelFoto\').toggle();$(\'#enviar-foto\').removeAttr(\'style\');}"';

            $dados_usuario["input_upload"] = form_upload(array("name" => "imagem",
                                                               "id" => "selecionar-foto",
                                                               "class" => "",
                                                               "accept" => "image/*",
                                                               "hidden" => "true"), '', $js);
            $dados_usuario["label_foto"] = form_label("Alterar Foto", "selecionar-foto", array('class' => 'ui small green inverted button',
                                                                                               'id' => 'labelFoto'));
            $dados_usuario["button_submit"] = form_button(array("type" => "submit",
                                                                "id" => "enviar-foto",
                                                                "content" => "Enviar",
                                                                "class" => "ui small red inverted button",
                                                                "style" => "display:none;"));
            $dados_usuario["form_close"] = form_close();
            $dados_usuario['label_busca_usuario'] = form_label("Buscar Usuário:", "buscarUsuario");
            $dados_usuario['input_buscar_usuario'] = form_input(array("name" => "BuscarUsuario",
                                                                      "id" => "buscarUsuario",
                                                                      "class" => "buscarUsuario prompt",
                                                                      "value" => '',
                                                                      "maxlength" => "255",
                                                                      "placeholder" => "Buscar Usuário"), '');
            $dados_usuario['label_busca_grupo'] = form_label("Buscar Grupo:", "buscarGrupo");
            $dados_usuario['input_buscar_grupo'] = form_input(array("name" => "BuscarGrupo",
                                                                    "id" => "buscarGrupo",
                                                                    "class" => "buscarGrupo prompt",
                                                                    "value" => '',
                                                                    "maxlength" => "255",
                                                                    "placeholder" => "Buscar Grupo"), '');
            $dados_usuario["usuario"] = $usuario;
            $dados_usuario["turma"] = $this->Model_cadastro->dadosPerfil($id_turma, 'turma', 'turma', 'id_turma');
            $dados_usuario["curso"] = $this->Model_cadastro->dadosPerfil2($id_turma, 'curso', 'turma', 'curso', 'id_turma');
            $dados_usuario["campus"] = $this->Model_cadastro->dadosPerfil2($id_turma, 'nome', 'curso', 'campus', 'id_curso');
            $dados_usuario["formacao"] = $this->Model_cadastro->dadosFormacao($usuario['formacao_academica']);


            $posts = $this->Model_timeline->gera_form("Administrador/painel");

            $dados_usuario["form_open_grupo"] = form_open("Administrador/painel", 'class="ui reply form segment"');
            $dados_usuario["label_titulo_grupo"] = form_label("Titulo", "titulo");
            $dados_usuario["input_titulo_grupo"] = form_input(array("name" => "titulo",
                                                                    "id" => "titulo",
                                                                    "class" => "",
                                                                    "maxlength" => "80",
                                                                    "value" => set_value('titulo')));
            $dados_usuario["label_conteudo_grupo"] = form_label("Conteudo da Postagem", "conteudo");
            $dados_usuario["input_conteudo_grupo"] = form_textarea(array("name" => "conteudo",
                                                                         "id" => "editor2",
                                                                         "class" => "",
                                                                         "maxlength" => "2555",
                                                                         "value" => set_value('conteudo')));
            $dados_usuario["button_submit_grupo"] = form_button(array("type" => "submit",
                                                                      "content" => "<i class=\"icon edit\"></i> Postar",
                                                                      "class" => "ui primary submit right floated labeled icon button"));
            $dados_usuario["form_close_grupo"] = form_close();

            $dados_usuario["form_open_relatorio"] = form_open("Administrador/gerarcsv", 'class="ui reply form segment"');


            $dados_campus = $this->Model_cadastro->selectCampus();
            $dados_cursos = '';
            $dados_turmas = '';
            $dados_usuario["label_campus"] = form_label("Campus","campus");
            $dados_usuario["dropdown_campus"] = form_dropdown('campus', $dados_campus, set_value('campus'), array('id' => 'idCampus'));
            $dados_usuario["label_curso"] = form_label("Curso","curso");
            $dados_usuario["dropdown_curso"] = form_dropdown('curso', $dados_cursos, set_value('curso'), array('id' => 'idCurso'));
            $dados_usuario["label_turmas"] = form_label("Turma","turma");
            $dados_usuario["dropdown_turmas"] = form_dropdown('turma', $dados_turmas, set_value('turma'), array('id' => 'idTurma'));
            $dados_usuario["label_todos"] = form_label("Todos");
            $dados_usuario["check_todos"] = form_checkbox('padrao', 'padrao', TRUE);
            $dados_usuario["check_nome"] = form_checkbox('Nome', 'on', FALSE);
            $dados_usuario["check_email"] = form_checkbox('Email', 'on', FALSE);
            $dados_usuario["check_campus"] = form_checkbox('Campus', 'on', FALSE);
            $dados_usuario["check_curso"] = form_checkbox('Curso', 'on', FALSE);
            $dados_usuario["check_turma"] = form_checkbox('Turma', 'on', FALSE);
            $dados_usuario["check_ano_egresso"] = form_checkbox('AnoEgresso', 'on', FALSE);
            $dados_usuario["button_submit_relatorio"] = form_button(array("type" => "submit",
                                                                          "content" => "<i class=\"icon edit\"></i> Gerar Relatório",
                                                                          "class" => "ui primary submit right floated labeled icon button"));
            $dados_usuario["form_close_relatorio"] = form_close();

            $retorno_grupos = $this->Model_grupo->buscaGrupos();

            foreach ($retorno_grupos as $grupos) {
                $dados_grupos['' . $grupos["id_grupo"] . ''] = $grupos['descricao'];
            }

            $dados_usuario["label_grupo"] = form_label("Grupo a ser Postado", "grupos");
            $dados_usuario["dropdown_grupo"] = form_dropdown('grupos', $dados_grupos, set_value('grupos'));

            $dados_usuario["nome"] = explode(" ", $usuario["nome"]);

            $dados_usuario["erros_validacao"] = array(
                "erros_titulo" => form_error('titulo'),
                "erros_conteudo" => form_error('conteudo'),
                "erros_grupos" => form_error('grupos')
            );

            $dados_usuario['qtd_bd_grupos'] = $this->qtdGrupo();
            $dados_usuario['qtd_bd_usuarios'] = $this->qtdUsuario();
            $posts += $dados_usuario;

            $this->twig->display('administrador/painel', $posts);

        } else {
            $this->load->model("Model_grupo");
            $usuario = $this->session->userdata("usuario_logado");
            $post = array(
                "titulo" => $this->input->post("titulo") . " [ADM]",
                "descricao" => $this->input->post("conteudo"),
                "id_status" => 1,
                "id_usuario" => $usuario['id_usuario'],
                "id_grupo" => $this->input->post("grupos")
            );

            $this->load->model("Model_postagem");

            $idPost = $this->Model_postagem->salva($post);

            $file = $this->custom_upload->multiple_upload('file', array(
                'upload_path' => 'static/images',
                'allowed_types' => 'jpg|jpeg|bmp|png|gif',
                'max_size' => '2048'
            ));
            $this->load->model("Model_timeline");
            $this->Model_timeline->publicacaoComImagens($file, $idPost);
            redirect('administrador/painel');
        }
    }
    public function gerarcsv(){
        $nome = "CSV_FILE_".date("YmdH_i_s".'csv');
        header('Content-type:text/csv');
        header('Content-Disposition: attachment;filename='.$nome);
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires:0');

        $this->load->model("Model_Administrador");
        $this->Model_administrador->infocsv();
    }
    public function getUsuario()
    {
        $nome = $this->input->post('nome');
        $limit = $this->input->post('limit');
        $start = $this->input->post('start');
        print_r($this->Model_administrador->retornaUsuario($nome,$limit,$start));
    }

    public function getGrupo()
    {
        $nome = $this->input->post('nome');
        $limit = $this->input->post('limit');
        $start = $this->input->post('start');
        print_r($this->Model_administrador->retornaGrupo($nome,$limit,$start));
    }

    public function banir()
    {

        $id_usuario = $this->input->post('id_usuario');
        if ($this->Model_administrador->banir($id_usuario) == "sucess") {
            redirect('' . base_url() . 'Administrador/Painel');
        }
    }

    public function desbanir()
    {

        $id_usuario = $this->input->post('id_usuario');
        if ($this->Model_administrador->desbanir($id_usuario) == "sucess") {
            redirect('' . base_url() . 'Administrador/Painel');
        }
    }

    public function enviarEmail()
    {
        $usuario = $this->session->userdata("usuario_logado");
        $this->load->model("Model_usuario");
        $email_para = $this->input->post('email_para');
        $nome_para = $this->input->post('nome_para');
        $assunto = $this->input->post('assunto');
        $conteudo = $this->input->post('conteudo');
        $nome_de = $usuario['nome'];
        $email_de = $usuario['email'];

        $this->Model_usuario->enviarEmail($email_de, $nome_de, $email_para, $nome_para, $assunto, $conteudo);

        redirect("" . base_url() . "Administrador/painel");

    }

    public function enviarEmailGrupo()
    {
        $usuario = $this->session->userdata("usuario_logado");
        $this->load->model("Model_usuario");
        $id_grupo = $this->input->post('id_grupo');
        $assunto = $this->input->post('assunto');
        $conteudo = $this->input->post('conteudo');
        $dados_usuarios = $this->Model_administrador->buscarDadosUsuario($id_grupo);
        $nome_de = $usuario['nome'];
        $email_de = $usuario['email'];
        foreach ($dados_usuarios as $item) {
            $this->Model_usuario->enviarEmail($email_de, $nome_de, $item['email'], $item['nome_completo'], $assunto, $conteudo);
        }
        $this->twig->display('administrador/enviandoEmail');
        redirect("" . base_url() . "Administrador/painel");
    }

    public function desativarGrupo(){

        $id_grupo = $this->input->post('id_grupo');
        if($this->Model_administrador->desativarGrupo($id_grupo) == "sucess") {
            redirect('' . base_url() . 'Administrador/Painel');
        }
    }

    public function reativarGrupo()
    {

        $id_grupo = $this->input->post('id_grupo');
        if ($this->Model_administrador->reativarGrupo($id_grupo) == "sucess") {
            redirect('' . base_url() . 'Administrador/Painel');
        }
    }

    public function formEmail()
    {

        $email = $this->input->post('email');
        $textArea = form_textarea(array("name" => "conteudo",
                                        "id" => "editor3",
                                        "class" => "",
                                        "maxlength" => "2555",
                                        "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        print_r($this->Model_administrador->enviarEmail($email, $textArea));
    }

    public function formEmailGrupo()
    {

        $idGrupo = $this->input->post('idGrupo');
        $textArea = form_textarea(array("name" => "conteudo",
                                        "id" => "editor3",
                                        "class" => "",
                                        "maxlength" => "2555",
                                        "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        print_r($this->Model_administrador->enviarEmailGrupo($idGrupo, $textArea));
    }


    public function criarFormulario()
    {
        $nomeFormulario = $this->input->post('nomeFormulario');
        $quantidadePergunta = $this->input->post('quantidadePergunta');
        $i = 0;
        while ($i <= $quantidadePergunta) {
            $perguntas[$i] = $this->input->post('perguntas' . $i . '');
        }

        $this->Model_Administrador->criarFormulario($nomeFormulario, $quantidadePergunta, $perguntas);
    }

    public function getCurso(){
        $this->load->model("Model_cadastro");

        $id_campus = $this->input->post('id_campus');
        echo $this->Model_cadastro->selectCurso($id_campus);
    }

    public function getTurma(){
        $this->load->model("Model_cadastro");

        $id_curso = $this->input->post('id_curso');
        echo $this->Model_cadastro->selectTurma($id_curso);
    }

    public function qtdGrupo(){
        $this->load->model("Model_administrador");
        $qtdBdGrupo = $this->Model_administrador->quantidadeGrupo();
        return $qtdBdGrupo;
    }
    public function qtdUsuario(){
        $this->load->model("Model_administrador");
        $qtdBdUsuario = $this->Model_administrador->quantidadeUsuario();
        return $qtdBdUsuario;
    }

    public function getCaminhoFoto($idUser){
        $this->load->model("Model_Usuario");
        $foto = $this->Model_usuario->buscarFoto($idUser);
        return $foto["file_name"];
    }

}