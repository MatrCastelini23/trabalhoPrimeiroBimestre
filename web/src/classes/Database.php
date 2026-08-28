<?php

class Database{
    private $host = 'db';
    private $port = '3306';
    private $db = 'solove';
    private $user = 'root';
    private $pass = 'claudinhoEBuchecha';
    private $pdo;

    public function connect() {
        if(!this->pdo){
            try{
                $dns = "mysql:host={$this->host};port{$this->port};dbname={$this->db}";
                $this->pdo = new PDO($dns, $this->user, $this->pass);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Erro ao conectar ao banco de dados" .$e->getMessage());
            }
        }
        return $this->pdo;
    }
}