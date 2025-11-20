<?php
require_once 'conexion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Politaste Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff7f0;
            color: #4a2c0f;
        }
        .navbar-custom {
            background-color: #ff6f00 !important;
        }
        .btn-login {
            background-color: white;
            color: #ff6f00;
            font-weight: bold;
            margin-left: 10px;
        }
        .btn-login:hover {
            background-color: #ffe0b2;
            color: #ff6f00;
        }
        .section-title {
            color: #ff6f00;
            font-weight: bold;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
        }
        .card-menu {
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1.5rem;
        }
        .card-menu img {
            height: 200px;
            object-fit: cover;
        }
        footer {
            background-color: #ff6f00;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold" href="#">Politaste Hub</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="#menu" style="display:flex; flex-direction:column; align-items:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="margin-bottom:2px;">
                            <path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5m0-2.5L2 12h3v8h6v-6h2v6h6v-8h3L12 3.19Z"/>
                        </svg>
                        <span style="font-size:0.75rem;">Menú</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="sobre_nosotros.php" style="display:flex; flex-direction:column; align-items:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="margin-bottom:2px;">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1 15h-2v-6h2v6Zm0-8h-2V7h2v2Z"/>
                        </svg>
                        <span style="font-size:0.75rem;">Sobre nosotros</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="aviso_de_privacidad.php" style="display:flex; flex-direction:column; align-items:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="margin-bottom:2px;">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6Zm-1 15h-2v-2h2v2Zm0-4h-2V8h2v5Z"/>
                        </svg>
                        <span style="font-size:0.75rem;">Aviso de Privacidad</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="#resenas" style="display:flex; flex-direction:column; align-items:center;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="white" style="margin-bottom:2px;">
                            <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                        <span style="font-size:0.75rem;">Reseñas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="login.php" class="btn btn-login btn-sm" style="margin-left:10px;">Iniciar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <h2 id="menu" class="section-title">Menú</h2>
    <div class="row" id="menuContent">
        <div class="col-12 text-center text-muted">Cargando...</div>
    </div>

    <h2 id="resenas" class="section-title">Reseñas</h2>
    <div id="resenaContent">
        <div class="text-center text-muted">Cargando...</div>
    </div>
</div>

<footer>© 2024 Politaste Hub - Todos los derechos reservados</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Cargar menú
    fetch('menu_publico.php')
        .then(r => r.text())
        .then(html => document.getElementById('menuContent').innerHTML = html);

    // Cargar reseñas
    fetch('resenas_publico.php')
        .then(r => r.text())
        .then(html => document.getElementById('resenaContent').innerHTML = html);
</script>

</body>
</html>
