<?php
class Endereco_model extends CI_Model {

    public function adicionar($dados) {
        $sql = "INSERT INTO enderecos (usuario_id, cep, estado, cidade, bairro, rua) VALUES (?, ?, ?, ?, ?, ?)";
        $this->db->query($sql, [
            $dados['usuario_id'], 
            $dados['cep'], 
            $dados['estado'], 
            $dados['cidade'], 
            $dados['bairro'], 
            $dados['rua']
        ]);
    }

   public function get_by_id($id) {
        $sql = "SELECT * FROM enderecos WHERE id = ?";
        $query = $this->db->query($sql, [$id]);
        return $query->row();
    }

    public function get_by_usuario($usuario_id) {
        $sql = "SELECT * FROM enderecos WHERE usuario_id = ? ORDER BY id DESC";
        $query = $this->db->query($sql, [$usuario_id]);
        return $query->result();
    }

    public function atualizar($id, $dados) {
        $sql = "UPDATE enderecos 
                SET cep = ?, estado = ?, cidade = ?, bairro = ?, rua = ? 
                WHERE id = ?";
        $this->db->query($sql, [
            $dados['cep'],
            $dados['estado'],
            $dados['cidade'],
            $dados['bairro'],
            $dados['rua'],
            $id
        ]);
    }

    public function excluir($id) {
        $sql = "DELETE FROM enderecos WHERE id = ?";
        $this->db->query($sql, [$id]);
    }
}
