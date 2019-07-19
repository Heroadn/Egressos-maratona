<?php
/**
 * Created by PhpStorm.
 * User: aluno
 * Date: 25/06/18
 * Time: 14:25
 */

class Usuario extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);

    }

    public function perfil()
    {
        $usuario = $this->session->userdata("usuario_logado");
        $usuario_oauth = $this->session->userdata("usuario_oauth");
        $this->load->model("Model_usuario");
        $id_turma = $usuario['id_turma'];
        $dados_usuario["file_name"] = $this->getCaminhoFoto($usuario["id_usuario"]);
        $this->load->model("Model_cadastro");

        $dados_usuario["form_open"] = form_open_multipart("Usuario/mudarFoto", 'class=""');
        $js = 'onchange="if(this.value != null){$(\'#labelFoto\').toggle();$(\'#enviar-foto\').removeAttr(\'style\');$(\'#cancelar-foto\').removeAttr(\'style\');console.log(1);}"';

        $dados_usuario["input_upload"] = form_upload(array("name" => "file", "id" => "selecionar-foto", "class" => "", "accept" => "image/*", "hidden" => "true",), '', $js);
//        $dados_usuario["input_upload"] = form_upload('file[]', null, array("id" => "selecionar-foto", "hidden" => "true", "accept" => "image/*",), '', $js);
        $dados_usuario["label_foto"] = form_label("Alterar Foto", "selecionar-foto", array('class' => 'ui small green inverted button', 'id' => 'labelFoto'));
        $dados_usuario["button_submit"] = form_button(array("type" => "submit", "id" => "enviar-foto", "content" => "Enviar", "class" => "ui small green inverted button", "style" => "display:none;"));
        $dados_usuario["anchor_cancelar"] = anchor('Usuario/perfil','Cancelar',array("href" => "Usuario/perfil", "id" => "cancelar-foto", "class" => "ui small red inverted button", "style" => "display:none;"));
        $dados_usuario["form_close"] = form_close();

        $dados_usuario["usuario"] = $usuario;
        $dados_usuario["turma"] = $this->Model_cadastro->dadosPerfil($id_turma, 'turma', 'turma', 'id_turma');
        $dados_usuario["curso"] = $this->Model_cadastro->dadosPerfil2($id_turma, 'curso, curso.id_curso', 'turma', 'curso', 'id_turma');
        $dados_usuario["campus"] = $this->Model_cadastro->dadosPerfil2($dados_usuario["curso"]['id_curso'], 'nome', 'curso', 'campus', 'id_curso');
        $dados_usuario["formacao"] = $this->Model_cadastro->dadosFormacao($usuario['formacao_academica']);
        $dados_usuario['label_busca_usuario'] = form_label("Buscar Usuário:", "buscarUsuario");
        $dados_usuario['input_buscar_usuario'] = form_input(array("name" => "BuscarUsuario", "id" => "buscarUsuario", "class" => "buscarUsuario", "value" => '', "maxlength" => "255", "placeholder" => "Buscar Usuário", "style" => "max-width:100%; word-wrap:break-word;"));
        $dados_usuario['url'] = current_url();
        $dados_usuario['base_url'] = base_url();
        $dados_usuario["username"] = $usuario["nome"];
        $idUsuario = $usuario["id_usuario"];
        $dados_usuario["membro_desde"] = $this->Model_usuario->buscaAnoDeIngresso($idUsuario);

        $dados_usuario["notificacoes"] = $this->Model_usuario->buscarNotificacoes();
        $dados_usuario["nr_notificacoes"] = strval(count($dados_usuario['notificacoes']));

        $dados_usuario["amigos"] = $this->Model_usuario->buscarAmigos();
        $dados_usuario["url_amigos"] = base_url("Usuario/perfilVisita");
        $dados_usuario["num_amigos"] = $this->Model_usuario->countAmigos();

        $this->twig->display('usuario/perfil', $dados_usuario);
    }

    public function autenticar()
    {

        $this->load->model("Model_usuario");
        $email = $this->input->post("email");
        $senha = $this->input->post("senha");

        $hash = $this->Model_usuario->buscarHash($email);
        $senha = crypt($senha, $hash);

        $usuario = $this->Model_usuario->logar($email, $senha);

        $form["form_open"] = form_open("Usuario/autenticar", 'class="ui form"');
        $form["label_email"] = form_label("Email", "email", 'class="color-label-brown"');
        $form["input_email"] = form_input(array("name" => "email", "id" => "email", "class" => "", "maxlength" => "255"));
        $form['label_senha'] = form_label("Senha", "senha", 'class="color-label-brown"');
        $form["password"] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "maxlength" => "255"));
        $form["button_submit"] = form_button(array("type" => "submit", "content" => "Entrar", "class" => "ui submit green button fluid color-button"));
        $form["form_close"] = form_close();

        if ($usuario) {

            if ($usuario["id_status"] == 2) {

                $form["usuario_logado"] = $this->session->userdata("usuario_logado");
                $this->session->set_flashdata('msg_login', "Valide seu email antes de logar em sua conta!");

                redirect("PortalEgresso");

            } else {

                $this->session->set_userdata("usuario_logado", $usuario);
                $form["msg_login"] = "Logado com Sucesso!";
                $form["classe_msg"] = "ui green message";
                $form["usuario_logado"] = $this->session->userdata("usuario_logado");

                redirect("Timeline/postagem");

            }
        } else {

            $form["usuario_logado"] = $this->session->userdata("usuario_logado");
            $this->session->set_flashdata('msg_login', "Email ou Senha invalidos!");

            redirect("PortalEgresso");

        }
    }

    public function validarEmail()
    {

        $token = $this->input->get('token');
        $this->load->model("Model_usuario");
        $resposta = $this->Model_usuario->verificaToken($token);

        if ($resposta) {

            $this->session->set_flashdata("msg_login", "Conta ativada!");
            $this->session->set_flashdata("classe", "ui centered green message");
            redirect('PortalEgresso');

        } else {

            $this->session->set_flashdata("msg_login", "Token Inválido!");
            $this->session->set_flashdata("classe", "ui centered red message");
            redirect('PortalEgresso');
        }

    }

    public function logout()
    {

        $this->session->unset_userdata("usuario_logado");
        $this->session->unset_userdata("usuario_oauth");
        $this->load->helper('cookie');
        delete_cookie('ci_session', 'localhost', '/');
        redirect('PortalEgresso/index');

    }

    public function editar()
    {
        $this->load->model("Model_usuario");
        $usuario = $this->session->userdata("usuario_logado");
        $form['form_open_editar'] = form_open("Usuario/editarPerfil", 'class="ui form"');
        $form['form_open_trocar_senha'] = form_open("Usuario/trocarSenha", 'class="ui form "');
        $form["label_nome"] = form_label("Nome", "nome");
        $form["input_nome"] = form_input(array("name" => "nome", "id" => "nome", "class" => "", "maxlength" => "255", "value" => '' . $usuario["nome"] . ''));
        $form["label_ultimo_nome"] = form_label("Sobrenome", "ultimo_nome");
        $form["input_ultimo_nome"] = form_input(array(" name" => "ultimo_nome", "id" => "ultimo_nome", "class" => "", "maxlength" => "255", "value" => '' . $usuario["ultimo_nome"] . ''));
        $form['label_email'] = form_label("Email", "email");
        $form['input_email'] = form_input(array("name" => "email", "id" => "email", "class" => "", "value" => '' . $usuario["email"] . '', "maxlength" => "255"));
        $form['label_facebook'] = form_label("Facebook", "Facebook");
        $form['input_facebook'] = form_input(array("name" => "facebook", "id" => "facebook", "class" => "", "value" => '' . $usuario["facebook"] . '', "maxlength" => "255"));
        $form['label_linkedin'] = form_label("LinkedIn", "LinkedIn");
        $form['input_linkedin'] = form_input(array("name" => "linkedin", "id" => "linkedin", "class" => "", "value" => '' . $usuario["linkedin"] . '', "maxlength" => "255"));
        $form['label_trabalho_atual'] = form_label("Trabalho Atual", "Trabalho Atual");
        $form['input_trabalho_atual'] = form_input(array("name" => "trabalho_atual", "id" => "trabalho_atual", "class" => "", "value" => '' . $usuario["trabalho_atual"] . '', "maxlength" => "255"));
        $form['label_formacao_academica'] = form_label("Formação Acadêmica", "Formação Acadêmica");
        $form['input_formacao_academica'] = form_input(array("name" => "formacao_academica", "id" => "formacao_academica", "class" => "", "value" => '' . $usuario["formacao_academica"] . '', "maxlength" => "255"));
        $form['label_senha'] = form_label("Senha", "senha");
        $form['input_senha'] = form_password(array("name" => "senha", "id" => "senha", "class" => "", "maxlength" => "255"));
        $form['label_senha_confirma'] = form_label("Confirmar Senha", "senhaconfirma");
        $form['input_senha_confirma'] = form_password(array("name" => "senhaconfirma", "id" => "senhaconfirma", "class" => "", "maxlength" => "255"));
        $form['form_button'] = form_button(array("type" => "submit", "content" => "Enviar", "class" => "ui green button color-button right floated"));
        $form['form_close'] = form_close();
        $form['erros_validacao'] = $this->session->flashdata('msg_erro');
        $form["file_name"] = $this->getCaminhoFoto($usuario["id_usuario"]);

        $usuario = $this->session->userdata("usuario_logado");
        $form["username"] = $usuario["nome"];

        $this->twig->display('usuario/editar', $form);
    }

    public function editarPerfil()
    {

        $this->load->model("Model_usuario");

        $this->form_validation->set_rules('nome', 'Nome', array('required', 'min_length[2]', 'max_length[50]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+$/] '));
        $this->form_validation->set_rules('ultimo_nome', 'Sobrenome', array('required', 'min_length[2]', 'max_length[50]', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ]+$/]'));
        $this->form_validation->set_rules('email', 'Email do Usuário', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {

            $form["erros_validacao"] = array(
                "erros_nome" => form_error('nome'),
                "erros_ultimo_nome" => form_error('ultimo_nome'),
                "erros_email" => form_error('email'),
            );

            $this->session->set_flashdata('msg_erro', $form["erros_validacao"]);

            redirect('Usuario/editar');

        } else {

            $nome = $this->input->post("nome");
            $ultimo_nome = $this->input->post("ultimo_nome");
            $email = $this->input->post("email");
            $facebook = $this->input->post("facebook");
            $linkedin = $this->input->post("linkedin");
            $trabalho_atual = $this->input->post("trabalho_atual");
            $formacao_academica = $this->input->post("formacao_academica");
            $usuario = $this->session->userdata("usuario_logado");
            $id = $usuario['id_usuario'];

            $usuario = $this->Model_usuario->editarPerfil($nome, $ultimo_nome, $email, $facebook, $linkedin, $trabalho_atual, $formacao_academica, $id);

            $this->session->unset_userdata("usuario_logado");
            $this->session->set_userdata("usuario_logado", $usuario);

            redirect('Usuario/perfil');

        }
    }

    public function trocarSenha()
    {
        $this->load->model('Model_usuario');
        $this->form_validation->set_rules('senha', 'Senha do Usuário', array('required', 'min_length[7]', array('numeroLetra', array($this->Model_usuario, 'numeroLetra')))); #array('required' => 'Você deve preencher a %s.') Configurar msg de erro
//        $this->form_validation->set_rules('senha', 'Senha do Usuário', 'regex_match[/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/]', array('regex_match' => 'A %s deve ter pelo menos uma Letra!'));
//        $this->form_validation->set_rules('senha', ' Senha do Usuário', 'regex_match[/^[0-9 ]+$/]', array('regex_match' => 'A %s deve ter pelo menos um Numero!'));
        $this->form_validation->set_rules('senhaconfirma', 'Confirmação de Senha', 'required|matches[senha]');
        if ($this->form_validation->run() == FALSE) {


            $form["erros_validacao"] = array(
                "erros_senha" => form_error('senha'),
                "erros_senhaconfirma" => form_error('senhaconfirma')
            );

            $this->session->set_flashdata('msg_erro', $form["erros_validacao"]);

            redirect('Usuario/editar');

        } else {

            $this->load->model("Model_usuario");

            $senha = $this->input->post("senha");
            $usuario = $this->session->userdata("usuario_logado");
            $id = $usuario['id_usuario'];

            $this->Model_usuario->trocarSenha($senha, $id);
            redirect('Usuario/perfil');

        }
    }

    public function mudarFoto()
    {
        $this->load->model("Model_usuario");
        $file = $this->custom_upload->single_upload('file', array(
            'upload_path' => 'static/images',
            'allowed_types' => 'jpg|jpeg|bmp|png|gif',
            'max_size' => '2048'// etc
        ));

        $image = array(
            'file_name' => base_url() . 'static/images/' . $file['file_name'],
            'file_size' => $file['file_size'],
            'data_insercao' => date("Y-m-d H:i:s"),
            'status_id_status' => 1,
        );

        $this->Model_usuario->trocarFoto($image);
        redirect('Usuario/Perfil');
    }


    public function autenticaOauth()
    {

        $usuario_oauth = $this->session->userdata("usuario_oauth");

        $this->load->model("Model_usuario");
        $usuario = $this->Model_usuario->loginOauth($usuario_oauth['email']);
        if ($usuario) {
            $this->session->set_userdata("usuario_logado", $usuario);
            redirect('Timeline/postagem');

        } else {

            $form["msg_login"] = "Erro inesperado!";
            $form["classe_msg"] = "ui centered red message";
            $form["usuario_logado"] = $this->session->userdata("usuario_logado");

            $this->twig->display('usuario/login', $form);
        }
    }

    public function getUsuario()
    {

        $nome = $this->input->post('nome');
        $this->load->model("Model_usuario");
        $usuario = $this->session->userdata("usuario_logado");
        print_r($this->Model_usuario->retornaUsuario($nome, $usuario['id_usuario']));
    }

    public function perfilVisita()
    {
        $id = $this->input->post('id_usuario');
        $usuario = $this->session->userdata("usuario_logado");

        if ($id == $usuario['id_usuario']) {
            redirect('Usuario/perfil');
        } else {

            $this->load->model("Model_usuario");
            $this->load->model("Model_cadastro");
            $usuario_id_curso = $this->Model_usuario->getIdCursoUsuario($usuario['id_usuario']);
            $usuario_id_campus = $this->Model_usuario->getIdCampusUsuario($usuario_id_curso);


            $perfilVisitado = $this->Model_usuario->buscarPerfil($id);
            $amigos = $this->Model_usuario->buscarAmigosNome($perfilVisitado['nome_completo']);
            $convite = $this->Model_usuario->buscarConviteAmigosNome($perfilVisitado['nome_completo']);
            if (count($amigos) == 1) {
                $dados_usuario["form_open_amigo"] = form_open("Amigo/removerAmigo", 'class="ui form"');
                $dados_usuario["button_submit_amigo"] = form_button(array("type" => "submit", "content" => "Desfazer Amizade", "class" => "ui bottom red button"));
            } elseif (count($convite) == 1) {
                $dados_usuario["form_open_amigo"] = form_open("Amigo/recusarConvite", 'class="ui form"');
                $dados_usuario["button_submit_amigo"] = form_button(array("type" => "submit", "content" => "Cancelar Convite", "class" => "ui bottom red button"));
            } else {
                $dados_usuario["form_open_amigo"] = form_open("Amigo/adicionarAmigo", 'class="ui form"');
                $dados_usuario["button_submit_amigo"] = form_button(array("type" => "submit", "content" => "Adicionar", "class" => "ui bottom green button"));
            }
            $jsBuscarUsuario = 'onkeyup="buscarUsuario()"';
            $dados_usuario["usuario"] = $perfilVisitado;
            $dados_usuario["id_usuario_amigo"] = $id;
            $dados_usuario["file_name_visita"] = $this->getCaminhoFoto($id);
            $dados_usuario["campus"] = $this->Model_cadastro->dadosPerfil($usuario_id_campus['id_campus'], 'nome', 'campus', 'id_campus');
            $dados_usuario["curso"] = $this->Model_cadastro->dadosPerfil($usuario_id_curso['id_curso'], 'curso', 'curso', 'id_curso');
            $dados_usuario["turma"] = $this->Model_cadastro->dadosPerfil($usuario['id_turma'], 'turma', 'turma', 'id_turma');
            $dados_usuario["formacao"] = $this->Model_cadastro->dadosFormacao($usuario['formacao_academica']);
            $dados_usuario['label_busca_usuario'] = form_label("Buscar Usuário:", "buscarUsuario");
            $dados_usuario['input_buscar_usuario'] = form_input(array("name" => "BuscarUsuario", "id" => "buscarUsuario", "class" => "buscarUsuario", "value" => '', "maxlength" => "255", "placeholder" => "", "style" => "max-width:100%; word-wrap:break-word;"), '', $jsBuscarUsuario);

//            $dados_usuario["notificacoes"] = $this->Model_usuario->buscarNotificacoes();
//            $dados_usuario["nr_notificacoes"] = strval(count($dados_usuario['notificacoes']));

            $dados_usuario["amigos"] = $this->Model_usuario->buscarAmigosVisita($id);
            $dados_usuario["num_amigos"] = $this->Model_usuario->CountAmigosVisita($id);
            $dados_usuario["url_amigos"] = base_url("Usuario/perfilVisita");

            $dados_usuario["username"] = $usuario["nome"];
            $idUsuario = $usuario["id_usuario"];
            $dados_usuario["file_name"] = $this->getCaminhoFoto($idUsuario);

            $this->twig->display('usuario/perfilvisita', $dados_usuario);

        }
    }

    public function getCaminhoFoto($idUser){
        $this->load->model("Model_usuario");
        $foto = $this->Model_usuario->buscarFoto($idUser);
        return $foto["file_name"];
    }
}