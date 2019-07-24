<?php

class LDAP extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
        $usuario = $this->session->userdata("usuario_logado");
        $this->twig->addGlobal('usuario_logado', $usuario);
        $this->load->model("Model_administrador");
        if($usuario['id_tipo_usuario'] != 2){
            //redirect('Usuario/Perfil');
        }
    }

    public function LDAPLogin(){

        $ldap_conn = ldap_connect('191.52.62.221/phpldapadmin');
        $ldap_dn = "cn=admin,dc=example,dc=com";
        $ldap_pass = "admin";

        ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        if (ldap_bind($ldap_conn, $ldap_dn, $ldap_pass)){

//            echo "Conexão Bem-Sucedida";

            $this->LDAPBuscarDados($ldap_conn);

        }

    }

    public function LDAPBuscarDados($conn){

        if ($conn) {

            $filter = "(uid=*)";

            $busca = ldap_search($conn, "dc=example,dc=com", $filter) or exit ("Falha na Busca.");

            $entradas = ldap_get_entries($conn, $busca);

            $this->LDAPTratarDados($entradas);
        }else{

            echo "Conexão não Efetuada";

        }
    }

    public function LDAPTratarDados($entradas){

        //pegar o nome de um unico usuario;
        $count = $entradas[0]['uid']['count'];
        if ($count>1){

            for ($i=0; $i<$count; $i++){
                print_r("<pre>");
                echo $entradas[0]['uid'][$i];
                print_r("</pre>");
            }
        }else{
            echo $entradas[0]['uid'][0];
        }


        print_r("<pre>");
        print_r($entradas);
        print_r("<pre>");



    }

}