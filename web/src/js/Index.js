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
