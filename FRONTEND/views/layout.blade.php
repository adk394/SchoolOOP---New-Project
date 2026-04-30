<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>School SPA</title>
  <link rel="stylesheet" href="/css/style.css">
  <style>form div{margin-bottom:6px}</style>
</head>
<body>
  <div id="app">
    @yield('content')
  </div>
  <script>
    window.API_BASE = "{{ $apiBase }}";
  </script>
  <script src="/js/app.js"></script>
</body>
</html>
