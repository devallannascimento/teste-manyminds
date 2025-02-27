<?php
class Api extends CI_Controller {

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
        $usuario = $this->Usuario_model->get_user_data($user_id);
    
        if ($usuario->role == 'master' || $usuario->role == 'admin') {
            $this->load->view('documentacao_api');
        } else {
            $this->load->view('dashboard');
        }
    }

    public function gerar_chave() {
        $user_id = $this->session->userdata('user_id');
        $usuario = $this->Usuario_model->get_user_data($user_id);
    
        if (!in_array($usuario->role, ['master', 'admin'])) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'error', 'message' => 'Acesso negado']));
        }

        $dados_log = [
            'usuario' => $usuario->usuario,
            'nome' => $usuario->nome
        ];
        $this->Log_model->registrar_log($usuario->id, 'Gerou uma api_key', 'api_keys', $usuario->id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));

        $api_key = $this->Usuario_model->gerar_api_key($user_id);
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['status' => 'success', 'api_key' => $api_key]));
    }

    public function logs() {
        header('Content-Type: application/json');
    
        $api_key = $this->input->get_request_header('Authorization', TRUE);
    
        if ($this->Usuario_model->validar_api_key($api_key)) {
            $logs = $this->Log_model->listar_logs();
    
            foreach ($logs as &$log) {
                if (!empty($log->detalhes)) {
                    $log->detalhes = json_decode($log->detalhes, true);
                }
            }
    
           echo json_encode([
                'status' => 'success',
                'logs' => $logs
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'API Key inválida'
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }
    }    
       
}
