<?php

class Form extends CI_Controller {

    public function index(){

        $this->load->helper(array('form'));

        $this->load->library('form_validation');

        $this->form_validation->set_rules('nome', 'Nome de Usuário', 'required');
        $this->form_validation->set_rules('email', 'Email do Usuário', 'required');
        $this->form_validation->set_rules('senha', 'Senha do Usuário', 'required');
        $this->form_validation->set_rules('Rsenha', 'Confirmação de Senha', 'required');
        $this->form_validation->set_rules('turma', 'Turma do Egresso', 'required');
        $this->form_validation->set_rules('campus', 'Campus de Egresso', 'required');
        $this->form_validation->set_rules('ano', 'Ano do Egresso', 'required');
        $this->form_validation->set_rules('curso', 'Curso do Egresso', 'required');

        if ($this->form_validation->run() == FALSE)
        {

            $this->load->model("Model_cadastro");
            $retorno_turmas = $this->Model_cadastro->dadosCad('id_turma', 'turma', 'turma');

            foreach ($retorno_turmas as $turmas){

                $dados_turmas[''.$turmas["id_turma"].''] = $turmas['turma'];

            }

            $retorno_cursos = $this->Model_cadastro->dadosCad('id_curso', 'curso', 'curso');

            foreach ($retorno_cursos as $cursos){

                $dados_cursos[''.$cursos["id_curso"].''] = $cursos['curso'];

            }

            $retorno_campus = $this->Model_cadastro->dadosCad('id_campus', 'nome', 'campus');

            foreach ($retorno_campus as $campus){

                $dados_campus[''.$campus["id_campus"].''] = $campus['nome'];

            }

            $dados_cadastro['turmas'] = $dados_turmas;
            $dados_cadastro['cursos'] = $dados_cursos;
            $dados_cadastro['campus'] = $dados_campus;

            $this->load->view('base/header');
            $this->load->view('usuario/cadastro', $dados_cadastro);
            $this->load->view('base/footer');

        }else{

            redirect('PortalEgresso/');

        }

    }
}