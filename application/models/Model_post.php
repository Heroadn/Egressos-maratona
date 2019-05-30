<?php


class Model_post extends CI_Model
{
    public function fetch($id_grupo = 0){
        $output = '';
        $this->load->model('Model_postagem');
        $data = $this->Model_postagem->fetch_data($this->input->post('limit'), $this->input->post('start'), $id_grupo);
        $base = base_url("Timeline/visu_post");
        if(count($data) > 0)
        {
            foreach($data as $row)
            {
                $fotos = $this->Model_postagem->buscarFotoPost($row['id_post']);
                if( $fotos[$row['id_post']]){
                    $totalFotos = count($fotos[$row['id_post']]);
                }else{
                    $totalFotos = 0;
                }
                $output .= '
                            <div class="ui card segment count timeline-post">
                                <div class="content">
                                    <span class="right floated star">'.$row['data'].'</span>
                                    <a class="header" href="'.$base.'/'.$row['id_post'].'">'.$row['titulo'].'</a>

                                    <div class="description">
                                        '.$row['descricao'].'
                                    </div>
                                </div>
                                    ';

                if($fotos[$row['id_post']]){

                    if($totalFotos > 1){
                        $output.= '
                                <div class="ui center aligned centered toslider" id="imagem'.$row['id_post'].'">
                                    <div class="ui grid center aligned">
                                        <div class="eight columns center aligned">
                                            <img class="ui medium image centered imagem1" src="'.$fotos[$row['id_post']][0].'">
                                        </div> 
                                    </div>
                                </div>';

                    }
                    else{
                        $output.= '
                                <div class="ui medium image centered toslider" id="imagem'.$row['id_post'].'">
                                    <img src="'.$fotos[$row['id_post']][0].'">
                                </div>';
                    }

                }

                $output .='
                                <div class="extra content">
                                    <div class="ui grid">
                                        <div class="one wide column">
                                            <span class="left floated gostei">
                                                <div class="curtidas2">
                                                    <label class="curtidas" id="'.$row['id_post'].'"></label><i class="like icon love"></i>
                                                </div>
                                            </span>
                                        </div>
                                        <div class="one wide column">
                                            
                                        </div>
                                        <div class="eight wide column">';

                if($fotos[$row['id_post']]) {

                    for ($i = 1; $i < $totalFotos AND $i < 5; $i++) {
                        $output .= '
                                            
                                            <img class="ui mini image centered" src="'.$fotos[$row['id_post']][$i].'">
                                            
                                ';
                    }
                        $maisFotos = $totalFotos - $i;
                        if($maisFotos > 0){
                            $output .= '<i class="mini plus icon"></i>'.$maisFotos.'';
                        }
                }
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
                $output.= '
                                            <div class="ui modal fotos" id="'.$row['id_post'].$row['id_post'].'">
                                                <div class="modal-content" id="content-modal">
                                                    <div class="modal-body">
                                                        <div class="ui container fluid pics" id="s2">';
                if($fotos[$row['id_post']]) {

                    for ($i = 0; $i < $totalFotos; $i++) {
                        $output.= '
                                    <img style="width: 250px; height: 250;" class="ui medium image centered" src="' . $fotos[$row['id_post']][$i] . '"/>';
                    }
                }

                $output .='             
	                                    </div>
	                                    <br>
                                    </div>
                                    <div class="modal-footer">';

                if($totalFotos > 1){

                    $output .= '              
                                        <div class="ui segment center aligned" id="buttons-slider">
	                                        <label class="prev2 ui inverted green button"><i class="icon angle left"></i>Anterior</label>
                                            <label class="next2 ui inverted green button">Próxima<i class="icon angle right"></i></label>
                                        </div>';
                }
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
                $output .= "
                        <script>
                            $('#imagem" . $row['id_post'] . "').on('click', function () {
                                $('#" . $row['id_post'] . $row['id_post'] . "').modal('setting', 'transition', 'fade').modal('show');
                                $(function(){
                                    $('.pics').cycle({
                                    timeout: 0,
                                    next:   '.next2', 
                                    prev:   '.prev2' 
		                            
	                            });
                                
                            });
                                });
                        </script>";

            }

        }
        echo $output;


    }
}