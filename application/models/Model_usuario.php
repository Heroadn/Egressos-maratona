<?php

class Model_usuario extends CI_Model{

    public function salva($usuario, $foto = NULL){
        $this->load->model('Model_postagem');
        if(!$foto){
            $foto = base_url().'static/images/padrao.png';
        }

        $this->db->where('id_turma', $usuario['id_turma']);
        $this->db->select('turma');
        $turma = $this->db->get("turma")->row_array();
        $grupos = $this->insereGrupo($turma['turma'], $usuario['ano_egresso'], $usuario['email']);

        $usuario['id_grupo'] = $grupos['id_grupo'];
        $usuario['nome_completo'] = ''.$usuario['nome'].' '.$usuario['ultimo_nome'].'';
        $this->db->insert("usuario", $usuario);

        $this->db->where('email', $usuario['email']);
        $this->db->select('id_usuario');
        $banco_user = $this->db->get("usuario")->row_array();

        $foto2 = array(
            'file_name' => $foto,
            'file_size' => 0,
            'data_insercao' => date("Y-m-d H:i:s"),
            "status_id_status" => 1,
        );

        $this->db->insert("midia", $foto2);
        $idMidia = $this->Model_postagem->CadastroMidia($foto2);
        $this->cadastroMidiaUsuario($banco_user['id_usuario'], $idMidia);

    }

    public function cadastroMidiaUsuario($idUsuario, $idMidia){
        $array = array(
            "midia_file_ID" => $idMidia,
            "usuario_id_usuario" => $idUsuario,
            "data_alteracao" => date("Y-m-d H:i:s"),
        );

        $this->db->insert("midia_usuario", $array);
    }

    public function logar($email, $senha){

        $this->db->where("email", $email);
        $this->db->where("senha", $senha);
        $usuario = $this->db->get("usuario")->row_array();
        if($usuario){
            $usuario['file_name'] = $this->buscarFoto($usuario["id_usuario"]);

            return $usuario;
        }
        else{
            return $usuario;
        }
    }

    public function loginOauth($email){

        $this->db->where("email", $email);
        $usuario = $this->db->get("usuario")->row_array();
        $usuario['file_name'] = $this->buscarFoto($usuario["id_usuario"]);

        return $usuario;

    }

    public function enviarEmail($from, $fromName, $to, $toName, $subject, $message, $reply = null, $replyName = null){

        $this->load->library('email');
        $this->email->from($from, $fromName);
        $this->email->to($to, $toName);

        if($reply)

            $this->email->reply_to($reply, $replyName);

        $this->email->subject($subject);
        $this->email->message($message);

        $this->email->send();
        #echo $this->email->print_debugger();
        #die('<br>Verifique o modelo do usuario<br>');
    }

    public function verificaToken($token){

        $this->db->where('token', $token);
        $resposta = $this->db->get("usuario")->row_array();

        $sql = array(
            'id_status' => 1,
            'token' => 0
        );
        $this->db->where('token', $token);
        $this->db->update('usuario', $sql);

        return $resposta;

    }

    public function editarPerfil($nome, $ultimo_nome ,$email, $facebook, $linkedin, $trabalhoAtual, $formacaoAcademica, $id){
        
        if($facebook == ""){
            $facebook = "vazio";
        }
        if($linkedin == ""){
            $linkedin = "vazio";
        }

        $data = array(
            'nome' => $nome,
            'ultimo_nome' => $ultimo_nome,
            'email' => $email,
            'facebook' => $facebook,
            'linkedin' => $linkedin,
            'trabalho_atual' => $trabalhoAtual,
            'formacao_academica' => $formacaoAcademica
        );
        $this->db->where('id_usuario', $id);
        $this->db->update('usuario', $data);

        $this->db->where('id_usuario', $id);
        $usuario = $this->db->get("usuario")->row_array();
        $this->db->where("id_usuario", $usuario["id_usuario"]);

        return $usuario;
    }

    public function trocarSenha($senha, $id){
        $senha_crypt = $this->bcrypt($senha);
        $data = array(
            'senha' => $senha_crypt
        );

        $this->db->where('id_usuario', $id);
        $this->db->update('usuario', $data);
    }

    public function bcrypt($senha){
        $tamanho = 22;
        $alfabeto = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $minimo = 0;
        $maximo = strlen($alfabeto) - 1;

        // Gerando a sequencia
        $salt = '';
        for ($i = $tamanho; $i > 0; --$i) {

            // Sorteando uma posicao do alfabeto
            $posicao_sorteada = mt_rand($minimo, $maximo);

            // Obtendo o simbolo correspondente do alfabeto
            $caractere_sorteado = $alfabeto[$posicao_sorteada];

            // Incluindo o simbolo na sequencia
            $salt .= $caractere_sorteado;
        }

        // Sequencia pronta
        $custo = 10;
        $senha = crypt($senha, '$2a$'. $custo . '$' . $salt . '$');
        return $senha;
    }

    public function buscarHash($email){

        $this->db->where("email", $email);
        $this->db->select('senha');
        $hash = $this->db->get("usuario")->row_array();
        return $hash['senha'];
    }

    public function buscarNome($email){

        $this->db->where("email", $email);
        $this->db->select('nome');
        $usuario = $this->db->get("usuario")->row_array();
        return $usuario['nome'];
    }

    public function insereGrupo($turma, $ano){

        $this->db->where("nome", $turma);
        $this->db->where("ano", $ano);
        $grupos = $this->db->get("grupo")->row_array();

        if ($grupos){

            return $grupos;

        }else{

            $novo_grupo = array(
                'nome' => $turma,
                'ano' => $ano,
                'descricao' => "Grupo da turma ".$turma." do ano de ".$ano,
                'id_status' => 1,
            );
            $this->db->insert("grupo", $novo_grupo);

            $this->db->where("nome", $turma);
            $this->db->where("ano", $ano);
            $grupo = $this->db->get("grupo")->row_array();

            $foto = base_url().'static/images/logo-ifc-vertical.png';
            $foto2 = array(
                'file_name' => $foto,
                'file_size' => '0',
                'data_insercao' => date("Y-m-d H:i:s"),
                'status_id_status' => 1,
            );
            $this->load->model("Model_postagem");
            $this->load->model("Model_grupo");
            $idMidia = $this->Model_postagem->cadastroMidia($foto2);
            $this->Model_grupo->cadastroMidiaGrupo($grupo["id_grupo"],$idMidia);
            $grupo['foto_grupo'] = $idMidia;
            return $grupo;

        }
    }

    public function trocarFoto($foto){
        $usuario = $this->session->userdata("usuario_logado");
        $this->load->model("Model_postagem");
        $this->excluirFoto($usuario['id_usuario']);
        $idMidia = $this->Model_postagem->cadastroMidia($foto);
        $usuario['file_name'] = $foto['file_name'];

        $this->cadastroMidiaUsuario($usuario['id_usuario'], $idMidia);

        $this->session->unset_userdata("usuario_logado");
        $this->session->set_userdata("usuario_logado", $usuario);
    }

    public function excluirFoto($idUsuario){
        $midiaFileIdWhere = 'midia.file_id = midia_file_ID';
        $midiaStatusWhere = 'midia.status_id_status = 1';
        $idMidia = $this->db
                        ->where($midiaFileIdWhere)
                        ->where($midiaStatusWhere)
                        ->where('usuario_id_usuario', $idUsuario)
                        ->select('file_ID')
                        ->get('midia, midia_usuario')->row_array();
        $this->db->set('status_id_status', 3)
            ->where('file_ID', $idMidia['file_ID'])
            ->update('midia');
    }

    public function buscarFoto($idUsuario){
        return  ($this->db->query("select midia.file_name from midia, midia_usuario, usuario where midia.file_ID = midia_usuario.midia_file_ID and midia.status_id_status = 1 and usuario.id_usuario = midia_usuario.usuario_id_usuario and usuario.id_usuario = $idUsuario")->row_array());
        //        $where = "file_ID = midia_file_ID";
//        $this->db->where("usuario_id_usuario", $idUsuario);
//        $this->db->where($where);
//        $this->db->select("file_name");
//        $foto = $this->db->get("midia, midia_usuario")->row_array();
//        return $foto['file_name'];
    }

    public function verificarOauth($email, $oauth){

        $this->db
            ->where('email',$email)
            ->select('oauth');
        $usuario = $this->db->get('usuario')->row_array();
        if($usuario != NULL AND $usuario['oauth'] == $oauth){
            return 1;
        }
        elseif($usuario == NULL){
            return 0;
        }
        else{
            return 2;
        }
    }

    public function gerarCodigo(){
        $contador = 0;

        $tamanho = 15;
        $alfabeto = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVxXyYzZ123456789!@#$%&*';
        $minimo = 0;
        $maximo = strlen($alfabeto) - 1;
        $codigo1 = '';
        for ($i = $tamanho; $i > 0; --$i) {

            // Sorteando uma posicao do alfabeto
            $posicao_sorteada = mt_rand($minimo, $maximo);

            // Obtendo o simbolo correspondente do alfabeto
            $caractere_sorteado = $alfabeto[$posicao_sorteada];

            // Incluindo o simbolo na sequencia
            $codigo1 .= $caractere_sorteado;
        }

        return $codigo1;
    }

    public function salvarCodigo($email, $codigo){
        $this->db->set('cod_rec_senha', $codigo);
        $this->db->where('email', $email);
        $this->db->update("usuario");
    }

    public function buscarCodigo($email){
        $this->db->where("email", $email);
        $this->db->select('cod_rec_senha');
        $usuario = $this->db->get("usuario")->row_array();
        return $usuario['cod_rec_senha'];
    }

    public function buscarPergunta($email){
        $this->db->where("email", $email);
        $this->db->select('pergunta');
        $usuario = $this->db->get("usuario")->row_array();
        return $usuario['pergunta'];
    }

    public function buscarResposta($email){
        $this->db->where("email", $email);
        $this->db->select('resposta');
        $usuario = $this->db->get("usuario")->row_array();
        return $usuario['resposta'];
    }

    public function recuperarSenha($senha, $email){
        $senha_crypt = $this->bcrypt($senha);
        $data = array(
            'senha' => $senha_crypt
        );

        $this->db->where('email', $email);
        $this->db->update('usuario', $data);
    }

    public function verificarEmail($email){
        $this->db->where("email", $email);
        $this->db->select('oauth');
        $usuario = $this->db->get("usuario")->row_array();
        if($usuario == ''){
            return 0;
        }else{
            if($usuario['oauth'] == NULL){
                return 1;
            }
            else{
                return 2;
            }
        }
    }

    public function numeroLetra($senha){

        if (preg_match('/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/', $senha) AND preg_match('/^[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$/', $senha)){

            $this->form_validation->set_message('numeroLetra', 'A {field} deve ter pelo menos um numero e uma letra!');
            return FALSE;

        }else{

            return TRUE;

        }

    }

    public function verificaNomeCompleto($nome){
        $nome_separado = explode(' ', $nome);
        $numero_de_nomes = 0;

        foreach ($nome_separado as $value){

            if (strlen($value) == 0){

                $numero_de_nomes = $numero_de_nomes;

            }else{

                $numero_de_nomes = $numero_de_nomes +1;

            }

        }

        if ($numero_de_nomes >= 2) {

            return TRUE;

        }else{

            $this->form_validation->set_message('verificaNomeCompleto', 'Você dever colocar o seu {field} completo!');
            return FALSE;

        }


    }

    public function buscarNotificacoes(){
        $usuario = $this->session->userdata("usuario_logado")['id_usuario'];

        return($this->db->query("SELECT id_notificacao, texto_notificacao, tipo_notificacao_id_tipo, id_usuario_de, midia.file_ID, midia.file_name,  id_usuario_para, id_origem, notificacao.tipo_notificacao_id_tipo
                                FROM tipo_notificacao, notificacao, usuario, midia_usuario, midia
                                WHERE tipo_notificacao_id_tipo = tipo_notificacao.id_tipo
                                AND notificacao.id_status = 7
                                AND id_usuario = id_usuario_de
                                AND id_usuario_para = $usuario
                                AND midia_usuario.usuario_id_usuario = usuario.id_usuario
                                AND midia.file_ID = midia_usuario.midia_file_ID
                                AND midia.status_id_status = 1")->result_array());

    }

    public function buscarAmigos(){

        $usuario = $this->session->userdata("usuario_logado")['id_usuario'];
        $ids = ($this->db->query("SELECT amigos.id_amigos 
                                  FROM amigos 
                                  WHERE id_usuario1 = $usuario 
                                  AND id_status = 8 
                                  OR id_usuario2 = $usuario
                                  AND id_status = 8")->result_array());
        $listaAmigos = array();
        foreach ($ids as $perfect_id ){
            $id_amigos = $perfect_id['id_amigos'];
            $amigos = ($this->db->query("SELECT DISTINCT usuario.id_usuario, usuario.nome_completo, usuario.descricao, midia.file_name
                                         FROM amigos, midia
                                         INNER JOIN midia_usuario ON midia.file_ID = midia_usuario.midia_file_ID
                                         INNER JOIN usuario ON midia_usuario.usuario_id_usuario = usuario.id_usuario
                                         WHERE amigos.id_amigos = $id_amigos AND
                                         midia.status_id_status = 1 AND
                                         usuario.id_usuario != $usuario AND
                                         amigos.id_usuario1 = usuario.id_usuario OR
                                         amigos.id_usuario2 = usuario.id_usuario AND
                                         usuario.id_usuario != $usuario AND
                                         midia.status_id_status = 1 AND
                                         amigos.id_amigos = $id_amigos")->row_array());
            array_push($listaAmigos, $amigos);
        }

        return $listaAmigos;
    }

    public function buscarAmigosVisita($id){

        return($this->db->query("SELECT usuario.id_usuario, usuario.nome_completo, midia.file_name, usuario.descricao 
                                 FROM usuario, midia, midia_usuario 
                                 WHERE midia.file_ID = midia_usuario.midia_file_ID
                                 AND midia.status_id_status = 1 
                                 AND (usuario.id_usuario IN (SELECT id_usuario2 FROM amigos WHERE id_usuario1 = $id AND id_status = 8) 
                                 OR usuario.id_usuario IN (SELECT id_usuario1 FROM amigos WHERE id_usuario2 = $id AND id_status = 8)) 
                                 AND usuario.id_usuario != $id 
                                 AND usuario.id_usuario = midia_usuario.usuario_id_usuario")->result_array());

    }

    public function countAmigos(){

        $usuario = $this->session->userdata("usuario_logado")['id_usuario'];

        return ($this->db->query("SELECT usuario.id_usuario 
                                  FROM usuario 
                                  WHERE (usuario.id_usuario IN (
                                  SELECT id_usuario2 
                                  FROM amigos 
                                  WHERE id_usuario1 = $usuario 
                                  AND id_status = 8) 
                                  OR usuario.id_usuario IN (
                                  SELECT id_usuario1 
                                  FROM amigos 
                                  WHERE id_usuario2 = $usuario 
                                  AND id_status = 8)) 
                                  AND usuario.id_usuario != $usuario")->num_rows());

    }

    public function countAmigosVisita($id){

        $usuario = $id;

        return ($this->db->query("SELECT usuario.id_usuario 
                                  FROM usuario 
                                  WHERE (usuario.id_usuario IN (
                                  SELECT id_usuario2 
                                  FROM amigos 
                                  WHERE id_usuario1 = $usuario 
                                  AND id_status = 8) 
                                  OR usuario.id_usuario IN (
                                  SELECT id_usuario1 
                                  FROM amigos 
                                  WHERE id_usuario2 = $usuario 
                                  AND id_status = 8)) 
                                  AND usuario.id_usuario != $usuario")->num_rows());

    }

    public function buscarAmigosNome($nome){

        $usuario = $this->session->userdata("usuario_logado")['id_usuario'];

        return($this->db->query("select amigouser1.id_usuario, amigouser1.nome_completo as nomAmigo, amigouser1.email, usuario.nome_completo
                                from usuario,amigos, usuario amigouser1
                                where usuario.id_usuario = $usuario and 
                                amigos.id_status = 8 and
                                usuario.id_usuario = amigos.id_usuario1 and
                                amigouser1.id_usuario = amigos.id_usuario2 and amigouser1.nome_completo LIKe '%$nome%'
                                
                                union
                                
                                select amigouser1.id_usuario, amigouser1.nome_completo as nomAmigo, amigouser1.email, usuario.nome_completo
                                from usuario,amigos, usuario amigouser1
                                where usuario.id_usuario = $usuario and 
                                amigos.id_status = 8 and
                                usuario.id_usuario = amigos.id_usuario2 and
                                amigouser1.id_usuario = amigos.id_usuario1 and amigouser1.nome_completo LIKE '%$nome%';")->result_array());

    }

    public function buscarConviteAmigosNome($nome){

        $usuario = $this->session->userdata("usuario_logado")['id_usuario'];

        return($this->db->query("SELECT usuario.id_usuario, usuario.nome_completo FROM usuario WHERE (usuario.id_usuario IN (SELECT id_usuario2 FROM amigos WHERE id_usuario1 = $usuario AND id_status = 7)) AND usuario.id_usuario != $usuario AND usuario.nome_completo LIKE '%$nome%' ORDER BY usuario.nome_completo")->result_array());

    }

    public function retornaUsuario($nome, $idUsuario){

        $alunos = $this->buscarUsuario($nome);
        $amigos = $this->buscarAmigosNome($nome);
        $convite = $this->buscarConviteAmigosNome($nome);
        $tamanhoConvite = count($convite);
        $tamanhoAmigos = count($amigos);
        $indiceAmigos = 0;
        $indiceConvite = 0;
        $aluno = "
                <br>
                <div class='ui cards'>";
        if($alunos->result()) {

            foreach ($alunos->result() as $item) {
                if ($indiceAmigos == $tamanhoAmigos) {
                    $indiceAmigos = "-1";
                }
                if ($indiceConvite == $tamanhoConvite) {
                    $indiceConvite = "-1";
                }
                $foto = $this->buscarFoto($item->id_usuario);
                $aluno .= "    
                    <div class='ui card' style='max-width:100%; min-width: 96%;'>
                        <div class='ui two column grid'>
                            <div class='row'>
                                <div class='column'>
                                    <div class='image' >
                                        <img class='ui circular image' src='" . $foto["file_name"] . "' width='60px' height='auto' style='margin-left: 10%;'>
                                    </div>
                                </div>
                                <div class='column'>
                                    <div style='max-width:100%;'>
                                    <h4>" . $item->nome_completo . "</h4>
                                    <form class='ui form' method='post' action='". base_url() ."Usuario/perfilVisita' style='margin-top: 0em;'>
                                        <div class='field'>
                                        <input name='id_usuario' type='hidden' value='$item->id_usuario'>
                                        </div>
                                        <input type='submit' class='ui button' value='Ver perfil'>
                                    </form>    
                                    </div>
                                </div>
                            </div>
                        </div>";
                if ($item->id_usuario == $idUsuario) {

                    $aluno .= "
                        <div class='column'>
                            <div class='ui red bottom attached'></div>
                        </div>";
                } elseif ($indiceAmigos != "-1" AND array_search($item->id_usuario, $amigos[$indiceAmigos])) {
                    $aluno .= "
                        <div class='column'>
                            <form action = '" . base_url() . "Amigo/removerAmigo' method='POST' style='margin-bottom: 0em;'>
                                <input type='hidden' value='" . $item->id_usuario . "' name='id_amigo'>
                                <button type='submit' class='ui bottom red attached button' style='min-width: 100%; max-width: 100%'>
                                    Cancelar Amizade
                                </button>
                            </form>
                         </div>";
                    $indiceAmigos += 1;
                } elseif ($indiceConvite != "-1" AND array_search($item->id_usuario, $convite[$indiceConvite])) {
                    $aluno .= "
                        <div class='column'>
                            <form action = '" . base_url() . "Amigo/recusarConvite' method='POST' style='margin-bottom: 0em;'>
                                <input type='hidden' value='" . $item->id_usuario . "' name='id_amigo'>
                                <button type='submit' class='ui bottom red attached button' style='min-width: 100%; max-width: 100%'>
                                    Cancelar Convite
                                </button>
                            </form>
                         </div>";
                    $indiceConvite += 1;

                } else {
                    $aluno .= "
                        <div class='column' >
                            <form action = '" . base_url() . "Amigo/adicionarAmigo' method='POST' style='margin-bottom: 0em;'>
                                <input type='hidden' value='" . $item->id_usuario . "' name='id_amigo'>
                                
                                <button type='submit'class='ui bottom green attached button' style='min-width: 100%; max-width: 100%'>
                                     Enviar solicitação
                                </button>
                            </form>
                        </div>";
                }

                $aluno .= "
                                           
                    </div>";
            }
            $aluno .= "
                </div>";
            return $aluno;
        }
        else{
            return "<label>Ninguém encontrado</label>";
        }
    }

    public function buscarUsuario($nome){
        return $this->db
            ->like('nome_completo', $nome, 'after')
            ->select('nome_completo, descricao, id_usuario')
            ->order_by('nome_completo')
            ->get('usuario');
    }

    public function buscarPerfil($id) {
        $usuario = $this->db->query("select usuario.nome_completo, usuario.email, midia.file_name, curso.id_curso, usuario.id_turma, campus.id_campus, usuario.id_grupo, usuario.ano_egresso, usuario.facebook, usuario.descricao, usuario.linkedin, usuario.trabalho_atual, usuario.formacao_academica, curso.curso, campus.nome, turma.turma, grupo.nome
        from usuario, midia, midia_usuario, curso, turma, campus, grupo
        where usuario.id_grupo = grupo.id_grupo 
        and usuario.id_turma = turma.id_turma
        and turma.id_curso = curso.id_curso
        and curso.id_campus = campus.id_campus
        and midia.file_ID = midia_usuario.midia_file_ID
        and midia_usuario.usuario_id_usuario = usuario.id_usuario and id_usuario = $id")->result_array();
        return $usuario[0];
    }

    public function buscaAnoDeIngresso($idUser){
        return ($this->db->query("SELECT DATE_FORMAT(usuario.data_criacao, '%M de %Y') as data FROM usuario WHERE usuario.id_usuario = $idUser")->row_array());
    }

    public function getIdCursoUsuario($id_usuario){
        return ($this->db->query("select turma.id_curso from usuario, turma where usuario.id_turma = turma.id_turma and usuario.id_usuario = $id_usuario")->row_array());
    }

    public function getIdCampusUsuario($id_curso){
        $id = $id_curso['id_curso'];
        return ($this->db->query("select curso.id_campus from curso where curso.id_curso = $id")->row_array());
    } 
    
}