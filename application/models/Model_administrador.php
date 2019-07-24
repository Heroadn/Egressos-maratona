<?php

class Model_administrador extends CI_Model{

    public function buscarUsuario($nome, $limit, $start){
        return $this->db
            ->like('nome_completo', $nome, 'both')
            ->select('nome_completo, email, id_status ,id_usuario, id_turma')
            ->order_by('nome_completo')
            ->limit($limit, $start)
            ->get('usuario');
    }

    public function buscarGrupo($nome, $limit, $start){
        return $this->db
            ->like('nome', $nome, 'both')
            ->select('nome, descricao, id_grupo , ano, id_status')
            ->order_by('nome')
            ->limit($limit, $start)
            ->get('grupo');
    }

    public function countBuscarUsuario($nome,$limit,$start){
        return $this->db
            ->like('nome_completo', $nome, 'both')
            ->select('nome_completo')
            ->limit($limit, $start)
            ->get('usuario')->num_rows();
    }

    public function countBuscarGrupo($nome,$limit,$start){
        return $this->db
            ->like('nome', $nome, 'both')
            ->select('nome, descricao, id_grupo , ano, id_status')
            ->limit($limit, $start)
            ->get('grupo')->num_rows();
    }

    public function quantidadeUsuario(){
        return $this->db
            ->select('id_usuario')
            ->get('usuario')->num_rows();
    }

    public function quantidadeGrupo(){
        return $this->db
            ->select('id_grupo')
            ->get('grupo')->num_rows();
    }

    public function buscarDadosUsuario($idGrupo){
        return $this->db
            ->where('id_grupo', $idGrupo)
            ->select('nome_completo, email')
            ->get('usuario')->result_array();
    }


    public function retornaUsuario($nome,$limit,$start)
    {
        $alunos = '';
        $qtdBanco = $this->quantidadeUsuario();
        $qtdBuscaBanco = $this->countBuscarUsuario($nome, $limit, $start);
        $aluno = $this->buscarUsuario($nome,$limit,$start);
        if ($qtdBuscaBanco != 0 and $start <= $qtdBanco) {
        foreach($aluno -> result() as $item) {
            $this->load->model('Model_usuario');
            $this->load->model('Model_cadastro');
            $foto = $this->Model_usuario->buscarFoto($item->id_usuario);
            $turma = $this->Model_cadastro->dadosPerfil($item->id_turma, 'turma', 'turma', 'id_turma');
            $curso = $this->Model_cadastro->dadosPerfil2($item->id_turma, 'curso, curso.id_curso', 'turma', 'curso', 'id_turma');
            $campus = $this->Model_cadastro->dadosPerfil2($curso['id_curso'], 'nome', 'curso', 'campus', 'id_curso');
            $alunos .= "<div class='ui container segment Usuario-card' id='" . $item->id_usuario . " width='100%' style='box-shadow: 3px 3px 10px #E6E6E6;'>
                            <div class='ui people'>
                                <div class='sides'>
                                    <div class='active side'>
                                            <div class='ui grid'>
                                                <div class='centered five wide mobile five wide tablet five wide computer column'>
                                                    <div class='image'>
                                                        <img src='" . $foto['file_name'] . "' width='100%' height='auto' alt='imagem do usuario'>
                                                    </div>
                                                </div>
                                                <div class='centered sixteen wide mobile sixteen wide tablet sixteen wide computer column'>
                                                    <div class='column'>
                                                        <div class='content'>
                                                            <div class='header'>" . $item->nome_completo . "</div>
                                                        </div>
                                                        <div class='extra content'>
                                                          <a>
                                                            <p>" . $campus['nome']. "</p>
                                                            <p>" . $curso['curso']. "</p>
                                                            <p>" . $turma['turma']. "</p>
                                                            <p class='emailAluno'>" . $item->email . "</p>
                                                          </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class='ui grid'>
                                                    <div class='seven wide mobile seven wide tablet eight wide computer column'>";
                if ($item->id_status == 3) {

                    $alunos .= $this->formDesbanirUsuario($item);

                } else {

                    $alunos .= $this->formBanirUsuario($item);

                }
                $alunos .= "
                                                    </div>
                                                    " . $this->buttonEnviarEmailUsuario($item) . "
                                                </div>
                                            
                                        </div>    
                                    </div>
                                </div>
                            </div>
                         </div>   
                                ";
            }
            $alunos .="<script src='".base_url()."static/scriptsAjax.js'></script>";
            $alunos .= "<script>
                        $('#buscarUsuarioView').scroll(function(){
                            console.log($('#buscarUsuarioView').scrollTop())
                        }";
            foreach($aluno -> result() as $item) {
                $alunos .= "
               
                if ( $('#buscarUsuarioView').scrollTop() > $('#" . $item->id_usuario . "').offset().top) {
                                        console.log('" . $item->nome_completo . " foi visto!');
                                      };";

            }
            $alunos .="});</script>";
            return $alunos;
        }else{
            if($start > 0) {
                $alunos .= "<h4 class=\"ui header center aligned Usuario-null\" id=\"inactive\"><br>Não foram encontrados mais resultados</h4>";

                return $alunos;
            }else {
                $alunos .= "<h4 class=\"ui header center aligned Usuario-null\" id=\"inactive\"><br>Digite um nome válido</h4>";

                return $alunos;
            }
        }
    }

    public function retornaGrupo($nome, $limit, $start)
    {
        $grupos = '';
        $qtdBanco = $this->quantidadeGrupo();
        $qtdBuscaBanco = $this->countBuscarGrupo($nome, $limit, $start);
        $grupo = $this->buscarGrupo($nome, $limit, $start);
        if ($qtdBuscaBanco != 0 and $start <= $qtdBanco) {
            foreach ($grupo->result() as $item) {
                $foto = $this->db->query("select midia.file_name from midia, midia_grupo where midia.file_ID = midia_grupo.midia_file_ID and midia_grupo.grupo_id_grupo = $item->id_grupo")->row_array();
                $grupos .= "
                    <br>
                    <div class='ui segment container Grupo-card' width='100%' style='box-shadow: 3px 3px 10px #E6E6E6;'>
                        <div class='ui people'>
                            <div class='sides'>
                                <div class='active side'>
                                        <div class='ui grid'>
                                            <div class='four wide mobile sixteen wide tablet sixteen wide computer column'>
                                                <div class='image' >
                                                    <img src='" . $foto['file_name'] . "' width='200px' height='auto' alt='imagem do grupo'>
                                                </div>
                                            </div>
                                            <div class='sixteen wide mobile sixteen wide tablet sixteen wide computer column'>
                                                <div class='content'>
                                                    <h4 class='header'>" . $item->nome . "</h4>
                                                </div>
                                                <div class='extra content'>
                                                  <a>
                                                  </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class='ui grid'>
                                            <div class='sixteen wide mobile sixteen wide tablet eight wide computer column'>";
                if ($item->id_status == 3) {

                    $grupos .= $this->formReativaGrupo($item);

                } else {
                    $grupos .= $this->formDesativaGrupo($item);

                }
                $grupos .= "
                                            </div>
                                           " . $this->formEnviarEmailGrupo($item) . "
                                        </div> 
                                </div>    
                            </div>
                        </div>
                    </div>";
            }
            $grupos .= "<script src='" . base_url() . "static/scriptsAjax.js'></script>
                   <script src=\"https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js\"></script>
                   <script src='" . base_url() . 'static/editor.js.' . "'></script>";

            return $grupos;
        }else{
            if($start > 0) {
                $grupos .= "<h4 class=\"ui header center aligned Grupo-null\" id=\"inactive\"><br>Não foram encontrados mais resultados</h4>";

                return $grupos;
            }else {
                $grupos .= "<h4 class=\"ui header center aligned Grupo-null\" id=\"inactive\"><br>Digite um nome válido</h4>";

                return $grupos;
            }
        }
    }

    public function pegarCampus(){

        return $this->db
            ->order_by('nome')
            ->get('campus');
    }

    public function selectCampus(){
        $options = "<option value='' selected='selected'>Selecione o Campus</option>";

        $campus = $this->pegarCampus();

        foreach($campus -> result() as $item){
            $options .= "<option value='{$item->id_campus}'>{$item->nome}</option>";
        }

        return $options;
    }
    public function pegarCurso($id_campus = null){

        return $this->db
            ->where('id_campus', $id_campus)
            ->order_by('curso')
            ->get('curso');
    }

    public function selectCurso($id_campus = null){
        $options = "<option value='' selected='selected'>Selecione o Curso</option>";

        $curso = $this->pegarCurso($id_campus);

        foreach($curso -> result() as $item){
            $options .= "<option value='{$item->id_curso}'>{$item->curso}</option>";
        }

        return $options;
    }

    public function pegarTurma($id_curso = null){

        return $this->db
            ->where('id_curso', $id_curso)
            ->order_by('turma')
            ->get('turma');
    }

    public function selectTurma($id_curso = null){
        $options = "<option value='' selected='selected'>Selecione a Turma</option>";

        $turma = $this->pegarTurma($id_curso);

        foreach($turma -> result() as $item){
            $options .= "<option value='{$item->id_turma}'>{$item->turma}</option>";
        }

        return $options;
    }
    public function banir($idUsuario){
        if($this->db->set('id_status', 3)
                ->where('id_usuario', $idUsuario)
                ->update("usuario") == 0){
            return "failed";
        }
        else{
            return "sucess";
        }
    }

    public function desbanir($idUsuario){
        if($this->db->set('id_status', 1)
                ->where('id_usuario', $idUsuario)
                ->update("usuario") == 0){
            return "failed";
        }
        else{
            return "sucess";
        }
    }

    public function desativarGrupo($idGrupo){
        if($this->db->set('id_status', 3)
                ->where('id_grupo', $idGrupo)
                ->update("grupo") == 0){
            return "failed";
        }
        else{
            return "sucess";
        }
    }

    public function reativarGrupo($idGrupo){
        if($this->db->set('id_status', 1)->where('id_grupo', $idGrupo)->update("grupo") == 0){
            return "failed";
        }
        else{
            return "sucess";
        }
    }

    public function enviarEmail($email,$textArea){
        return  "
          
            <div class='sixteen column'>
                <form class='ui form segment' action='" . base_url() . "Administrador/enviarEmail' method='POST'>
                    <input type='hidden' value='' name='nome_para' required>
                    <div class='row'>
                        <input type='email' value='".$email."' name='email_para' required>
                    </div>
                    <br />
                    <div class='row'>
                        <input type='text' placeholder='Assunto' name='assunto' required>
                    </div>
                    <br />
                    <div class='row'>
                        ".$textArea."
                    </div>
                    <br />
                    <button class='ui green basic button' type='submit'>Enviar</button>
                </form>
            </div>
            
            <script src='https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js'></script>
            <script src='".base_url()."static/editor.js'></script>";
    }

    public function enviarEmailGrupo($idGrupo,$textArea){
        return "
            <form class='ui form segment' action='".base_url()."Administrador/enviarEmailGrupo' method='POST'>
                <input type='hidden' value='".$idGrupo."' name='id_grupo' required>
                <div class='row'>
                    <input type='text' placeholder='Assunto' name='assunto' required>
                </div>
                <br />
                <div class='row'>
                    ".$textArea."
                </div>
                <br />
                <button class='ui green basic button' type='submit'>Enviar</button>
                
            </form>
            <script src='https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js'></script>
            <script src='".base_url()."static/editor.js'></script>";
    }

    public function criarFormulario($idGrupo, $nomeFormulario, $quantidadePerguntas, $perguntas){
        $novo_formulario = array(
            'nome' => $nomeFormulario,
            'id_grupo' => 1,
        );
        $this->db->insert("formulario", $novo_formulario);

        $formulario = $this->db->where(''.$nomeFormulario.'')
            ->select('id_formulario')
            ->get('formulario');
        foreach ($perguntas as $pergunta){
            $nova_pergunta = array(
                'nome' => $pergunta,
                'id_formulario' => $formulario,
            );
            $this->db->insert("perguntas", $nova_pergunta);
        }
    }

    public function formDesbanirUsuario($item){
        return "<form action='" . base_url() . "Administrador/desbanir' method='POST'>
                        <input type='hidden' value='" . $item->id_usuario . "' name='id_usuario'>
                        <button class='ui yellow basic button centered' type='submit'>Desbanir</button>
                   </form>";
    }

    public function formBanirUsuario($item){
        return "<form action='" . base_url() . "Administrador/banir' method='POST'>
                        <input type='hidden' value='" . $item->id_usuario . "' name='id_usuario'>
                        <button class='ui red basic button' type='submit'>Banir</button>
                   </form>";
    }

    public function buttonEnviarEmailUsuario($item){
        return "<div class='nine wide mobile nine wide tablet eight wide computer column'>
                    <a href='#formEmail'><button class='ui blue basic button title enviarEmail align-center' value='". $item->email ."'>
                    Enviar email
                    </button></a>
               </div>";
    }

    public function formReativaGrupo($item){
        return "<form action='" . base_url() . "Administrador/reativarGrupo' method='POST'>
                    <input type='hidden' value='" . $item->id_grupo . "' name='id_grupo'>
                    <button class='ui yellow basic button' type='submit'>Reativar Grupo</button>
                </form>";
    }

    public function formDesativaGrupo($item){
        return "<form action='" . base_url() . "Administrador/desativarGrupo' method='POST'>
                    <input type='hidden' value='" . $item->id_grupo . "' name='id_grupo'>
                    <button class='ui red basic button' type='submit'>Desativar Grupo</button>
                </form>";
    }

    public function formEnviarEmailGrupo($item) {
        return "<div class='sixteen wide mobile sixteen wide tablet eight wide computer column '>
                    <a href='#formEmail'>
                        <button class='ui blue basic button enviarEmailGrupo' value='" . $item->id_grupo . "'>
                        Enviar email
                        </button>
                     </a>
                </div>";
    }

    public function infocsv(){
        $handle = fopen('php://output', 'w');

        $campos = array();
        if($this->input->post("padrao")){
            array_push($campos, 'Nome Completo', 'Email', 'Campus', 'Curso', 'Turma');

            $this->db->select('nome_completo, email, campus.nome, curso, turma');
            $this->db->from('usuario');
            $this->db->join('turma', 'turma.id_turma = usuario.id_turma');
            $this->db->join('curso', 'curso.id_curso = turma.id_curso');
            $this->db->join('campus', 'campus.id_campus = curso.id_campus');

            if($this->input->post("campus")){
                $campus = $this->input->post("campus");
                $this->db->where('curso.id_campus', $campus);
                $this->db->where('campus.id_campus', $campus);
            }
            if($this->input->post("curso")){
                $curso = $this->input->post("curso");
                $this->db->where('turma.id_curso', $curso);
                $this->db->where('curso.id_curso', $curso);
            }
            if($this->input->post("turma")){
                $turma = $this->input->post("turma");
                $this->db->where('usuario.id_turma', $turma);
                $this->db->where('turma.id_turma', $turma);
            }

        }
        else{

            $mapa_input_para_coluna = array(
                'Nome' => 'nome_completo',
                'Email' => 'email',
                'Campus' => 'campus.nome',
                'Curso' => 'curso',
                'Turma' => 'turma',
                'AnoEgresso' => 'ano_egresso'
            );

            $selecionados = (array_filter($mapa_input_para_coluna, function($input){
                return array_key_exists($input, $_POST) && $_POST[$input] === 'on';
            }, ARRAY_FILTER_USE_KEY));

            $campos = array_keys($selecionados);
            $consulta = implode(array_values($selecionados), ',');

            $this->db->select($consulta);
            $this->db->from('usuario');
            $this->db->join('turma', 'turma.id_turma = usuario.id_turma');
            $this->db->join('curso', 'curso.id_curso = turma.id_curso');
            $this->db->join('campus', 'campus.id_campus = curso.id_campus');

            if($this->input->post("campus")){
                $campus = $this->input->post("campus");
                $this->db->where('curso.id_campus', $campus);
                $this->db->where('campus.id_campus', $campus);
            }
            if($this->input->post("curso")){
                $curso = $this->input->post("curso");
                $this->db->where('turma.id_curso', $curso);
                $this->db->where('curso.id_curso', $curso);
            }
            if($this->input->post("turma")){
                $turma = $this->input->post("turma");
                $this->db->where('usuario.id_turma', $turma);
                $this->db->where('turma.id_turma', $turma);
            }
        }
        fputcsv($handle, $campos);

        $data['usuario'] = $this->db->get()->result_array();
        foreach($data['usuario'] as $key => $row){
            fputcsv($handle, $row);
        }
        fclose($handle);
    }
}