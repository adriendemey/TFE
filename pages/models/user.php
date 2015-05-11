<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User extends MY_Model {
    function __construct() {
        parent::__construct();
        $this->table_name = 'users';
        $this->primary_key = 'id';
        $this->table_order = 'id DESC';
    }
    function login($username, $password) {
        // Cette requête renvoie tout les résultats où username=$username et password=$password.
        // NULL est là car j'utilise un array pour passer mes critères,
        // Le premier FALSE signifie que je souhaite utiliser la méthode ET pour orwhere
        $getby = $this->get_by(array('username' => $username,'password' => md5($password)),NULL, FALSE, FALSE);
        if(count($getby) == 1) {
            return $getby;
        } else {
            return false;
        }
    }
}
?>