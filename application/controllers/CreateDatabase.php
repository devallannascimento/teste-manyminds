<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CreateDatabase extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function index() {
    
        if ($this->db->conn_id) {

            echo "Conexão com a datebase foi bem-sucedida!" . PHP_EOL;

            $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario VARCHAR(50) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                nome VARCHAR(100),
                ativo BOOLEAN DEFAULT TRUE,
                role ENUM('usuario', 'admin', 'master') DEFAULT 'usuario',
                criado TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )";
            if ($this->db->query($sql)) {
                echo "Tabela usuarios criada com sucesso!"  . PHP_EOL;
            } else {
                echo "Erro ao criar a tabela de usuarios!"  . PHP_EOL;
            }

            $sql2 = "CREATE TABLE IF NOT EXISTS login_attempts (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        ip_address VARCHAR(45) NOT NULL,
                        attempts INT NOT NULL DEFAULT 1,
                        last_attempt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                    );";
            if ($this->db->query($sql2)) {
                echo "Tabela de tentativas de login criada com sucesso!" . PHP_EOL;
            } else {
                echo "Erro ao criar a tabela de tentativas de login!" . PHP_EOL;
            }
            
            $sql3 = "CREATE TABLE IF NOT EXISTS enderecos (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                cep VARCHAR(10) NOT NULL,
                estado VARCHAR(50) NOT NULL,
                cidade VARCHAR(100) NOT NULL,
                bairro VARCHAR(100) NOT NULL,
                rua VARCHAR(150) NOT NULL,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
            );";
            if ($this->db->query($sql3)) {
                echo "Tabela de endereços criada com sucesso!"  . PHP_EOL;
            } else {
                echo "Erro ao criar a tabela de endereços!<br>" . PHP_EOL;
            } 

            $sql4 = "CREATE TABLE IF NOT EXISTS logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                acao VARCHAR(255) NOT NULL,
                tabela_afetada VARCHAR(255) NOT NULL,
                registro_id INT NOT NULL,
                detalhes TEXT,
                data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
            );";
            if ($this->db->query($sql4)) {
                echo "Tabela de logs criada com sucesso!"  . PHP_EOL;
            } else {
                echo "Erro ao criar a tabela de logs!" . PHP_EOL;
            }

            $sql5 = "CREATE TABLE IF NOT EXISTS api_keys (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                api_key VARCHAR(64) NOT NULL UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
            );";
            if ($this->db->query($sql5)) {
                echo "Tabela de api_keys criada com sucesso!" . PHP_EOL;
            } else {
                echo "Erro ao criar a tabela de api_keys!" . PHP_EOL;
            }

            $senha_master = password_hash('admin123', PASSWORD_DEFAULT);
            $dados_master = [
                'usuario' => 'master',
                'senha' => $senha_master,
                'email' => 'admin@example.com',
                'nome' => 'Administrador',
                'role' => 'master',
                'ativo' => TRUE
            ];
 
            $query = $this->db->query("SELECT * FROM usuarios WHERE role = 'master' LIMIT 1");
                if ($query->num_rows() == 0) {
                 $this->db->query("INSERT INTO usuarios (usuario, senha, email, nome, role, ativo) 
                                   VALUES ('{$dados_master['usuario']}', '{$dados_master['senha']}', 
                                           '{$dados_master['email']}', '{$dados_master['nome']}', 
                                           '{$dados_master['role']}', {$dados_master['ativo']})");
                echo "Usuário master criado com sucesso!" . PHP_EOL;
            } else {
                echo "Usuário master já existe!" . PHP_EOL;
            }
            
        } else {
            echo "Erro na conexão.";
        }
    }
}
