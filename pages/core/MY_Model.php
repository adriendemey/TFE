<?php
class MY_Model extends CI_Model {
    public $table_name = '';
    public $primary_key = '';
    public $table_order = '' ;
    public $filter = 'intval';
    
    function __construct() {
        parent::__construct();
    }
    
    /*
  ___  ____  ____ 
 / __)(  __)(_  _)
( (_ \ ) _)   )(  
 \___/(____) (__)
     */
    
    /* GET
         Simple requête qui récupère toutes les informations.
         paramètres :
         $ids           Optionnel array ou int
    */
    public function get($ids = FALSE) {
        // drapeau unique ou multiple // si un seul id est passé, on renvoie un seul enregistrement
        // $unique = $ids == FALSE || is_array($ids) ? FALSE : TRUE;
        if ($ids == FALSE || is_array($ids) || $ids > 1) {
            $unique = FALSE;
        } else {
            $unique = TRUE;
        }
        // Test si un ou plusieur id
        if ($ids !== FALSE) {
            // Si le paramètre est un tableau, c'est qu'on souhaites afficher certains enregistrements
            if (is_array($ids)) {
                // protection pour les injections
                $filterIds = $this->filter;
                // Application du filtre
                $ids = array_map($filterIds,$ids);
                $this->db->where_in($this->primary_key, $ids);
            } else {
                // Si c'est un nombre, c'est qu'on souhaites afficher autant d'enregistrement que ce nombre
            }
            
        }
        /*if($unique == FALSE){
            $this->db->limit(1);
        }else */if($unique == FALSE && is_int($ids)){
            $this->db->limit($ids);
        }
        $method = $unique ? 'row_array' : 'result_array';
        $this->db->order_by($this->table_order);
        return $this->db->get($this->table_name)->$method();
    }
    /* GET_BY
        Récupère uniquement les informations qu'on a besoin.
        paramètres :
        $key            array ou string
        $value          string ou NULL si $key = array
        $orwhere        Boolean Définit la méthode OU ou ET suivant le paramètre $orwhere.
        $count          int Nombre d'entrées souhaitées. 0 ou FALSE pour tout recevoir.
    */
    public function get_by($key, $val = FALSE, $orwhere = FALSE, $count = FALSE) {
        if(!is_array($key)) {
            // Si on recherche dans une seule colonne, pas besoin de array, on passe donc $key et $val
            $this->db->where(htmlentities($key), htmlentities($val));
        } else {
            $key = array_map('htmlentities', $key);
            $where_method = $orwhere === TRUE ? 'or_where' : 'where';
            $this->db->$where_method($key);
        }
        $count === FALSE || $this->db->limit($count);
        
        if ($count == 1) {
            // Si $count demande 1 seule ligne :
            $method = 'row_array'; // Transforme le retour en array de 1 ligne
        } else {
            // Si count vaut FALSE (=tout), ou plus de 1 :
            $method = 'result_array'; // Transforme le retour en array
        }
        $this->db->order_by($this->table_order);
        return $this->db->get($this->table_name)->$method();
    }
    /* GET_KEY_VALUE
        Renvoie un array formaté tel quel : "'$key_field' => '$value_field'".
        paramètres :
        $key_field      string
        $value_field    string
        $ids            Optionnel array ou string
    */
    public function get_key_value($key_field, $value_field, $ids=FALSE) {
        $this->db->select($key_field.','.$value_field);
        $result = $this->get($ids);
        $data = array();
        if (count($result) > 0) {
            if($ids != FALSE && !is_array($ids)) {
                // Si il n'y a qu'un seul résultat, on le rentre quand même dans un tableau
                $result = array($result);
            }
            foreach($result as $row) {
                $data[$row[$key_field]] = $row[$value_field];
            }
        }
        return $data;
    }
    /* GET_ASSOC
        Renvoie un array dont l'index est définit par la clé primaire de la table.
        paramètre :
        $ids            Optionnel array ou string
    */
    public function get_assoc($ids = FALSE) {
        $result = $this->get($ids);
        if($ids != FALSE && !is_array($ids)) {
            $results = array($result);
        }
        $data = $this->to_assoc($result);
        return $data;
    }
    /* TO_ASSOC
        Méthode appellée par get_assoc pour définir l'index du array.
        Vous n'êtes pas sensés l'utiliser directement.
    */
    private function to_assoc($result = array()) {
        $data = array();
        if(count($result) > 0) {
            foreach($result as $row) {
                $tmp = array_values(array_slice($row,0,1));
                $data[$tmp[0]] = $row;
            }
        }
        return $data;
    }
    /*
  __   ____  ____      _    ____  ____  __  ____ 
 / _\ (    \(    \    / )  (  __)(    \(  )(_  _)
/    \ ) D ( ) D (   / /    ) _)  ) D ( )(   )(  
\_/\_/(____/(____/  (_/    (____)(____/(__) (__) 
    */
    
    /* SAVE
        Méthode 2 en 1 pour enregister ou modifier des entrées.
        paramètres :
        $data           array
        $id             Optionnel string
    */
    public function save($data,$id = FALSE) {
        if ($id === FALSE) {
            //insertion
            $this->db->set($data)->insert($this->table_name);
        } else {
            //mise à jour
            $filter = $this->filter;
            $this->db->set($data)->where($this->primary_key, $filter($id))->update($this->table_name);
        }
        
        return $id === FALSE ? $this->db->insert_id() : $id;
    }
    /*
 ____  ____  __    ____  ____  ____ 
(    \(  __)(  )  (  __)(_  _)(  __)
 ) D ( ) _) / (_/\ ) _)   )(   ) _) 
(____/(____)\____/(____) (__) (____)
    */
    
    /* DELETE
        Supprime une ou des entrées
        paramètre :
        $ids            array ou string
    */
    public function delete($ids) {
        $filter = $this->filter;
        $ids = !is_array($ids) ? array($ids) : $ids;
        foreach ($ids as $id) {
            $id = $filter($id);
            if ($id) {
                $this->db->where($this->primary_key, $id)->limit(1)->delete($this->table_name);
            }
        }
    }
    /* DELETE_BY
        Supprime des entrées qui sont le résultats d'une recherche
        paramètres :
        $key            string
        $value          string
    */
    public function delete_by($key,$value) {
        if(empty($key)) {
            return FALSE;
        }
        $this->db->where(htmlentities($key),htmlentities($value))->delete($this->table_name);
    }
    /*
  __  ____  _  _  ____  ____  ____ 
 /  \(_  _)/ )( \(  __)(  _ \/ ___)
(  O ) )(  ) __ ( ) _)  )   /\___ \
 \__/ (__) \_)(_/(____)(__\_)(____/
    */
    
    /* COUNT
        Retroune le nombre d'entrées
    */
    public function count() {
        $this->db->from($this->table_name);
        return $this->db->count_all_results();
    }
}
?>