@extends('layout')

@section('content')
<div>
  <h1>SchoolOOP / Izan Aranda Valverde </h1>
  <div id="auth-section" style="margin-bottom: 15px; padding: 10px; background: #f5f5f5; border-radius: 5px;">
    <span id="user-info" style="display: none;">
      Bienvenido, <strong id="user-name"></strong> (<span id="user-email"></span>)
      <button id="logout-btn" style="margin-left: 10px;">Cerrar sesión</button>
    </span>
    <span id="login-section">
      <button id="login-btn">Iniciar sesión con Google</button>
    </span>
  </div>
  <nav id="main-nav" style="display: none;">
    <button id="nav-teachers">Profesores</button>
    <button id="nav-students">Estudiantes</button>
    <button id="nav-subjects">Asignaturas</button>
    <button id="nav-courses">Cursos</button>
  </nav>
  <div id="view"></div>
</div>
@endsection
