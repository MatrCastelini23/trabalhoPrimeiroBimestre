CREATE TABLE IF NOT EXISTS listaDeAfazeres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    afazer VARCHAR(100) NOT NULL,
    completo BOOLEAN NOT NULL,
    data_realizada DATE
);

INSERT INTO listaDeAfazeres (afazer, completo, data_realizada) 
VALUES ('Lavar a roupa', true, '2026-04-01');

INSERT INTO listaDeAfazeres (afazer, completo, data_realizada) 
VALUES ('Limpar a casa', false, '2026-05-01');
