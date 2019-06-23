<?php

class Model_cadastro extends CI_Model{

    public function dadosCad($where, $valorWhere, $select, $tabela){

        $dados = $this->db->where("$where", "$valorWhere")
            ->select("$select")
            ->get(''.$tabela.'')->row();
        return $dados;

    }

    public function dadosPerfil($campoId, $campoNome ,$tabela, $campoWhere){
        $this->db->where("$campoWhere", $campoId);
        $this->db->select($campoNome);
        $campo = $this->db->get("$tabela")->row_array();
        return $campo;
    }

    public function dadosPerfil2($campoId, $campoNome ,$tabela1, $tabela2, $campoWhere){
        $where = "$tabela2.id_$tabela2 = $tabela1.id_$tabela2";
        $this->db->where("$campoWhere", $campoId);
        $this->db->where($where);
        $this->db->select($campoNome);
        $campo = $this->db->get("$tabela1, $tabela2")->row_array();
        return $campo;
    }


    public function dadosFormacao($formacaoAcademica){

        $formacao = explode(";", $formacaoAcademica);
        return $formacao;
    }

    public function pegarCampus(){

        return $this->db
            ->order_by('nome')
            ->get('campus');
    }

    public function selectCampus(){
        $options = "<option value='' selected='selected'>Selecione o Campus</option>";

        $campus = $this->pegarCampus();

        foreach($campus ->result() as $item){
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
}