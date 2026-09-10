<?php
  require_once 'classes/ListaDeAfazeres.php';

  $afazeres = new ListaDeAfazeres();

  $lista = $afazeres->buscarlistaAfazeres();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $afazeres->criarTarefa($_POST['afazer']);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

  if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $dados = json_decode(file_get_contents('php://input'), true);

    // Decide qual operação rodar dependendo do que veio no corpo da requisição PUT
    if (isset($dados['afazer'])) {
        // O payload contém o texto da tarefa - é uma edição
        $afazeres->editarTarefa($dados['id'], $dados['afazer']);
    } else {
        // Sem texto - é uma conclusão de tarefa
        $afazeres->atualizarTarefa($dados['id'], date('Y-m-d'));
    }
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $afazeres->deletarTarefa($dados['id']);
    exit;
  }
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Minhas Tarefas</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="/css/style.css">
</head>
<body>

<div class="container app-card">
  <div class="card shadow-sm border-0">
    <div class="card-body p-4">

      <h1 class="h4 mb-1">Minhas Tarefas</h1>
      <p class="text-muted small mb-4">Adicione, edite, marque como feita ou exclua suas tarefas.</p>

      <form method="POST" class="d-flex gap-2 mb-4">
        <input
          type="text"
          name="afazer"
          class="form-control"
          placeholder="Digite uma nova tarefa..."
          required
          maxlength="200"
        >
        <button type="submit" class="btn btn-primary flex-shrink-0">
          <i class="bi bi-plus-lg"></i> Adicionar
        </button>
      </form>

      <ul  class="list-group">
        <?php if(empty($lista)): ?>
          <div class="text-center text-muted py-5">
            <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
            Nenhuma tarefa pendente.
          </div>
          <?php else: ?>
          <?php foreach($lista as $l): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span<?= $l['completo'] == 1 ? ' class="text-decoration-line-through text-muted"' : '' ?>><?= htmlspecialchars($l["afazer"] ?? 'Sem afazeres') ?></span>
                <span class="flex-shrink-0">
                  <!-- Botão: ao clicar, chama a função JS abrirEdicao(), passando o id e o texto atual da tarefa-->
                  <button type="button" class="btn btn-sm btn-outline-secondary" onclick="abrirEdicao(<?= (int)$l['id'] ?>, <?= htmlspecialchars(json_encode($l['afazer'] ?? ''), ENT_QUOTES, 'UTF-8') ?>)">Editar</button>
                  <?php if($l['completo'] != 1): ?>
                    <button type="button" class="btn btn-sm btn-success" onclick="concluirTarefa(<?= (int)$l['id'] ?>)">Concluir</button>
                  <?php endif; ?>
                  <button type="button" class="btn btn-sm btn-danger" onclick="deletarTarefa(<?= (int)$l['id'] ?>)">Deletar</button>
                </span>
              </li>
          <?php endforeach; ?>
        <?php endif; ?>
      </ul>

      <div id="counter" class="text-muted small mt-3"></div>

    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editForm">
        <div class="modal-header">
          <h5 class="modal-title">Editar tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="text" id="editInput" class="form-control" maxlength="200" required>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>

<script>
  let tarefaEmEdicaoId = null;
  // Cria a instância do modal do Bootstrap, ligada à div editModal do HTML
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));

  // Chamada pelo onclick do botão "Editar" de cada tarefa
  function abrirEdicao(id, afazerAtual) {
    // Guarda o id da tarefa que está sendo editada, pra usar depois no submit do form
    tarefaEmEdicaoId = id;
    // Preenche o campo de texto do modal com o valor atual da tarefa
    document.getElementById('editInput').value = afazerAtual;
    // Abre o modal de edição na tela
    editModal.show();
  }

  // Escuta o envio do formulário de edição
  document.getElementById('editForm').addEventListener('submit', function (e) {
    // Impede o comportamento padrão do form (que recarregaria a página sozinho)
    e.preventDefault();
    const novoAfazer = document.getElementById('editInput').value.trim();
    // Se o campo estiver vazio, ou não houver tarefa selecionada, não faz nada
    if (!novoAfazer || tarefaEmEdicaoId === null) return;

    // Dispara a atualização de fato, mandando o id e o novo texto
    editarTarefa(tarefaEmEdicaoId, novoAfazer);
  });

  // Manda ao servidor o texto editado da tarefa
  function editarTarefa(id, afazer) {
    // Faz uma requisição PUT pra própria página, com o corpo em JSON
    fetch(window.location.href, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id, afazer: afazer })
    // Quando a resposta chega, recarrega a página pra mostrar a tarefa já atualizada
    }).then(() => location.reload());
  }

  function concluirTarefa(id) {
    fetch(window.location.href, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    }).then(() => location.reload());
  }

  function deletarTarefa(id) {
    fetch(window.location.href, {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: id })
    }).then(() => location.reload());
  }
</script>

</body>
</html>