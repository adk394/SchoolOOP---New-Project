<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management</title>
    <style>
        body { font-family: sans-serif; max-width: 900px; margin: 20px auto; padding: 0 16px; }
        h1 { margin-bottom: 8px; }
        .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 8px; margin-bottom: 16px; }
        .menu a { display: block; border: 1px solid #ddd; border-radius: 6px; padding: 8px; text-decoration: none; color: #111; }
        .card { border: 1px solid #ddd; border-radius: 8px; padding: 12px; }
        label { display: block; margin-top: 8px; }
        input { width: 100%; padding: 6px; margin-top: 2px; }
        button { margin-top: 12px; padding: 8px 10px; }
        .ok { background: #e6ffed; border: 1px solid #a6d8b2; padding: 8px; border-radius: 6px; }
        .error { background: #ffeaea; border: 1px solid #d8a6a6; padding: 8px; border-radius: 6px; }
        .help { color: #555; margin-bottom: 10px; }
    </style>
</head>
<body>
<h1>School Management de Izan Aranda</h1>
<p class="help">Cada caso de uso tiene su propia URL.</p>

<?php if (!empty($success)): ?>
    <p class="ok"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>

<nav class="menu">
    <a href="/?route=home">Inicio</a>
    <a href="/?route=create-student">CreateStudent</a>
    <a href="/?route=create-course">CreateCourse</a>
    <a href="/?route=create-subject">CreateSubject</a>
    <a href="/?route=create-teacher">CreateTeacher</a>
    <a href="/?route=enroll-student">EnrollStudent</a>
    <a href="/?route=assign-teacher-to-subject">AssignTeacherToSubject</a>
    <a href="/?route=delete-student">DeleteStudent (extra)</a>
    <a href="/?route=delete-course">DeleteCourse (extra)</a>
    <a href="/?route=delete-subject">DeleteSubject (extra)</a>
    <a href="/?route=delete-teacher">DeleteTeacher (extra)</a>
</nav>

<?php if ($route === 'home'): ?>
    <div class="card">
        <h3>Selecciona un caso de uso</h3>
    </div>
<?php endif; ?>

<?php if ($route === 'create-student'): ?>
    <form class="card" method="post" action="/?route=create-student">
        <h3>CreateStudent</h3>
        <input type="hidden" name="action" value="create_student">
        <label>Nombre</label><input name="name" required>
        <label>Email</label><input name="email" type="email" required>
        <button type="submit">Crear Student</button>
    </form>
<?php endif; ?>

<?php if ($route === 'create-course'): ?>
    <form class="card" method="post" action="/?route=create-course">
        <h3>CreateCourse</h3>
        <input type="hidden" name="action" value="create_course">
        <label>Nombre</label><input name="name" required>
        <button type="submit">Crear Course</button>
    </form>
<?php endif; ?>

<?php if ($route === 'create-subject'): ?>
    <form class="card" method="post" action="/?route=create-subject">
        <h3>CreateSubject</h3>
        <input type="hidden" name="action" value="create_subject">
        <label>Nombre</label><input name="name" required>
        <label>Course ID</label><input name="course_id" type="number" min="1" required>
        <button type="submit">Crear Subject</button>
    </form>
<?php endif; ?>

<?php if ($route === 'create-teacher'): ?>
    <form class="card" method="post" action="/?route=create-teacher">
        <h3>CreateTeacher</h3>
        <input type="hidden" name="action" value="create_teacher">
        <label>Nombre</label><input name="name" required>
        <label>Email</label><input name="email" type="email" required>
        <button type="submit">Crear Teacher</button>
    </form>
<?php endif; ?>

<?php if ($route === 'enroll-student'): ?>
    <form class="card" method="post" action="/?route=enroll-student">
        <h3>EnrollStudent</h3>
        <input type="hidden" name="action" value="enroll_student">
        <label>Student ID</label><input name="student_id" type="number" min="1" required>
        <label>Course ID</label><input name="course_id" type="number" min="1" required>
        <button type="submit">Matricular</button>
    </form>
<?php endif; ?>

<?php if ($route === 'assign-teacher-to-subject'): ?>
    <form class="card" method="post" action="/?route=assign-teacher-to-subject">
        <h3>AssignTeacherToSubject</h3>
        <input type="hidden" name="action" value="assign_teacher">
        <label>Teacher ID</label><input name="teacher_id" type="number" min="1" required>
        <label>Subject ID</label><input name="subject_id" type="number" min="1" required>
        <button type="submit">Asignar</button>
    </form>
<?php endif; ?>

<?php if ($route === 'delete-student'): ?>
    <form class="card" method="post" action="/?route=delete-student">
        <h3>DeleteStudent (extra)</h3>
        <input type="hidden" name="action" value="delete_student">
        <label>Student ID</label><input name="student_id" type="number" min="1" required>
        <button type="submit">Eliminar Student</button>
    </form>
<?php endif; ?>

<?php if ($route === 'delete-course'): ?>
    <form class="card" method="post" action="/?route=delete-course">
        <h3>DeleteCourse (extra)</h3>
        <input type="hidden" name="action" value="delete_course">
        <label>Course ID</label><input name="course_id" type="number" min="1" required>
        <button type="submit">Eliminar Course</button>
    </form>
<?php endif; ?>

<?php if ($route === 'delete-subject'): ?>
    <form class="card" method="post" action="/?route=delete-subject">
        <h3>DeleteSubject (extra)</h3>
        <input type="hidden" name="action" value="delete_subject">
        <label>Subject ID</label><input name="subject_id" type="number" min="1" required>
        <button type="submit">Eliminar Subject</button>
    </form>
<?php endif; ?>

<?php if ($route === 'delete-teacher'): ?>
    <form class="card" method="post" action="/?route=delete-teacher">
        <h3>DeleteTeacher (extra)</h3>
        <input type="hidden" name="action" value="delete_teacher">
        <label>Teacher ID</label><input name="teacher_id" type="number" min="1" required>
        <button type="submit">Eliminar Teacher</button>
    </form>
<?php endif; ?>

</body>
</html>
