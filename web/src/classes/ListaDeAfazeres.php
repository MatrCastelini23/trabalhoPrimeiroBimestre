<?php

require_once 'Database.php';

class ListaDeAfazeres{
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function buscarlistaAfazeres(){
        $sql = "SELECT * FROM listaDeAfazeres";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function criarTarefa($afazer){
        $sql = "INSERT INTO listaDeAfazeres (afazer, completo, data_realizada) VALUES (:afazer, :completo, :data_realizada)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'afazer' => $afazer,
            'completo' => 0,
            'data_realizada' => '0000-00-00'
        ]);
    }

    public function atualizarTarefa($id, $data_realizada){
        $sql = "UPDATE listaDeAfazeres SET completo = :completo, data_realizada = :data_realizada WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':completo', 1);
        $stmt->bindParam(':data_realizada', $data_realizada);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Tarefa atualizada";
    }

    public function deletarTarefa($id){
        $sql = "DELETE FROM listaDeAfazeres WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
    }
}