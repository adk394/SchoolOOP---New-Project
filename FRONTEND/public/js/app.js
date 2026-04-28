document.addEventListener('DOMContentLoaded', () => {
  const apiBase = window.API_BASE || 'http://localhost:8000';
  const view = document.getElementById('view');

  document.getElementById('nav-teachers').addEventListener('click', () => showList('teachers'));
  document.getElementById('nav-students').addEventListener('click', () => showList('students'));
  document.getElementById('nav-subjects').addEventListener('click', () => showList('subjects'));
  document.getElementById('nav-courses')?.addEventListener('click', () => showList('courses'));

  async function showList(resource) {
    view.innerHTML = '<h2>Loading...</h2>';
    try {
      const res = await fetch(`${apiBase}/${resource}`);
      if (!res.ok) throw new Error(res.statusText);
      const data = await res.json();
      render(resource, data);
    } catch (err) {
      view.innerHTML = `<div class="error">Error: ${err.message}</div>`;
    }
  }

  function render(resource, items) {
    const list = Array.isArray(items) ? items : (items && Array.isArray(items.data) ? items.data : []);
    const title = resource.charAt(0).toUpperCase() + resource.slice(1);
    const container = document.createElement('div');
    container.innerHTML = `
      <h2>${title}</h2>
      <div id="list"></div>
      <h3>Create ${title.slice(0, -1)}</h3>
      <form id="create-form">
        <div id="form-fields"></div>
        <button type="submit">Create</button>
      </form>
    `;

    const listEl = container.querySelector('#list');
    if (list.length === 0) {
      listEl.innerHTML = '<p>No items</p>';
    } else {
      const ul = document.createElement('ul');
      list.forEach(i => {
        const li = document.createElement('li');
        li.textContent = JSON.stringify(i);
        ul.appendChild(li);
      });
      listEl.appendChild(ul);
    }

    const formFields = container.querySelector('#form-fields');
    const defaultFields = {
      teachers: ['name', 'email'],
      students: ['name', 'email'],
      subjects: ['name', 'course_id'],
      courses: ['name']
    };
    const sample = list[0] || {};
    let keys = Object.keys(sample).filter(k => k !== 'id');
    if (keys.length === 0) {
      keys = defaultFields[resource] || ['name'];
    }
    keys.forEach(k => {
      const div = document.createElement('div');
      if (k === 'course_id') {
        div.innerHTML = `<label>${k}: <input name="${k}" type="number" min="1" required></label>`;
      } else if (k === 'email') {
        div.innerHTML = `<label>${k}: <input name="${k}" type="email" required></label>`;
      } else {
        div.innerHTML = `<label>${k}: <input name="${k}" required></label>`;
      }
      formFields.appendChild(div);
    });

    container.querySelector('#create-form').addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(e.target);
      const body = {};
      fd.forEach((v, k) => body[k] = v);
      try {
        const res = await fetch(`${apiBase}/${resource}`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(body)
        });
        if (!res.ok) {
          let errMsg = res.statusText;
          try {
            const errJson = await res.json();
            errMsg = errJson.error || errJson.message || JSON.stringify(errJson);
          } catch (_) {
          }
          throw new Error(`${res.status} ${errMsg}`);
        }
        await showList(resource);
      } catch (err) {
        alert('Error creating: ' + err.message);
      }
    });

    view.innerHTML = '';
    view.appendChild(container);
  }

  showList('teachers');
});
