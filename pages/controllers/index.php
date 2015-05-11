<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class index extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
	public function index() {
		$data['main_content'] = 'accueil_view';
        $this->load->view('template/standard',$data);
	}
}
?>