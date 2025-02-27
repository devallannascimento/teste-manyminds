<?php
class Usuario_model extends CI_Model {

    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->library('session');
    }

    public function get_user_data($usuario_id) {

        $sql = "SELECT * FROM usuarios WHERE id = ?";

        $query = $this->db->query($sql, [$usuario_id]);
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return null;
        }
    }

    public function get_all_users() {
        
        $sql = "SELECT * FROM usuarios";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function get_users_admin() {
        
        $sql = "SELECT * FROM usuarios WHERE role != 'admin' and role != 'master'";

        $query = $this->db->query($sql);
        return $query->result();
    }

    public function inativar($id) {
        
        $sql = "UPDATE usuarios SET ativo = 0 WHERE id = ?";

        $this->db->query($sql, [$id]);
    }

    public function reativar($id) {
        
        $sql = "UPDATE usuarios SET ativo = 1 WHERE id = ?";

        $this->db->query($sql, [$id]);
    }

    public function atualizar($id, $dados) {
        
        $sql = "UPDATE usuarios SET nome = ?, email = ? WHERE id = ?";
    
        $this->db->query($sql, array($dados['nome'], $dados['email'], $id));
    }
    
    public function gerar_api_key($user_id) {

        $this->load->helper('string');
        $api_key = bin2hex(random_bytes(32));
        $hashed_key = password_hash($api_key, PASSWORD_BCRYPT);

        $sql = "INSERT INTO api_keys (user_id, api_key) VALUES (?, ?)";
        $this->db->query($sql, [$user_id, $hashed_key]);

        return $api_key;
    }

    
    public function validar_api_key($api_key) {

        $this->db->select('api_key');
        $this->db->from('api_keys');
        $query = $this->db->get();
    
        foreach ($query->result() as $row) {
            if (password_verify($api_key, $row->api_key)) {
                return true;
            }
        }
        
        return false;
    }
    
}
