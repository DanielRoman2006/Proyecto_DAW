<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Aceptar tanto la clave 'rol' (es) como 'role' (en) para compatibilidad.
$userRole = $_SESSION['rol'] ?? $_SESSION['role'] ?? null;
if ($userRole !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Politaste Hub - Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="responsive.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff7f0;
            color: #4a2c0f;
            margin: 0;
            padding: 0;
        }

        .navbar-custom {
            background-color: #ff6f00 !important;
            box-shadow: 0 2px 5px rgba(255,111,0,0.4);
        }

        .navbar-brand img {
            height: 40px;
            border-radius: 5px;
            background-color: white;
            padding: 2px;
        }

        /* --- ESTILOS DE ESCRITORIO (Por defecto) --- */
        .navbar-nav .nav-link {
            color: white !important;
            font-size: 0.85rem;
            display: flex;
            flex-direction: column; /* Icono arriba, texto abajo */
            align-items: center;
            transition: all 0.2s ease;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        
        .navbar-nav .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            transform: translateY(-2px);
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            margin-bottom: 4px; /* Espacio entre icono y texto en escritorio */
            transition: transform 0.2s;
        }

        footer {
            background-color: #ff6f00;
            color: white;
            text-align: center;
            padding: 1rem 0;
            font-weight: 600;
            box-shadow: 0 -2px 5px rgba(255,111,0,0.4);
        }

        /* --- ESTILOS PARA MÓVIL (Cuando el menú se colapsa) --- */
        /* Bootstrap 'lg' colapsa en 991px, así que usamos ese punto de quiebre */
        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #e65100; /* Un tono ligeramente más oscuro para el menú desplegado */
                margin-top: 10px;
                border-radius: 0 0 10px 10px; /* Bordes redondeados abajo */
                padding: 10px 0;
                box-shadow: inset 0 5px 10px rgba(0,0,0,0.1);
            }

            .navbar-nav .nav-link {
                flex-direction: row; /* Pone el icono AL LADO del texto */
                align-items: center;
                justify-content: flex-start; /* Alinea todo a la izquierda */
                padding: 12px 25px; /* Más espacio para el dedo */
                font-size: 1rem; /* Letra un poco más grande en móvil */
                border-radius: 0; /* Quita bordes redondeados individuales */
                border-left: 4px solid transparent; /* Preparación para hover */
                width: 100%;
            }

            .navbar-nav .nav-link:hover {
                background-color: rgba(255, 255, 255, 0.1);
                transform: none; /* Quita el efecto de salto */
                border-left: 4px solid white; /* Indicador visual a la izquierda */
            }

            .nav-icon {
                margin-bottom: 0; /* Quita margen inferior */
                margin-right: 15px; /* Añade margen derecho para separar del texto */
                width: 22px;
                height: 22px;
            }
            
            /* Opcional: Líneas divisorias sutiles entre opciones */
            .nav-item {
                border-bottom: 1px solid rgba(255,255,255,0.05);
            }
            .nav-item:last-child {
                border-bottom: none;
            }
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center gap-2" href="paginaAdmin.php">
            <img src="imagenes/logouni.png" alt="Logo Universidad">
            <span class="fw-bold text-white">Politaste Hub</span>
        </a>

        <button class="navbar-toggler text-white border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuNav">

            <span class="mx-auto text-white fw-bold d-none d-lg-block" style="font-size: 1.3rem;">
                Panel de Administración
            </span>

            <ul class="navbar-nav ms-auto d-flex">

                <li class="nav-item">
                    <a href="paginaAdmin.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5m0-2.5L2 12h3v8h6v-6h2v6h6v-8h3L12 3.19Z" fill="white"/>
                        </svg>
                        <span>Inicio</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="gestionarProductos.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                             <path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z" fill="white"/>
                        </svg>
                        <span>Productos</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="gestionUsuarios.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill="white"/>
                        </svg>
                        <span>Usuarios</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="verResenas.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" fill="white"/>
                        </svg>
                        <span>Reseñas</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="profile.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3.3 0-9.8 1.7-9.8 5v2.6h19.6V19.2c0-3.3-6.5-5-9.8-5z" fill="white"/>
                        </svg>
                        <span>Cuenta</span>
                    </a>
                </li>

            </ul>
        </div>

    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="mobileImprovements.js"></script>
</body>
</html>