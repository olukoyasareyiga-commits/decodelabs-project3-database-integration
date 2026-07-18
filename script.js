const API = {
  users: 'api/users.php',
  tasks: 'api/tasks.php',
};

async function loadUsers() {
  const res = await fetch(API.users);
  const users = await res.json();

  const list = document.getElementById('user-list');
  const select = document.getElementById('task-user');
  list.innerHTML = '';
  select.innerHTML = '';

  users.forEach(u => {
    const li = document.createElement('li');
    li.innerHTML = `<span>${u.name} (${u.email})</span>`;
    const delBtn = document.createElement('button');
    delBtn.textContent = 'Delete';
    delBtn.onclick = () => deleteUser(u.user_id);
    li.appendChild(delBtn);
    list.appendChild(li);

    const opt = document.createElement('option');
    opt.value = u.user_id;
    opt.textContent = u.name;
    select.appendChild(opt);
  });
}

async function loadTasks() {
  const res = await fetch(API.tasks);
  const tasks = await res.json();

  const list = document.getElementById('task-list');
  list.innerHTML = '';

  tasks.forEach(t => {
    const li = document.createElement('li');
    li.innerHTML = `<span>${t.title} <span class="status">${t.status}</span></span>`;
    const delBtn = document.createElement('button');
    delBtn.textContent = 'Delete';
    delBtn.onclick = () => deleteTask(t.task_id);
    li.appendChild(delBtn);
    list.appendChild(li);
  });
}

async function addUser(e) {
  e.preventDefault();
  const name = document.getElementById('user-name').value;
  const email = document.getElementById('user-email').value;

  const res = await fetch(API.users, {
    method: 'POST',
    body: JSON.stringify({ name, email }),
  });
  const result = await res.json();
  if (result.error) return alert(result.error);

  e.target.reset();
  loadUsers();
}

async function deleteUser(id) {
  await fetch(API.users, { method: 'DELETE', body: JSON.stringify({ id }) });
  loadUsers();
  loadTasks();
}

async function addTask(e) {
  e.preventDefault();
  const user_id = document.getElementById('task-user').value;
  const title = document.getElementById('task-title').value;

  const res = await fetch(API.tasks, {
    method: 'POST',
    body: JSON.stringify({ user_id, title }),
  });
  const result = await res.json();
  if (result.error) return alert(result.error);

  e.target.reset();
  loadTasks();
}

async function deleteTask(id) {
  await fetch(API.tasks, { method: 'DELETE', body: JSON.stringify({ id }) });
  loadTasks();
}

document.getElementById('user-form').addEventListener('submit', addUser);
document.getElementById('task-form').addEventListener('submit', addTask);

loadUsers();
loadTasks();
