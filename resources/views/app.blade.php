<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <title>Pengaduan Sarana</title>
    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body class="antialiased text-slate-900" style="background: radial-gradient(circle at top right, #eff6ff, #ffffff, #f0f9ff);">
    @inertia
</body>

</html>