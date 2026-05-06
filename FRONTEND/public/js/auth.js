(function() {
  const apiBase = window.API_BASE || 'http://localhost:8000/api';

  const AuthStorage = {
    getToken: () => localStorage.getItem('jwt_token'),
    setToken: (token) => localStorage.setItem('jwt_token', token),
    removeToken: () => localStorage.removeItem('jwt_token'),
    getUser: () => {
      const user = localStorage.getItem('user');
      return user ? JSON.parse(user) : null;
    },
    setUser: (user) => localStorage.setItem('user', JSON.stringify(user)),
    removeUser: () => localStorage.removeItem('user'),
    clear: () => {
      localStorage.removeItem('jwt_token');
      localStorage.removeItem('user');
    }
  };

  window.Auth = {
    isAuthenticated: () => !!AuthStorage.getToken(),
    getToken: () => AuthStorage.getToken(),
    getUser: () => AuthStorage.getUser(),

    login: async () => {
      try {
        const response = await fetch(`${apiBase}/auth/login`);
        const data = await response.json();

        if (data.auth_url) {
          window.location.href = data.auth_url;
        } else {
          alert('error al obtener url de autenticacion');
        }
      } catch (err) {
        alert('error de conexion: ' + err.message);
      }
    },

    logout: () => {
      AuthStorage.clear();
      updateUI();
      document.getElementById('view').innerHTML = '';
    },

    handleCallback: async () => {
      const urlParams = new URLSearchParams(window.location.search);
      const token = urlParams.get('token');
      const error = urlParams.get('error');

      if (error) {
        alert('error de autenticacion: ' + error);
        window.history.replaceState({}, document.title, '/');
        return false;
      }

      if (token) {
        const user = {
          google_id: urlParams.get('google_id'),
          email: urlParams.get('email'),
          name: urlParams.get('name')
        };

        AuthStorage.setToken(token);
        AuthStorage.setUser(user);
        window.history.replaceState({}, document.title, '/');
        updateUI();
        return true;
      }

      return false;
    },

    fetchWithAuth: async (url, options = {}) => {
      const token = AuthStorage.getToken();

      const headers = {
        'Content-Type': 'application/json',
        ...options.headers
      };

      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }

      const response = await fetch(url, {
        ...options,
        headers
      });

      if (response.status === 401) {
        AuthStorage.clear();
        updateUI();
        alert('sesion expirada. inicia sesion de nuevo.');
        throw new Error('unauthorized');
      }

      return response;
    }
  };

  function updateUI() {
    const isAuth = window.Auth.isAuthenticated();
    const user = AuthStorage.getUser();

    const loginSection = document.getElementById('login-section');
    const userInfo = document.getElementById('user-info');
    const mainNav = document.getElementById('main-nav');

    if (isAuth && user) {
      loginSection.style.display = 'none';
      userInfo.style.display = 'inline';
      mainNav.style.display = 'block';
      document.getElementById('user-name').textContent = user.name;
      document.getElementById('user-email').textContent = user.email;
    } else {
      loginSection.style.display = 'inline';
      userInfo.style.display = 'none';
      mainNav.style.display = 'none';
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    window.Auth.handleCallback().then(() => {
      updateUI();
    });

    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');

    if (loginBtn) {
      loginBtn.addEventListener('click', window.Auth.login);
    }

    if (logoutBtn) {
      logoutBtn.addEventListener('click', window.Auth.logout);
    }
  });
})();
