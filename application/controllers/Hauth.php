<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Hauth Controller Class
 */
class Hauth extends CI_Controller {
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('hybridauth');
    }
    /**
     * {@inheritdoc}
     */
    /**
     * Try to authenticate the user with a given provider
     *
     * @param string $provider_id Define provider to login
     */
    public function window($provider_id)
    {
        $params = array(
            'hauth_return_to' => site_url("hauth/window/{$provider_id}"),
        );
        if (isset($_REQUEST['openid_identifier']))
        {
            $params['openid_identifier'] = $_REQUEST['openid_identifier'];
        }
        try
        {
            $adapter = $this->hybridauth->HA->authenticate($provider_id, $params);
            $profile = $adapter->getUserProfile();
            $email = $profile->email;
            $oauth = $profile->identifier;
            $this->load->model('Model_usuario');
            $verificaOauth = $this->Model_usuario->verificarOauth($email, $oauth);
            if($verificaOauth == 1){
                $profile = (array) $profile;
                $this->session->set_userdata("usuario_oauth", $profile);
                redirect('Usuario/autenticaOauth');
            }
            elseif($verificaOauth == 2){
                $this->session->set_flashdata("oauth_existente", "Já existe uma conta com esse email!");
                $this->session->set_flashdata("classe_oauth_existente", "ui centered red message");
                redirect('PortalEgresso');
            }
            else{
                $this->load->model('Model_cadastro');
                $profile = (array) $profile;
                $profile["redeSocial"] = $provider_id;
                $this->session->set_userdata("usuario_oauth", $profile);
                redirect('PortalEgresso/completarCadastro');
            }
        }
        catch (Exception $e)
        {
            show_error($e->getMessage());
        }
    }
    /**
     * Handle the OpenID and OAuth endpoint
     */
    public function endpoint()
    {
        var_dump($_REQUEST);die();
        if (isset($_REQUEST['hauth_start']) || isset($_REQUEST['hauth_done']))
        {
            Hybrid_Endpoint::process();
        }
    }
}