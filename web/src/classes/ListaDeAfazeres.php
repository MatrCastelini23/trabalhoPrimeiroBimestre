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
        // Imprime uma mensagem de confirmação direto na resposta da requisição
        echo "Tarefa criada";
    }

    public function atualizarTarefa($id, $data_realizada){
        $sql = "UPDATE listaDeAfazeres SET completo = :completo, data_realizada = :data_realizada WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':completo', 1, PDO::PARAM_INT);
        $stmt->bindParam(':data_realizada', $data_realizada);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        echo "Tarefa atualizada";
    }

    // Método novo: atualiza só o texto (afazer) de uma tarefa existente, sem mexer em completo/data
    public function editarTarefa($id, $afazer){
        $sql = "UPDATE listaDeAfazeres SET afazer = :afazer WHERE id = :id";
        // Prepara o comando SQL acima pra evitar SQL injection
        $stmt = $this->db->prepare($sql);
        // Associa o valor de $afazer ao placeholder :afazer da query preparada
        $stmt->bindParam(':afazer', $afazer);
        // Associa o valor de $id ao placeholder :id da query preparada
        $stmt->bindParam(':id', $id);
        // Executa o UPDATE de fato no banco, usando os valores associados acima
        $stmt->execute();
        // Imprime uma mensagem de confirmação direto na resposta da requisição
        echo "Tarefa editada";
    }

    public function deletarTarefa($id){
        $sql = "DELETE FROM listaDeAfazeres WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        // Imprime uma mensagem de confirmação direto na resposta da requisição
        echo "Tarefa Deletada";
    }
}