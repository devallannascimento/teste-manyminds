<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Usuario_model');
        $this->load->model('Endereco_model');
        $this->load->model('Log_model');
        $this->load->helper('url');
        $this->load->library('session');
    }

    public function index() {

        $user_id = $this->session->userdata('user_id');
        $usuario = $this->Usuario_model->get_user_data($user_id);
    
        if ($usuario->role == 'master') {
            $data['usuarios'] = $this->Usuario_model->get_all_users();
        } else if ($usuario->role == 'admin') {
            $data['usuarios'] = $this->Usuario_model->get_users_admin();
        } else {
            $data['usuarios'] = [$usuario];
        }

        $this->load->view('dashboard', $data);
    }

    public function cadastro_endereco($id) {
        
        $usuario = $this->Usuario_model->get_user_data($id);
        if (!$usuario) {
            redirect('auth/login');
        }

        $data['user'] = $usuario;
        $this->load->view('cadastrar_endereco', $data);
    }

    public function inativar($id) {

        $user_id = $this->session->userdata('user_id');
        $usuario = $this->Usuario_model->get_user_data($user_id);
    
        $this->Usuario_model->inativar($id);
        $usuarioAfetado = $this->Usuario_model->get_user_data($id);
        $this->session->set_flashdata('success', 'Usuário inativado!');

        $dados_log = [
            'usuario' => $usuarioAfetado->usuario,
            'email' => $usuarioAfetado->email,
            'nome' => $usuarioAfetado->nome
        ];
        $this->Log_model->registrar_log($usuario->id, 'Inativou um usuário', 'usuarios', $id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));

        redirect('dashboard');
    }

    public function reativar($id) {

        $user_id = $this->session->userdata('user_id');
        $usuarioAfetado = $this->Usuario_model->get_user_data($id);
        $usuario = $this->Usuario_model->get_user_data($user_id);

        $this->Usuario_model->reativar($id);
        $this->session->set_flashdata('success', 'Usuário reativado!');

        $dados_log = [
            'usuario' => $usuarioAfetado->usuario,
            'email' => $usuarioAfetado->email,
            'nome' => $usuarioAfetado->nome
        ];
        $this->Log_model->registrar_log($usuario->id, 'Reativou um usuário', 'usuarios', $id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));

        redirect('dashboard');
    }

    public function editar_usuario($id) {

        $data['usuario'] = $this->Usuario_model->get_user_data($id);
        $this->load->view('editar_usuario', $data);
    }

    public function atualizar_usuario($id) {
        
        $nome = $this->input->post('nome');
        $email = $this->input->post('email');
            
        if (!preg_match("/^[A-Za-zÀ-ú']{2,40}(\s[A-Za-zÀ-ú']{2,40})+$/", $nome)) {
            $this->session->set_flashdata('error', 'O nome deve conter pelo menos duas palavras, cada uma com pelo menos 2 caracteres, e não pode conter caracteres especiais, com exceção do apóstrofo.');
            redirect('dashboard/editar_usuario/'.$id);
        }
    
        $nome_formatado = ucwords(strtolower($nome));
    
        $dados = [
            'nome' => $nome_formatado,
            'email' => $email
        ];
    
        $usuario_antigo = $this->Usuario_model->get_user_data($id);
    
        $this->Usuario_model->atualizar($id, $dados);
        $this->session->set_flashdata('success', 'Usuário atualizado com sucesso!');
    
        $user_id = $this->session->userdata('user_id');
        $usuario_logado = $this->Usuario_model->get_user_data($user_id);
    
        $dados_log = [
            'usuario' => $usuario_antigo->usuario,
                'usuario_antigo' => [
                'nome' => $usuario_antigo->nome,
                'email' => $usuario_antigo->email
            ],
            'usuario_novo' => $dados
        ];
        $this->Log_model->registrar_log($usuario_logado->id, 'Editou dados do usuário', 'usuarios', $id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));
        
        redirect('dashboard');
    }

    public function adicionar_endereco($id) {
        
        $usuario_id = $id;
        $cep = $this->input->post('cep');
        $estado = $this->input->post('estado');
        $cidade = $this->input->post('cidade');
        $bairro = $this->input->post('bairro');
        $rua = $this->input->post('rua');
        $numero = $this->input->post('numero');

        $endereco_completo = $rua . ', ' . $numero;
    
        if ($usuario_id && $cep && $estado && $cidade && $bairro && $rua) {
            
            $dados = [
                'usuario_id' => $usuario_id,
                'cep' => $cep,
                'estado' => $estado,
                'cidade' => $cidade,
                'bairro' => $bairro,
                'rua' => $endereco_completo
            ];
    
            $this->Endereco_model->adicionar($dados);

            $user_id = $this->session->userdata('user_id');
            $usuario = $this->Usuario_model->get_user_data($user_id);
            $usuarioAfetado = $this->Usuario_model->get_user_data($usuario_id);

            $sql = "SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY id DESC LIMIT 1";
            $query = $this->db->query($sql, [$usuario_id]);
            $novo_endereco = $query->row();

            $dados_log = [
                'usuario' => [
                    'id' => $usuarioAfetado->id,
                    'usuario' => $usuarioAfetado->usuario
                ],
                'endereco' => "$novo_endereco->rua - $novo_endereco->cep"
            ];
            $this->Log_model->registrar_log($usuario->id, 'Adicionou um endereço', 'enderecos', $novo_endereco->id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));
            $this->session->set_flashdata('success', 'Endereço adicionado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Por favor, preencha todos os campos!');
        }

        redirect('dashboard');
    }
    
    public function editar_endereco($id) {
        
        $data['endereco'] = $this->Endereco_model->get_by_id($id);
        $this->load->view('editar_endereco', $data);
    }

    public function atualizar_endereco($id){

        $dados = [
            'cep' => $this->input->post('cep'),
            'estado' => $this->input->post('estado'),
            'cidade' => $this->input->post('cidade'),
            'bairro' => $this->input->post('bairro'),
            'rua' => $this->input->post('rua')
        ];

        $endereco_antigo = $this->Endereco_model->get_by_id($id);

        $this->Endereco_model->atualizar($id, $dados);
        $this->session->set_flashdata('success', 'Endereço atualizado com sucesso!');

        $user_id = $this->session->userdata('user_id');
        $usuarioAfetado = $this->Usuario_model->get_user_data($endereco_antigo->usuario_id);
        $usuario = $this->Usuario_model->get_user_data($user_id);

        $dados_log = [
            'usuario' => $usuarioAfetado->usuario,
            'endereco_antigo' => "$endereco_antigo->rua - $endereco_antigo->cep",
            'endereco_novo' => "{$dados['rua']} - {$dados['cep']}"
        ];
        $this->Log_model->registrar_log($usuario->id, 'Editou um endereço', 'enderecos', $id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));
        
        redirect('dashboard');
    }
    
    public function excluir_endereco($id) {
        
        $user_id = $this->session->userdata('user_id');
        $usuario = $this->Usuario_model->get_user_data($user_id);
        $endereco = $this->Endereco_model->get_by_id($id);
        $usuarioAfetado = $this->Usuario_model->get_user_data($endereco->usuario_id);

        $this->Endereco_model->excluir($id);
        $this->session->set_flashdata('success', 'Endereço excluído com sucesso!');
        
        $dados_log = [
            'usuario' => $usuarioAfetado->usuario,
            'endereco' => "$endereco->rua - $endereco->cep"
        ];
        $this->Log_model->registrar_log($usuario->id, 'Excluiu um endereço', 'enderecos', $endereco->id, json_encode($dados_log, JSON_UNESCAPED_UNICODE));
        
        redirect('dashboard');
    }
    
    public function exibir_enderecos($usuario_id) {

        $enderecos = $this->Endereco_model->get_by_usuario($usuario_id);
        $data['enderecos'] = $this->Endereco_model->get_by_usuario($user->id);
        $this->load->view('dashboard', $data);
    }
}
