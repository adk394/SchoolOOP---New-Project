

<?php $__env->startSection('content'); ?>
<div class="shell">
  <main class="main-content">
    <header class="topbar">
      <div class="topbar-left"><a class="topbar-brand" href="/">Faculty</a><nav id="main-nav" class="main-nav" aria-label="Navegación principal" style="display: none;"><button id="nav-teachers" class="nav-item">Profesores</button><button id="nav-students" class="nav-item">Estudiantes</button><button id="nav-subjects" class="nav-item">Asignaturas</button><button id="nav-courses" class="nav-item">Cursos</button></nav></div>
      <div id="auth-section" class="account-area">
        <span id="login-section"><button id="login-btn" class="login-button"><span class="google-g">G</span> Acceder</button></span>
        <span id="user-info" class="user-info" style="display: none;"><span class="user-copy"><strong id="user-name"></strong><small id="user-email"></small></span><button id="logout-btn" class="logout-button" aria-label="Cerrar sesión">Salir&nbsp; →</button></span>
      </div>
    </header>

    <section class="hero-panel">
      <div class="hero-copy"><p class="eyebrow">Faculty <span>•</span> Curso 2026</p><h1>Panel<br><em>académico.</em></h1><p class="hero-description">Administra profesores, estudiantes, asignaturas y cursos desde un único espacio.</p></div>
    </section>

    <section class="metric-strip" aria-label="Secciones de Faculty">
      <div><span>01</span><strong>Quiénes enseñan</strong><small>Profesores y responsables</small></div>
      <div><span>02</span><strong>Quiénes aprenden</strong><small>Estudiantes registrados</small></div>
      <div><span>03</span><strong>Qué ocurre</strong><small>Asignaturas y cursos</small></div>
    </section>
    <section id="view" aria-live="polite"></section>
  </main>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\izana\Downloads\programacion\Faculty\FRONTEND\views/app.blade.php ENDPATH**/ ?>