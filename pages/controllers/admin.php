<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
session_start(); //we need to call PHP's session object to access it through CI
class Admin extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    function index() {
        // Test si la personne est connectée :
        if($this->session->userdata('logged_in')) {
            // Si la session existe :
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $data['main_content'] = 'admin_view';
            $this->load->view('template/admin',$data);
        } else {
            //Si la session n'existe pas, direction la page de connexion :
            redirect('login', 'refresh');
        }
    }
    function logout() {
        $this->session->unset_userdata('logged_in');
        session_destroy();
        redirect('index', 'refresh');
    }
    function login() {
        $this->load->helper(array('form'));
        $data['main_content'] = 'login_view';
        $this->load->view('template/standard',$data);
    }
}
?>