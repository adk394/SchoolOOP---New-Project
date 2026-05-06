document.addEventListener('DOMContentLoaded', () => {
  const apiBase = window.API_BASE || 'http://localhost:8000/api';
  const view = document.getElementById('view');
  const resources = ['teachers', 'students', 'subjects', 'courses'];

  const labels = {
    id: 'ID',
    name: 'Nombre',
    email: 'Email',
    course_id: 'Curso',
    teacher_id: 'Profesor'
  };

  const listFields = {
    teachers: ['id', 'name', 'email'],
    students: ['id', 'name', 'email', 'course_id'],
    subjects: ['id', 'name', 'course_id', 'teacher_id'],
    courses: ['id', 'name']
  };

  const createFields = {
    teachers: ['name', 'email'],
    students: ['name', 'email', 'course_id'],
    subjects: ['name', 'course_id', 'teacher_id'],
    courses: ['name']
  };

  const editFields = {
    teachers: ['name', 'email'],
    students: ['name', 'email', 'course_id'],
    subjects: ['name', 'course_id', 'teacher_id'],
    courses: ['name']
  };

  let cache = {
    courses: [],
    teachers: []
  };

  resources.forEach(r => {
    const btn = document.getElementById(`nav-${r}`);
    if (btn) btn.addEventListener('click', () => showList(r));
  });

  async function request(path, options = {}) {
    const response = await fetch(`${apiBase}/${path}`, options);
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`${response.status} ${errorText || response.statusText}`);
    }
    return response.status === 204 ? null : response.json();
  }

  async function loadCourses() {
    if (cache.courses.length === 0) {
      const data = await request('courses');
      cache.courses = Array.isArray(data) ? data : data?.data || [];
    }
    return cache.courses;
  }

  async function loadTeachers() {
    if (cache.teachers.length === 0) {
      const data = await request('teachers');
      cache.teachers = Array.isArray(data) ? data : data?.data || [];
    }
    return cache.teachers;
  }

  async function showList(resource) {
    view.textContent = 'Cargando...';
    try {
      await Promise.all([loadCourses(), loadTeachers()]);
      const data = await request(resource);
      const items = Array.isArray(data) ? data : data?.data || [];
      render(resource, items);
    } catch (err) {
      view.innerHTML = `<div class="error">${escape(err.message)}</div>`;
    }
  }

  function render(resource, items) {
    const title = resource[0].toUpperCase() + resource.slice(1);
    const fields = createFields[resource] || ['name'];
    const list = listFields[resource] || ['id', 'name'];

    const tableHtml = items.length
      ? `<table>
          <thead>
            <tr>${list.map(f => `<th>${labels[f] || f}</th>`).join('')}<th>Acciones</th></tr>
          </thead>
          <tbody>
            ${items.map(item => `
              <tr>
                ${list.map(f => `<td>${formatValue(f, item[f])}</td>`).join('')}
                <td>
                  <button type="button" data-action="edit" data-id="${item.id}">Editar</button>
                  <button type="button" data-action="delete" data-id="${item.id}">Borrar</button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>`
      : '<p>No hay elementos.</p>';

    view.innerHTML = `
      <h2>${title}</h2>
      <div id="list">${tableHtml}</div>
      <div id="edit-form-container"></div>
      <h3>Crear ${title.slice(0, -1)}</h3>
      <form id="create-form">
        ${fields.map(f => renderInput(f)).join('')}
        <button type="submit">Crear</button>
      </form>
    `;

    view.querySelectorAll('button[data-action]').forEach(btn => {
      btn.addEventListener('click', () => {
        const action = btn.dataset.action;
        const id = btn.dataset.id;
        if (action === 'edit') renderEditForm(resource, items.find(i => String(i.id) === id));
        if (action === 'delete') deleteItem(resource, id);
      });
    });

    document.getElementById('create-form').addEventListener('submit', e => submitForm(e, resource));
  }

  function renderInput(field, value = '', required = true) {
    if (field === 'course_id') {
      const options = cache.courses.map(c => `<option value="${c.id}" ${value == c.id ? 'selected' : ''}>${escape(c.name)}</option>`).join('');
      return `<div><label>${labels[field] || field}: <select name="${field}" ${required ? 'required' : ''}><option value="">Selecciona curso</option>${options}</select></label></div>`;
    }
    if (field === 'teacher_id') {
      const options = cache.teachers.map(t => `<option value="${t.id}" ${value == t.id ? 'selected' : ''}>${escape(t.name)}</option>`).join('');
      return `<div><label>${labels[field] || field}: <select name="${field}"><option value="">Sin profesor</option>${options}</select></label></div>`;
    }
    const type = field === 'email' ? 'email' : 'text';
    return `<div><label>${labels[field] || field}: <input name="${field}" value="${escape(value)}" type="${type}" ${required ? 'required' : ''}></label></div>`;
  }

  function renderEditForm(resource, item) {
    if (!item) return;
    const title = resource[0].toUpperCase() + resource.slice(1);
    const fields = editFields[resource] || ['name'];
    const container = document.getElementById('edit-form-container');

    container.innerHTML = `
      <h3>Editar ${title.slice(0, -1)}</h3>
      <form id="edit-form">
        <input type="hidden" name="id" value="${item.id}">
        ${fields.map(f => renderInput(f, item[f] ?? '', f !== 'teacher_id')).join('')}
        <button type="submit">Guardar</button>
        <button type="button" id="cancel-edit">Cancelar</button>
      </form>
    `;

    document.getElementById('edit-form').addEventListener('submit', e => submitEdit(e, resource, item.id));
    document.getElementById('cancel-edit').addEventListener('click', () => container.innerHTML = '');
  }

  async function submitForm(e, resource) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    if (data.course_id) data.course_id = parseInt(data.course_id);
    if (data.teacher_id === '') {
      data.teacher_id = null;
    } else if (data.teacher_id) {
      data.teacher_id = parseInt(data.teacher_id);
    }
    
    try {
      await request(resource, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      cache.courses = [];
      cache.teachers = [];
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  async function submitEdit(e, resource, id) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    if (data.course_id) data.course_id = parseInt(data.course_id);
    if (data.teacher_id === '') {
      data.teacher_id = null;
    } else if (data.teacher_id) {
      data.teacher_id = parseInt(data.teacher_id);
    }
    
    try {
      await request(`${resource}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      cache.courses = [];
      cache.teachers = [];
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  async function deleteItem(resource, id) {
    if (!confirm('Eliminar este elemento?')) return;
    try {
      await request(`${resource}/${id}`, { method: 'DELETE' });
      cache.courses = [];
      cache.teachers = [];
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  function formatValue(field, value) {
    if (value === null || value === undefined || value === '') {
      return field === 'teacher_id' ? 'Sin profesor' : '-';
    }
    if (field === 'course_id') {
      const course = cache.courses.find(c => c.id == value);
      return course ? escape(course.name) : escape(value);
    }
    if (field === 'teacher_id') {
      const teacher = cache.teachers.find(t => t.id == value);
      return teacher ? escape(teacher.name) : escape(value);
    }
    return escape(value);
  }

  function escape(value) {
    return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  showList('teachers');
});
