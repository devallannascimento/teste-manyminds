<?php
class Log_model extends CI_Model {

    public function registrar_log($usuario_id, $acao, $tabela, $registro_id, $detalhes = null) {
       
        $sql = "INSERT INTO logs (usuario_id, acao, tabela_afetada, registro_id, detalhes) VALUES (?, ?, ?, ?, ?)";
        return $this->db->query($sql, [$usuario_id, $acao, $tabela, $registro_id, $detalhes]);
    }

    public function listar_logs() {

        $sql = "SELECT logs.*, usuarios.usuario 
                FROM logs
                JOIN usuarios ON usuarios.id = logs.usuario_id
                ORDER BY logs.data DESC";

        $query = $this->db->query($sql);
        return $query->result();
    }

}
