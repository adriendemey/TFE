<?php
class index_controller extends CI_Controller {
	public function index() {
		$data['main_content'] = 'accueil_view';
        $this->load->view('template/standard',$data);
	}
}
?>