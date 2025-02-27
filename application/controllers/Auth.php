<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->model('Usuario_model');
        $this->load->library('session');
        $this->load->helper(array('url', 'form'));
    }

    public function index() { 
        
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            redirect('dashboard');
        } else {
            $this->load->view('login');
        }
    }

    public function cadastro() {
        $this->load->view('cadastrar_usuario');
    }
    
    public function cadastro_admin() {
        
        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            redirect('auth');
            return;
        }
    
        $usuario = $this->Usuario_model->get_user_data($user_id);
        if ($usuario && isset($usuario->role) && $usuario->role === 'master') {
            $this->load->view('cadastrar_admin');
        } else {
            redirect('dashboard');
        }
    }    

    public function login() {
        
        $ip_address = $this->input->ip_address();
        $usuario = $this->input->post('usuario');
        $senha = $this->input->post('senha');

        if ($this->ip_bloqueado($ip_address)) {

            $this->session->set_flashdata('error', 'Muitas tentativas. Tente novamente mais tarde.');
            redirect('auth');
        }

        $sql = "SELECT * FROM usuarios WHERE usuario = ? AND ativo = 1";
        $query = $this->db->query($sql, [$usuario]);
        $user = $query->row();

        if ($user && password_verify($senha, $user->senha)) {

            $this->db->query("DELETE FROM login_attempts WHERE ip_address = ?", array($ip_address));
            $this->session->set_userdata('user_id', $user->id);
            redirect('dashboard');
        } else {

            $this->registrar_falha_login($ip_address);
            $this->session->set_flashdata('error', 'Usuário ou senha inválidos.');
            redirect('auth');
        }
    }

    public function cadastrar() {

        $usuario = $this->input->post('usuario');
        $senha = $this->input->post('senha');
        $confirmar_senha = $this->input->post('confirmar_senha');
        $email = $this->input->post('email');
        $nome = $this->input->post('nome');
        $ativo = 1;
    
        if ($senha !== $confirmar_senha) {
            $this->session->set_flashdata('error', 'As senhas não coincidem.');
            redirect('auth/cadastro');
            return;
        }
    
        $query = $this->db->query("SELECT * FROM usuarios WHERE usuario = ?", array($usuario));
        if ($query->num_rows() > 0) {
            $this->session->set_flashdata('error', 'Este nome de usuário já está em uso.');
            redirect('auth/cadastro');
            return;
        }
    
        $query_email = $this->db->query("SELECT * FROM usuarios WHERE email = ?", array($email));
        if ($query_email->num_rows() > 0) {
            $this->session->set_flashdata('error', 'Este email já está registrado.');
            redirect('auth/cadastro');
            return;
        }
    
        if (!preg_match("/^[A-Za-zÀ-ú']{2,40}(\s[A-Za-zÀ-ú']{2,40})+$/", $nome)) {

            $this->session->set_flashdata('error', 'O nome deve conter pelo menos duas palavras, cada uma com pelo menos 2 caracteres, e não pode conter caracteres especiais, com exceção do apóstrofo.');
            redirect('auth/cadastro');
            return;
        }
    
        $nome_formatado = ucwords(strtolower($nome));
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
    
        $data = array(
            'usuario' => $usuario,
            'senha' => $senha_hash,
            'email' => $email,
            'nome' => $nome_formatado,
            'ativo' => $ativo
        );
    
        $this->db->query("INSERT INTO usuarios (usuario, senha, email, nome, ativo) VALUES (?, ?, ?, ?, ?)", 
            array($data['usuario'], $data['senha'], $data['email'], $data['nome'], $data['ativo']));
    
        $usuario_id_query = $this->db->query("SELECT LAST_INSERT_ID() AS usuario_id");
        $usuario_id = $usuario_id_query->row()->usuario_id;
    
        $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
        $this->load->model('Log_model');
        $dados_log = [
            'usuario' => $usuario,
            'email' => $email,
            'nome' => $nome
        ];
        $this->Log_model->registrar_log($usuario_id, 'Criou um usuário', 'usuarios', $usuario_id, json_encode($dados_log));
    
        redirect('auth');
    }
    

    public function cadastrar_admin() {

        $usuario = $this->input->post('usuario');
        $senha = $this->input->post('senha');
        $confirmar_senha = $this->input->post('confirmar_senha');
        $email = $this->input->post('email');
        $nome = $this->input->post('nome');
        $role = $this->input->post('role');
    
        if ($senha !== $confirmar_senha) {

            $this->session->set_flashdata('error', 'As senhas não coincidem.');
            redirect('auth/cadastro_admin');
            return;
        }

        $query = $this->db->query("SELECT * FROM usuarios WHERE usuario = ?", array($usuario));
        if ($query->num_rows() > 0) {

            $this->session->set_flashdata('error', 'Este nome de usuário já está em uso.');
            redirect('auth/cadastro_admin');
        }

        $query_email = $this->db->query("SELECT * FROM usuarios WHERE email = ?", array($email));
        if ($query_email->num_rows() > 0) {

            $this->session->set_flashdata('error', 'Este email já está registrado.');
            redirect('auth/cadastro_admin');
        }

        if (!preg_match("/^[A-Za-zÀ-ú']{2,40}(\s[A-Za-zÀ-ú']{2,40})+$/", $nome)) {

            $this->session->set_flashdata('error', 'O nome deve conter pelo menos duas palavras, cada uma com pelo menos 2 caracteres, e não pode conter caracteres especiais, com exceção do apóstrofo.');
            redirect('auth/cadastro_admin');
            return;
        }
        
        $nome_formatado = ucwords(strtolower($nome));
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            
        $data = array(
            'usuario' => $usuario,
            'senha' => $senha_hash,
            'email' => $email,
            'nome' => $nome_formatado,
            'ativo' => 1,
            'role' => $role
        );
            
        $this->db->query("INSERT INTO usuarios (usuario, senha, email, nome, ativo, role) VALUES (?, ?, ?, ?, ?, ?)", 
            array($data['usuario'], $data['senha'], $data['email'], $data['nome'], $data['ativo'], $data['role']));
            
        $usuario_id_query = $this->db->query("SELECT LAST_INSERT_ID() AS usuario_id");
        $usuario_id = $usuario_id_query->row()->usuario_id;
            
        $this->session->set_flashdata('success', 'Usuário cadastrado com sucesso!');
        $this->load->model('Log_model');
        $dados_log = [
            'usuario' => $usuario,
            'email' => $email,
            'nome' => $nome
        ];
        $this->Log_model->registrar_log($usuario_id, 'Criou um usuário', 'usuarios', $usuario_id, json_encode($dados_log));
            
        redirect('dashboard');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }

    private function registrar_falha_login($ip_address) {

        $sql = "SELECT * FROM login_attempts WHERE ip_address = ?";
        $query = $this->db->query($sql, [$ip_address]);
        $attempt = $query->row();

        if ($attempt) {
            $this->db->query("UPDATE login_attempts SET attempts = attempts + 1, last_attempt = ? WHERE ip_address = ?", [date('Y-m-d H:i:s'), $ip_address]);
        } else {
            $this->db->query("INSERT INTO login_attempts (ip_address, attempts) VALUES (?, 1)", [$ip_address]);
        }
    }

    private function ip_bloqueado($ip_address) {

        $sql = "SELECT * FROM login_attempts WHERE ip_address = ?";
        $query = $this->db->query($sql, [$ip_address]);
        $attempt = $query->row();

        if ($attempt && $attempt->attempts >= 3) {
            $tempo_espera = 5 * 60;
            $tempo_decorrido = time() - strtotime($attempt->last_attempt);

            if ($tempo_decorrido < $tempo_espera) {
                return true;
            } else {
                $this->db->query("DELETE FROM login_attempts WHERE ip_address = ?", [$ip_address]);
                return false;
            }
        }
        return false;
    }
}
