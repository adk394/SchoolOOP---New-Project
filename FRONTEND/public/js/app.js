document.addEventListener('DOMContentLoaded', () => {
  const apiBase = window.API_BASE || 'http://localhost:8000/api';
  const view = document.getElementById('view');
  const resources = ['teachers', 'students', 'subjects', 'courses'];

  const displayLabels = {
    id: 'ID',
    name: 'Nombre',
    email: 'Email',
    course_id: 'Curso',
    teacher_id: 'Profesor'
  };

  const listFieldsByResource = {
    teachers: ['id', 'name', 'email'],
    students: ['id', 'name', 'email', 'course_id'],
    subjects: ['id', 'name', 'course_id', 'teacher_id'],
    courses: ['id', 'name']
  };

  const createFieldsByResource = {
    teachers: ['name', 'email'],
    students: ['name', 'email'],
    subjects: ['name', 'course_id'],
    courses: ['name']
  };

  const editFieldsByResource = {
    teachers: ['name', 'email'],
    students: ['name', 'email'],
    subjects: ['name', 'course_id', 'teacher_id'],
    courses: ['name']
  };

  resources.forEach(resource => {
    const button = document.getElementById(`nav-${resource}`);
    if (button) button.addEventListener('click', () => showList(resource));
  });

  async function request(path, options) {
    const response = await fetch(`${apiBase}/${path}`, options);
    if (!response.ok) {
      const errorText = await response.text();
      throw new Error(`${response.status} ${errorText || response.statusText}`);
    }
    return response.status === 204 ? null : response.json();
  }

  async function showList(resource) {
    view.textContent = 'Cargando...';
    try {
      const data = await request(resource);
      const items = Array.isArray(data) ? data : data?.data || [];
      render(resource, items);
    } catch (err) {
      view.innerHTML = `<div class="error">${escape(err.message)}</div>`;
    }
  }

  function render(resource, items) {
    const title = resource[0].toUpperCase() + resource.slice(1);
    const createFields = createFieldsByResource[resource] || ['name'];
    const listFields = listFieldsByResource[resource] || ['id', 'name'];

    const listHtml = items.length
      ? `
        <table>
          <thead>
            <tr>${listFields.map(field => `<th>${displayLabels[field] || field}</th>`).join('')}<th>Acciones</th></tr>
          </thead>
          <tbody>
            ${items.map(item => `
              <tr>
                ${listFields.map(field => `<td>${formatValue(field, item[field])}</td>`).join('')}
                <td>
                  <button type="button" data-action="edit" data-id="${item.id}">Editar</button>
                  <button type="button" data-action="delete" data-id="${item.id}">Borrar</button>
                </td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `
      : '<p>No hay elementos.</p>';

    view.innerHTML = `
      <h2>${title}</h2>
      <div id="list">${listHtml}</div>
      <div id="edit-form-container"></div>
      <h3>Crear ${title.slice(0, -1)}</h3>
      <form id="create-form">
        ${createFields.map(field => renderInput(field)).join('')}
        <button type="submit">Crear</button>
      </form>
    `;

    view.querySelectorAll('button[data-action]').forEach(button => {
      const action = button.dataset.action;
      const id = button.dataset.id;
      button.addEventListener('click', () => {
        if (action === 'edit') return renderEditForm(resource, items.find(item => String(item.id) === id));
        if (action === 'delete') return deleteItem(resource, id);
      });
    });

    document.getElementById('create-form').addEventListener('submit', event => submitForm(event, resource));
  }

  function renderInput(field, value = '', required = true) {
    const type = field === 'email' ? 'email' : field === 'course_id' || field === 'teacher_id' ? 'number' : 'text';
    const attrs = `${type === 'number' ? 'min="1"' : ''} ${required ? 'required' : ''}`;
    return `<div><label>${displayLabels[field] || field}: <input name="${field}" value="${escape(value)}" type="${type}" ${attrs}></label></div>`;
  }

  function renderEditForm(resource, item) {
    if (!item) return;

    const title = resource[0].toUpperCase() + resource.slice(1);
    const editFields = editFieldsByResource[resource] || ['name'];
    const container = document.getElementById('edit-form-container');

    container.innerHTML = `
      <h3>Editar ${title.slice(0, -1)}</h3>
      <form id="edit-form">
        <input type="hidden" name="id" value="${item.id}">
        ${editFields.map(field => renderInput(field, item[field] ?? '', field !== 'teacher_id')).join('')}
        <button type="submit">Guardar</button>
        <button type="button" id="cancel-edit">Cancelar</button>
      </form>
    `;

    document.getElementById('edit-form').addEventListener('submit', event => submitEdit(event, resource, item.id));
    document.getElementById('cancel-edit').addEventListener('click', () => {
      container.innerHTML = '';
    });
  }

  async function submitForm(event, resource) {
    event.preventDefault();
    const data = Object.fromEntries(new FormData(event.target));
    try {
      await request(resource, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  async function submitEdit(event, resource, id) {
    event.preventDefault();
    const formData = Object.fromEntries(new FormData(event.target));
    const data = { ...formData };

    if (resource === 'subjects' && Object.prototype.hasOwnProperty.call(data, 'teacher_id') && data.teacher_id === '') {
      data.teacher_id = null;
    }

    try {
      await request(`${resource}/${id}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  async function deleteItem(resource, id) {
    if (!confirm('Eliminar este elemento?')) return;
    try {
      await request(`${resource}/${id}`, { method: 'DELETE' });
      showList(resource);
    } catch (err) {
      alert(err.message);
    }
  }

  function formatValue(field, value) {
    if (value === null || value === undefined || value === '') {
      return field === 'teacher_id' ? 'Sin profesor' : '-';
    }
    return escape(value);
  }

  function escape(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }

  showList('teachers');
});
