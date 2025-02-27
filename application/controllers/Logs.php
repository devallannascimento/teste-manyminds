<?php
class Logs extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Usuario_model');
        $this->load->model('Log_model');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index() {

        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            redirect('auth');
        }

        $usuario = $this->Usuario_model->get_user_data($user_id);
        if ($usuario && !in_array($usuario->role, ['master', 'admin'])) {
            redirect('dashboard');
        }

        $data['logs'] = $this->Log_model->listar_logs();
        $this->load->view('logs', $data);
    }
}
