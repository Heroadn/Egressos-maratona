<?php

class Model_conversar extends CI_Model
{

    public function fetch($id_grupo = 0,$limit = 10, $start = 0){
        $output = '';
        $this->load->model('Model_mensagem');
        $data = $this->Model_mensagem->fetch_data($this->input->post('limit'), $this->input->post('start'), $id_grupo);
        
        if(count($data) > 0)
        {
            foreach($data as $row)
            {
                
                $output .=
                                        
                '</div>
                <div class="six wide column">
                    <div class="right floated author">
                        <img class="ui avatar image" src="'.$row['file_name'].'">'.$row['nome'].' '.$row['ultimo_nome'].'         
                    </div>
                </div>
                        </div>
                    </div>
                </div>';

                $output .= '
                            <div class="ui card segment count timeline-post">
                                <div class="content">
                                    <span class="right floated star">'.$row['data'].'</span>

                                    <div id="'.$row['id_mensagem'].'" class="conteudo">
                                        <span class="hidden id_mensagem">'.$row['id_mensagem'].'</span>
                                        '.$row['conteudo'].'
                                    </div>
                                </div>
                                    ';
                

                $output.= '
                                            <div class="ui modal fotos" id="'.$row['id_mensagem'].$row['id_mensagem'].'">
                                                <div class="modal-content" id="content-modal">
                                                    <div class="modal-body">
                   
                                                    <div class="ui container fluid pics" id="s2">';
                

                $output .='             
	                                    </div>
	                                    <br>
                                    </div>
                                    <div class="modal-footer">';
                
                $output .='
                                        
                                        <div class="actions">
                                            <br>
                                            <button class="ui red cancel labeled icon button"><i class="remove icon"></i>Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                $output .= '
                        <link rel="stylesheet" href="'.base_url().'static/slider.css"></>
                    ';
            }

        }
        echo $output;
    }

    public function gera_form($action){
        $posts["form_open"] = form_open_multipart($action, 'class="ui reply form segment" id="formChat"');
        $posts["label_conteudo"] = form_label("Conteudo da Postagem", "conteudo");
        $posts["input_conteudo"] = form_textarea(array("name" => "conteudo", "id" => "conteudo", "class" => "", "maxlength" => "2555", "value" => strip_tags(htmlspecialchars_decode(set_value('conteudo')))));
        $posts["button_confirm"] = form_button(array("content" => "<i class=\"icon edit\"></i> Postar", "class" => "confirm ui primary submit right floated labeled icon button", "onclick" => "salvarMensagem();"));//
        $posts["form_close"] = form_close();
        return $posts;
    }
}