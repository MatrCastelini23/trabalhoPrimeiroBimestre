<?php

class Database{
    private $host = 'db';
    private $port = '3306';
    private $db = 'soLove';
    private $user = 'root';
    private $pass = 'claudinhoEBuchecha';
    private $pdo;

    public function connect() {
        if(!$this->pdo){
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db}";
            $lastException = null;

            for ($attempt = 1; $attempt <= 10; $attempt++) {
                try {
                    $this->pdo = new PDO($dsn, $this->user, $this->pass);
                    $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    break;
                } catch(PDOException $e) {
                    $lastException = $e;
                    usleep(500000);
                }
            }

            if (!$this->pdo) {
                die("Erro ao conectar ao banco de dados: " . $lastException->getMessage());
            }
        }
        return $this->pdo;
    }
}
?>