<?php $__env->startSection('content'); ?>
<div>
  <h1>SchoolOOP / Izan Aranda Valverde </h1>
  <nav>
    <button id="nav-teachers">Teachers</button>
    <button id="nav-students">Students</button>
    <button id="nav-subjects">Subjects</button>
    <button id="nav-courses">Courses</button>
  </nav>
  <div id="view"></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>