<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Login extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('user','',TRUE);
    }
    function index() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean');
        // Utilisation d'un callback. Lance la fonction check_database (plus bas dans ce fichier)
        $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_check_database');

        if($this->form_validation->run() == FALSE) {
            // Validation des champs ratée, affichage de la page login (contenant les champs)
            $data['main_content'] = 'login_view';
            $this->load->view('template/standard',$data);
        } else {
            // Validation réussie, redirection vers la page d'administration
            redirect('admin', 'refresh');
        }
    }
    function check_database($password) {
        // Si les champs sont correctement complétés, cette fonction est appellée dans la règle pour le champ password.
        // Cette fonction renvoie TRUE si une ligne 
        
        // On récupère la valeur du champ username :
        $username = $this->input->post('username');
        // Et on envoie une requête à la BDD, le mot de passe est passé en paramètre de cette fonction (voir set_rule de password)
        $result = $this->user->login($username, $password);

        if($result) {
            // Si username et password concordent, une variable de session est créée :
            $sess_array = array();
            foreach($result as $row) {
                $sess_array = array(
                    'id' => $row['id'],
                    'username' => $row['username']
                );
                $this->session->set_userdata('logged_in', $sess_array);
            }
            return TRUE;
        } else {
            $this->form_validation->set_message('check_database', 'Utilisateur ou mot de passe invalide.');
            return false;
        }
    }
}
?>