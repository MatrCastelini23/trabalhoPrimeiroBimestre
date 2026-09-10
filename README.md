# Trabalho DevOps -- To Do List 
### Criação de docker-compose + Dockerfile

## Requisitos:
Para rodar esse projeto é necessário ter instalado na sua máquina:
- Docker
- Docker Compose

Recomendação: instalar Docker Desktop que inclui os dois requisitos acima.

## Passo a passo:

- Clone o repositorio com o comando:
    - git clone https://github.com/MatrCastelini23/trabalhoPrimeiroBimestre.git
- No bash use o comando Docker (Em caso de utilizar Docker Desktop, o mesmo deve estar aberto no computador):
    - docker compose up -d
    - Caso tenha o docker desktop confira os container no painel
- Abra no navegador o endereço:
    - http://localhost:8080
- Utilize a lista para criar, ler, concluir ou atualizar, e deletar.

## Explicação: 

O arquivo docker-compose.yml possui três serviços, um serviço de php-apache, um serviço de phpMyAdmin e um serviço de banco de dados MariaDB.

- O container de php-apache é contruído através de um Dockerfile no diretório web, o Dockerfile fica reponsavel por direcionar o compose a versão da imagem, pela pasta de dentro do sistema Docker, pelos comando RUN que habilitam o modulol apache e baixam o drive de conexão com o banco (PDO). Este container tem a função de servidor web e interpretador dos arquivos php para acesso no navegador. No navegador o usuario pode administrar sua lista de tarefas. 

- O container de MariaDB contruido com uma imagem direto do docker hub. É o banco de dados utilizado para o armazenamento das tarefas da lista. A criação das tabelas e das informações pré-definidas (Seeds) são responsabilida do volume ./db.sql. Toda vez que o container sobe, o compose checa se o volume existe, se não existir, o volume é criado e SQL cria a tabela e preeche o banco soLove.

- O container de phpMyAdmin possui uma imagem retirada direto do docker hub, ela serve para o acesso ao painel administrador do banco de dados.

A rede utilizada foi a criada pelo próprio compose. 

## Observações:

- Utilizamos a rede que o próprio compose cria entre os containers para facilitar o desenvolvimento e diminuir o tamanho do docker-compose.yml.

- Personalizamos a imagem do php-apache no Dockerfile também com a finalidade de diminuir o arquivo docker-compose.yml, uma vez que podemos utizar o comando RUN para ativar o modulos desejados para o servidor apache e então contrui-lo no docker-compose com a sequencia build -> context -> dockerfile.

- Criamos também um arquivo na raiz do projeto chamado db.sql. Esse arquivo foi colocado dentro dos volumes do serviço db, funcionando como Seeds do projeto, uma vez que ao subir os container do banco pela primeira vez no ambiente, esse SQL será executado dentro do banco.

- Uma observação sobre o banco de dados. O banco de dados leva alguns segundos a mais para subir do que os outros serviços, logo se subir todos os containers e já abrir o navegador, você pode se deparar com um erro de conexão com o banco. Logo o ideal é esperar antes de acessar web a lista.

## Alunos:

- Matheus Janoca Castelini -- R.A: 250289
- Pedro Herinque Gonçalvez de Souza -- R.A: 250275