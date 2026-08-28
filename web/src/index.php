<?php
  require_once 'classes/ListaDeAfazeres.php';

  $afazeres = new ListaDeAfazeres();

  $lista = $afazeres->buscarlistaAfazeres();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $criar = $afazeres->criarTarefa($_POST['afazer']);
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
<style>
  body {
    background-color: #f4f6f9;
    min-height: 100vh;
  }
  .app-card {
    max-width: 640px;
    margin: 48px auto;
  }
  .task-item {
    transition: background-color .15s ease;
  }
  .task-item.done .task-text {
    text-decoration: line-through;
    color: #9aa0a6;
  }
  .task-text {
    word-break: break-word;
  }
  #emptyState {
    display: none;
  }
</style>
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
              <li><?= htmlspecialchars($l["afazer"] ?? 'Sem afazeres') ?></li>
              <button>Concluir</button>
              <button>Deletar</button>
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
  const STORAGE_KEY = 'tarefas';

  let tasks = JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
  let editingId = null;

  const taskForm = document.getElementById('taskForm');
  const taskInput = document.getElementById('taskInput');
  const taskList = document.getElementById('taskList');
  const emptyState = document.getElementById('emptyState');
  const counter = document.getElementById('counter');
  const editForm = document.getElementById('editForm');
  const editInput = document.getElementById('editInput');
  const editModal = new bootstrap.Modal(document.getElementById('editModal'));

  function save() {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(tasks));
  }

  function render() {
    taskList.innerHTML = '';

    if (tasks.length === 0) {
      emptyState.style.display = 'block';
    } else {
      emptyState.style.display = 'none';
    }

    tasks.forEach(task => {
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex align-items-center gap-2 task-item' + (task.done ? ' done' : '');

      li.innerHTML = `
        <input class="form-check-input mt-0 flex-shrink-0" type="checkbox" ${task.done ? 'checked' : ''}>
        <span class="task-text flex-grow-1">${escapeHtml(task.text)}</span>
        <button class="btn btn-sm btn-outline-secondary" title="Editar">
          <i class="bi bi-pencil"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" title="Excluir">
          <i class="bi bi-trash"></i>
        </button>
      `;

      li.querySelector('input[type="checkbox"]').addEventListener('change', () => {
        task.done = !task.done;
        save();
        render();
      });

      li.querySelector('.btn-outline-secondary').addEventListener('click', () => {
        editingId = task.id;
        editInput.value = task.text;
        editModal.show();
      });

      li.querySelector('.btn-outline-danger').addEventListener('click', () => {
        if (confirm('Excluir esta tarefa?')) {
          tasks = tasks.filter(t => t.id !== task.id);
          save();
          render();
        }
      });

      taskList.appendChild(li);
    });

    const total = tasks.length;
    const feitas = tasks.filter(t => t.done).length;
    counter.textContent = total > 0 ? `${feitas} de ${total} tarefa(s) concluída(s)` : '';
  }

  function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  taskForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = taskInput.value.trim();
    if (!text) return;

    tasks.push({
      id: Date.now().toString(),
      text,
      done: false
    });

    save();
    render();
    taskForm.reset();
    taskInput.focus();
  });

   editForm.addEventListener('submit', (e) => {
    e.preventDefault();
    const text = editInput.value.trim();
    if (!text || editingId === null) return;

    const task = tasks.find(t => t.id === editingId);
    if (task) {
      task.text = text;
      save();
      render();
    }

    editModal.hide();
    editingId = null;
  });

  render();
</script>

</body>
</html>