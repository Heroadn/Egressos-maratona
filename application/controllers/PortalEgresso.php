<?php
/**
 * Created by PhpStorm.
 * User: aluno
 * Date: 19/06/18
 * Time: 15:22
 */

class PortalEgresso extends CI_Controller{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);
        $this->load->library('Hybridauth');

        if (isset($usuario)){
            redirect('Usuario/perfil');
        }
    }

    public function index(){
        $providers = array();
        foreach ($this->hybridauth->HA->getProviders() as $provider_id => $params)
        {
            $providers[] = anchor("hauth/window/{$provider_id}", $provider_id);
        }
        $this->load->helper('cookie');
        delete_cookie('ci_session', 'localhost', '/');

        $form["form_open"] = form_open("Usuario/autenticar", 'class="ui form"');
        $form["label_email"] = form_label("Email","email",'class="color-label-brown"');
        $form["input_email"] = form_input(array("name" => "email", "id" => "email", "class" => "", "maxlength" => "255"));
        $form['label_senha'] = form_label("Senha","senha",'class="color-label-brown"');
        $form["password"] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "maxlength" => "255"));
        $form["button_submit"] = form_button(array("type" => "submit", "content" => "Entrar", "class" => "ui submit green button fluid color-button"));
        $form["form_close"] = form_close();
        $form['oauth_existente'] = $this->session->flashdata('oauth_existente');
        $form['classe_oauth_existente'] = $this->session->flashdata('classe_oauth_existente');
        $form['msg_login'] = $this->session->flashdata('msg_login');
        $form['msg_login'] = $this->session->flashdata('msg_login');
        $form['msg_login'] = $this->session->flashdata('msg_login');
        $form['msg_login'] = $this->session->flashdata('msg_login');
        $form['msg_login'] = $this->session->flashdata('msg_login');

        $form["usuario_logado"] = $this->session->userdata("usuario_logado");

        $form["providers"] = $providers;
        $form["recuperar_senha"] = '<a href="'.base_url().'PortalEgresso/RecuperarSenha">Recuperar senha</a>';
        $this->twig->display('usuario/login', $form);

    }

    public function cadastro($tipo = null){
        switch ($tipo) {
            case strtolower('Usuario'):
                $this->cadastrarUsuario();
                break;
            case strtolower('Empresa'):
                $this->cadastrarEmpresa();
                break;
            default:      
                $this->selecao();
                break;
        }
    }

    public function selecao(){;
        $this->twig->display('cadastro/selecao');
    }

    public function cadastrarUsuario(){
        $this->load->model("Model_cadastro");
        $this->load->model("Model_usuario");

        $this->form_validation->set_rules('nome', 'Nome', array('required', 'min_length[2]', 'max_length[50]', 'regex_match[/^[ A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+$/]'));
        $this->form_validation->set_rules('ultimo_nome', 'Sobrenome', array('required', 'min_length[2]', 'max_length[50]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+$/]'));
        $this->form_validation->set_rules('email', 'Email do Usuário', 'required|valid_email|is_unique[usuario.email]');
        $this->form_validation->set_rules('senha', 'Senha do Usuário', array('required', 'min_length[7]', array('numeroLetra', array($this->Model_usuario, 'numeroLetra')))); #array('required' => 'Você deve preencher a %s.') Configurar msg de erro
        $this->form_validation->set_rules('Rsenha', 'Confirmação de Senha', 'required|matches[senha]');
        $this->form_validation->set_rules('pergunta', 'Pergunta de Segurança', 'min_length[5]|max_length[80]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]');
        $this->form_validation->set_rules('resposta', 'Resposta', 'min_length[5]|max_length[800]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]');
        $this->form_validation->set_rules('turma', 'Turma do Egresso', 'required');
        $this->form_validation->set_rules('campus', 'Campus de Egresso', 'required');
        $this->form_validation->set_rules('ano', 'Ano do Egresso', 'required|exact_length[4]|numeric');
        $this->form_validation->set_rules('curso', 'Curso do Egresso', 'required');

        if ($this->form_validation->run() == FALSE)
        {
            $dados_campus = $this->Model_cadastro->selectCampus();
            $dados_cursos = '';
            $dados_turmas = '';

            $form["form_open"] = form_open("PortalEgresso/cadastro/usuario", 'class="ui form segment"');
            $form["label_nome"] = form_label("Nome","nome");
            $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => set_value('nome')));
            $form["label_ultimo_nome"] = form_label("Sobrenome","ultimo_nome");
            $form["input_ultimo_nome"] = form_input(array(" name" => "ultimo_nome", "id" => "ultimo_nome", "class" => "", "maxlength" => "255", "value" => set_value('ultimo_nome')));
            $form["label_email"] = form_label("Email","email");
            $form["input_email"] = form_input(array("name" => "email", "id" => "email", "class" => "", "maxlength" => "255", "value" => set_value('email')));
            $form["label_senha"] = form_label("Senha","Senha");
            $form["label_Rsenha"] = form_label("Repita a Senha","Rsenha");
            $form["input_senha"] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "maxlength" => "255"));
            $form["input_Rsenha"] = form_password(array("name" => "Rsenha", "id" => "Rsenha", "class" => "", "maxlength" => "255"));
            $form["label_turmas"] = form_label("Turma","turma");
            $form["dropdown_turmas"] = form_dropdown('turma', $dados_turmas, set_value('turma'), array('id' => 'idTurma'));
            $form["label_campus"] = form_label("Campus","campus");
            $form["dropdown_campus"] = form_dropdown('campus', $dados_campus, set_value('campus'), array('id' => 'idCampus'));
            $form["label_ano"] = form_label("Ano","ano");
            $form["input_ano"] = form_input(array("name" => "ano", "id" => "ano", "class" => "", "maxlength" => "255", "value" => set_value('ano')));
            $form["label_pergunta"] = form_label("Pergunta de Segurança", "pergunta");
            $form["input_pergunta"] = form_input(array("name" => "pergunta", "id" => "pergunta", "class" => "", "maxlength" => "255", "value" => set_value('pergunta')));
            $form["label_resposta"] = form_label("Resposta", "resposta");
            $form["input_resposta"] = form_textarea(array("name" => "resposta", "id" => "resposta", "class" => "", "maxlength" => "255", "value" => set_value('resposta')));
            $form["label_curso"] = form_label("Curso","curso");
            $form["dropdown_curso"] = form_dropdown('curso', $dados_cursos, set_value('curso'), array('id' => 'idCurso'));
            $form["label_termos"] = form_label("Ao clicar você aceita os Termos de uso e a Política de privacidade.","radioTermos");
            $form["checkbox_termos"] = form_checkbox(array("name" => "radioTermos", "class" => "checkbox"));
            $form["button_submit"] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated enviar", "disabled" => 'disabled'));
            $form["form_close"] = form_close();

            $form["erros_validacao"] = array(
                "erros_nome" => form_error('nome'),
                "erros_ultimo_nome" => form_error('ultimo_nome'),
                "erros_email" => form_error('email'),
                "erros_senha" => form_error('senha'),
                "erros_Rsenha" =>form_error('Rsenha'),
                "erros_pergunta" =>form_error('pergunta'),
                "erros_resposta" => form_error('resposta'),
                "erros_turma" => form_error('turma'),
                "erros_campus" => form_error('campus'),
                "erros_curso" => form_error('curso'),
                "erros_ano" => form_error('ano')
            );

            $this->twig->display('usuario/cadastro', $form);

        }else {
            $token = hash("ripemd160", $this->input->post("email"));
            $this->load->model("Model_usuario");
            $senha = $this->input->post("senha");
            $senha = $this->Model_usuario->bcrypt($senha);
            $resposta = $this->Model_usuario->bcrypt($this->input->post("resposta"));
            $usuario = array(
                "id_tipo_usuario" => 0,
                "nome" => $this->input->post("nome"),
                "ultimo_nome" => $this->input->post("ultimo_nome"),
                "email" => $this->input->post("email"),
                "senha" => $senha,
                "pergunta" => $this->input->post("pergunta"),
                "resposta" => $resposta,
                "id_status" => 2,
                "descricao" => "...",
                "data_criacao" => date("Y-m-d H:i:s"),
                "id_turma" => $this->input->post("turma"),
                "ano_egresso" => $this->input->post("ano"),
                "token" => $token
            );
            $this->Model_usuario->salva($usuario);

            $assunto = "Email de Verificação da Conta de: " . $usuario["nome"] . " - Portal Egressos";
            $mensagem = "Para ativar sua conta, clique no link a seguir:{unwrap}".base_url()."/Usuario/validarEmail?token=" . $token . "{/unwrap}";
            $this->Model_usuario->enviarEmail("hackthanos@acid-software.net", "Portal Egressos", $usuario['email'], $usuario['nome'], $assunto, $mensagem);

            $form["button_login"] = anchor("PortalEgresso", "<i class=\"sign out alternate ui icon\"></i>Login</a>", 'class="color-a-brown"');
            $form["usuario_logado"] = $this->session->userdata("usuario_logado");

            $this->twig->display('usuario/novo', $form);
        }
    }

    public function cadastrarEmpresa(){
        $this->load->model("Model_cadastro");
        $this->load->model("Model_usuario");

        $this->form_validation->set_rules('nome', 'Nome Fantasia', array('required', 'min_length[2]', 'max_length[50]', 'regex_match[/^[ A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+$/]'));
        $this->form_validation->set_rules('email', 'Email do Usuário', 'required|valid_email|is_unique[usuario.email]');
        $this->form_validation->set_rules('senha', 'Senha do Usuário', array('required', 'min_length[7]', array('numeroLetra', array($this->Model_usuario, 'numeroLetra')))); #array('required' => 'Você deve preencher a %s.') Configurar msg de erro
        $this->form_validation->set_rules('Rsenha', 'Confirmação de Senha', 'required|matches[senha]');
        $this->form_validation->set_rules('pergunta', 'Pergunta de Segurança', 'min_length[5]|max_length[80]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]');
        $this->form_validation->set_rules('resposta', 'Resposta', 'min_length[5]|max_length[800]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$% ]+$/]');

            if ($this->form_validation->run() == FALSE)
            {
                $dados_campus = $this->Model_cadastro->selectCampus();
                $dados_cursos = '';
                $dados_turmas = '';

                $form["form_open"] = form_open("PortalEgresso/cadastro/empresa", 'class="ui form segment"');
                $form["label_nome"] = form_label("Nome Fantasia","nome");
                $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => set_value('nome')));
                $form["label_email"] = form_label("Email","email");
                $form["input_email"] = form_input(array("name" => "email", "id" => "email", "class" => "", "maxlength" => "255", "value" => set_value('email')));
                $form["label_senha"] = form_label("Senha","Senha");
                $form["label_Rsenha"] = form_label("Repita a Senha","Rsenha");
                $form["input_senha"] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "maxlength" => "255"));
                $form["input_Rsenha"] = form_password(array("name" => "Rsenha", "id" => "Rsenha", "class" => "", "maxlength" => "255"));
                $form["label_pergunta"] = form_label("Pergunta de Segurança", "pergunta");
                $form["input_pergunta"] = form_input(array("name" => "pergunta", "id" => "pergunta", "class" => "", "maxlength" => "255", "value" => set_value('pergunta')));
                $form["label_resposta"] = form_label("Resposta", "resposta");
                $form["input_resposta"] = form_textarea(array("name" => "resposta", "id" => "resposta", "class" => "", "maxlength" => "255", "value" => set_value('resposta')));
                $form["label_termos"] = form_label("Ao clicar você aceita os Termos de uso e a Política de privacidade.","radioTermos");
                $form["checkbox_termos"] = form_checkbox(array("name" => "radioTermos", "class" => "checkbox"));

                //cnpj
                $form["label_cnpj"] = form_label("CNPJ", "cnpj");
                $form["input_cnpj"] = form_input(array("name" => "CNPJ", "id" => "cnpj", "class" => "", "maxlength" => "20"));

                //porte
                $form["label_porte"] = form_label("Qual porte de sua empresa?","checkbox_porte");
                $form["button_submit"] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated enviar", "disabled" => 'disabled'));
                $form["form_close"] = form_close();

                $form["erros_validacao"] = array(
                    "erros_nome" => form_error('nome'),
                    "erros_email" => form_error('email'),
                    "erros_senha" => form_error('senha'),
                    "erros_Rsenha" =>form_error('Rsenha'),
                    "erros_pergunta" =>form_error('pergunta'),
                    "erros_resposta" => form_error('resposta')
                );

                $this->twig->display('empresa/cadastro', $form);

            }else {
                $token = hash("ripemd160", $this->input->post("email"));
                $this->load->model("Model_usuario");
                $senha = $this->input->post("senha");
                $senha = $this->Model_usuario->bcrypt($senha);
                $resposta = $this->Model_usuario->bcrypt($this->input->post("resposta"));
                $usuario = array(
                    "id_tipo_usuario" => 1,
                    "nome" => $this->input->post("nome"),
                    "email" => $this->input->post("email"),
                    "senha" => $senha,
                    "porte" => $this->input->post("porte"),
                    "CNPJ" => $this->input->post("CNPJ"),
                    "municipio" => $this->input->post("CNPJ"),
                    "pergunta" => $this->input->post("pergunta"),
                    "resposta" => $resposta,
                    "id_status" => 2,
                    "descricao" => "...",
                    "data_criacao" => date("Y-m-d H:i:s"),
                    "token" => $token
                );
                $this->Model_usuario->salva($usuario);

                $assunto = "Email de Verificação da Conta de: " . $usuario["nome"] . " - Portal Egressos";
                $mensagem = "Para ativar sua conta, clique no link a seguir:{unwrap}".base_url()."/Usuario/validarEmail?token=" . $token . "{/unwrap}";
                $this->Model_usuario->enviarEmail("hackthanos@acid-software.net", "Portal Egressos", $usuario['email'], $usuario['nome'], $assunto, $mensagem);

                $form["button_login"] = anchor("PortalEgresso", "<i class=\"sign out alternate ui icon\"></i>Login</a>", 'class="color-a-brown"');
                $form["usuario_logado"] = $this->session->userdata("usuario_logado");

                $this->twig->display('usuario/novo', $form);

            }
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

    public function completarCadastro(){

        $this->form_validation->set_rules('turma', 'Turma do Egresso', 'required');
        $this->form_validation->set_rules('campus', 'Campus de Egresso', 'required');
        $this->form_validation->set_rules('ano', 'Ano do Egresso', 'required|min_length[4]|max_length[4]');
        $this->form_validation->set_rules('curso', 'Curso do Egresso', 'required');

        $this->form_validation->set_rules('pergunta', 'Pergunta de Segurança', 'min_length[10]|max_length[80]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$%? ]+$/]');
        $this->form_validation->set_rules('resposta', 'Resposta', 'min_length[10]|max_length[800]|required|regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ0-9,.!@#$%? ]+$/]');

        if ($this->form_validation->run() == FALSE) {

            $this->load->model("Model_cadastro");
            $dados_campus = $this->Model_cadastro->selectCampus();
            $dados_cursos = '';
            $dados_turmas = '';

            $form["form_open"] = form_open("PortalEgresso/completarCadastro", 'class="ui form segment"');
            $form["label_turmas"] = form_label("Turma","turma");
            $form["dropdown_turmas"] = form_dropdown('turma', $dados_turmas, set_value('turma'), array('id' => 'idTurma'));
            $form["label_campus"] = form_label("Campus","campus");
            $form["dropdown_campus"] = form_dropdown('campus', $dados_campus, set_value('campus'), array('id' => 'idCampus'));
            $form["label_ano"] = form_label("Ano","ano");
            $form["input_ano"] = form_input(array("name" => "ano", "id" => "ano", "class" => "", "maxlength" => "255", "value" => set_value('ano')));
            $form["label_curso"] = form_label("Curso","curso");
            $form["dropdown_curso"] = form_dropdown('curso', $dados_cursos, set_value('curso'), array('id' => 'idCurso'));
            $form["label_pergunta"] = form_label("Pergunta de Segurança", "pergunta");
            $form["input_pergunta"] = form_input(array("name" => "pergunta", "id" => "pergunta", "class" => "", "maxlength" => "255"));
            $form["label_resposta"] = form_label("Resposta", "resposta");
            $form["input_resposta"] = form_textarea(array("name" => "resposta", "id" => "resposta", "class" => "", "maxlength" => "255"));
            $form["label_termos"] = form_label("Ao clicar você aceita os Termos de uso e a Política de privacidade.","radioTermos");
            $form["checkbox_termos"] = form_checkbox(array("name" => "radioTermos", "class" => "checkbox"));
            $form["button_submit"] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated enviar", "disabled" => 'disabled'));
            $form["form_close"] = form_close();
            $usuario_oauth= $this->session->userdata("usuario_oauth");
            $this->twig->addGlobal('usuario_oauth', $usuario_oauth);

            $form["erros_validacao"] = array(
                "erros_turma" => form_error('turma'),
                "erros_campus" => form_error('campus'),
                "erros_curso" => form_error('curso'),
                "erros_ano" => form_error('ano'),
                "erros_pergunta" =>form_error('pergunta'),
                "erros_resposta" => form_error('resposta'),
            );

            $this->twig->display('usuario/completaCadastro', $form);

        }else {
            $usuario_oauth= $this->session->userdata("usuario_oauth");
            $redeSocial = $usuario_oauth["redeSocial"];
            $this->load->model("Model_usuario");
            if($usuario_oauth['description'] == ''){
                $description = '...';
            }
            else{
                $description = $usuario_oauth['description'];
            }
            $resposta = $this->Model_usuario->bcrypt($this->input->post("resposta"));
            $usuario = array(
                "id_tipo_usuario" => 0,
                "nome" => $usuario_oauth['firstName'],
                "ultimo_nome" => $usuario_oauth['lastName'],
                "email" => $usuario_oauth['email'],
                "senha" => 'vazio',
                "id_status" => 1,
                "descricao" => $description,
                "id_turma" => $this->input->post("turma"),
                "ano_egresso" => $this->input->post("ano"),
                "token" => '0',
                "oauth" => $usuario_oauth['identifier'],
                "pergunta" => $this->input->post("pergunta"),
                "resposta" => $resposta,
                "$redeSocial" => $usuario_oauth['profileURL']
            );
            $foto = $usuario_oauth['photoURL'];
            $this->Model_usuario->salva($usuario, $foto);
            redirect('Usuario/autenticaOauth');
        }

    }

    public function recuperarSenha(){
        $form['form_open_esqueceu_senha'] = form_open("PortalEgresso/recuperarSenhaPasso2", 'class="ui form"');
        $form['label_email'] = form_label("Email da conta","email");
        $form['input_email'] = form_input(array("name" => "email", "id" => "email", "class" => "", "placeholder" => "Digite seu email", "maxlength" => "255"));
        $form['form_button'] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated"));
        $form['form_close'] =  form_close();
        $form['msg_trocar_senha'] = $this->session->flashdata('msg_trocar_senha');
        $form['msg_nao_pode_trocar_senha'] = $this->session->flashdata('msg_nao_pode_trocar_senha');
        $form['classe_trocar_senha'] = $this->session->flashdata('classe_trocar_senha');
        $form['classe_nao_pode_trocar_senha'] = $this->session->flashdata('classe_nao_pode_trocar_senha');
        $this->twig->display('usuario/esqueceuSenha', $form);
    }

    public function recuperarSenhaPasso2(){
        $this->load->model("Model_usuario");
        $email = $this->input->post("email");
        $email_recuperar['email'] = $email;
        $verificar_email = $this->Model_usuario->verificarEmail($email);
        if($verificar_email == 1){
            $this->session->set_userdata("email_recuperar", $email_recuperar);
            $nome = $this->Model_usuario->buscarNome($email);
            $codigo = $this->Model_usuario->gerarCodigo();
            $codigo_bcrypt = $this->Model_usuario->bcrypt($codigo);
            $this->Model_usuario->salvarCodigo($email, $codigo_bcrypt);
            $assunto = "Email para recuperação de conta - Portal Egressos";
            $mensagem = "Para recuperar sua conta digite este código {unwrap}$codigo{/unwrap} no campo do formulário.";
            $this->Model_usuario->enviarEmail("hackathon@desenvolvedor.tech", "Portal Egressos", $email, $nome, $assunto, $mensagem);
            redirect("PortalEgresso/recuperarSenhaPasso3");
        }
        elseif($verificar_email == 2){
            $this->session->set_flashdata("msg_nao_pode_trocar_senha", "Não pode recuperar a senha com este email!");
            $this->session->set_flashdata("classe_nao_pode_trocar_senha", "ui centered red message");
            redirect('PortalEgresso/recuperarSenha');
        }else{
            $this->session->set_flashdata("msg_trocar_senha", "Email Inválido!");
            $this->session->set_flashdata("classe_trocar_senha", "ui centered red message");
            redirect('PortalEgresso/recuperarSenha');
        }
    }

    public function recuperarSenhaPasso3(){
        $form['form_open_codigo_senha'] = form_open("PortalEgresso/recuperarSenhaPasso4", 'class="ui form"');
        $form['label_codigo'] = form_label("Código recebido no email","codigoSenha");
        $form['input_codigo'] = form_input(array("name" => "codigoSenha", "id" => "codigoSenha", "class" => "", "placeholder" => "Digite o seu código", "maxlength" => "255"));
        $form['form_button'] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated"));
        $form['form_close'] =  form_close();
        $form['msg_trocar_senha3'] = $this->session->flashdata('msg_trocar_senha3');
        $form['classe_trocar_senha3'] = $this->session->flashdata('classe_trocar_senha3');
        $this->twig->display('usuario/recuperarSenha', $form);
    }

    public function recuperarSenhaPasso4(){
        $this->load->model("Model_usuario");
        $codigo = $this->input->post("codigoSenha");
        $email_recuperar = $this->session->userdata("email_recuperar");
        $codigo_base = $this->Model_usuario->buscarCodigo($email_recuperar['email']);
        $codigo_cryptado = crypt($codigo, $codigo_base);
        $form['msg_trocar_senha4'] = $this->session->flashdata('msg_trocar_senha4');
        $form['classe_trocar_senha4'] = $this->session->flashdata('classe_trocar_senha4');
        if($codigo == ''){
            $codigo_cryptado = $this->session->userdata("codigo_recuperar");
        }
        if ($codigo_cryptado == $codigo_base) {
            $this->session->set_userdata("codigo_recuperar", $codigo_cryptado);
            $pergunta = $this->Model_usuario->buscarPergunta($email_recuperar['email']);
            $form['form_open_pergunta_senha'] = form_open("PortalEgresso/recuperarSenhaPasso5", 'class="ui form"');
            $form['label_pergunta'] = form_label("$pergunta", "pergunta");
            $form['label_resposta'] = form_label("Sua resposta secreta para a pergunta", "resposta");
            $form['input_resposta'] = form_input(array("name" => "resposta", "id" => "resposta", "class" => "", "placeholder" => "Digite o seu código", "maxlength" => "255"));
            $form['form_button'] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated"));
            $form['form_close'] = form_close();
            $this->twig->display('usuario/recuperarSenha2', $form);
        }else {
            $this->session->set_flashdata("msg_trocar_senha3", "Código Inválido!");
            $this->session->set_flashdata("classe_trocar_senha3", "ui centered red message");
            redirect('PortalEgresso/recuperarSenhaPasso3');
        }
    }

    public function recuperarSenhaPasso5(){
        $this->load->model("Model_usuario");
        $resposta = $this->input->post("resposta");
        $email_recuperar = $this->session->userdata("email_recuperar");
        $resposta_base = $this->Model_usuario->buscarResposta($email_recuperar['email']);
        $resposta_cryptada = crypt($resposta, $resposta_base);
        $form['msg_trocar_senha5'] = $this->session->flashdata('msg_trocar_senha5');
        $form['classe_trocar_senha5'] = $this->session->flashdata('classe_trocar_senha5');
        if($resposta == ''){
            $resposta_cryptada = $this->session->userdata("resposta_recuperar");
        }
        if($resposta_cryptada == $resposta_base){
            $this->session->set_userdata("resposta_recuperar", $resposta_cryptada);
            $form['form_open_nova_senha'] = form_open("PortalEgresso/recuperarSenhaPasso6", 'class="ui form"');
            $form['label_senha'] = form_label("Nova senha","senha");
            $form['input_senha'] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "placeholder" => "Digite a nova senha", "maxlength" => "255"));
            $form['label_senha_confirma'] = form_label("Confirmar senha","senhaConfirma");
            $form['input_senha_confirma'] = form_password(array("name" => "senhaConfirma", "id" => "senhaConfirma", "class" => "", "placeholder" => "Confirmar senha", "maxlength" => "255"));
            $form['form_button'] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated"));
            $form['form_close'] =  form_close();
            $this->twig->display('usuario/recuperarSenha3', $form);
        }
        else {
            $this->session->set_flashdata("msg_trocar_senha4", "Resposta Inválida!");
            $this->session->set_flashdata("classe_trocar_senha4", "ui centered red message");
            redirect('PortalEgresso/recuperarSenhaPasso4');
        }
    }

    public function recuperarSenhaPasso6(){
        $this->load->model("Model_usuario");
        $senha = $this->input->post("senha");
        $senhaconfirma = $this->input->post("senhaConfirma");
        $email_recuperar = $this->session->userdata("email_recuperar");
        if($senha == $senhaconfirma){
            $this->Model_usuario->recuperarSenha($senha, $email_recuperar['email']);
            redirect('PortalEgresso');
        }
        else{
            $this->session->set_flashdata("msg_trocar_senha5", "Senhas não correspondem!");
            $this->session->set_flashdata("classe_trocar_senha5", "ui centered red message");
            redirect('PortalEgresso/recuperarSenhaPasso5');
        }
    }

    public function modal(){
        $this->twig->display('teste');
    }

    public function getUsuario(){

        $nome = $this->input->post('nome');
        $this->load->model("Model_usuario");
        $usuario = $this->session->userdata("usuario_logado");
        print_r($this->Model_usuario->retornaUsuario($nome, $usuario['id_usuario']));
    }
}