<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Politaste Hub</title>

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

        .navbar-nav .nav-link {
            color: white !important;
            font-size: 0.85rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .nav-icon {
            /* Asegura que los iconos SVG tengan el tamaño y color correctos */
            width: 24px;
            height: 24px;
            margin-bottom: 2px;
            fill: white; /* Color del icono */
        }

        @media (max-width: 600px) {
            .navbar-nav .nav-link span {
                display: none;
            }
        }

        footer {
            background-color: #ff6f00;
            color: white;
            text-align: center;
            padding: 1rem 0;
            font-weight: 600;
            box-shadow: 0 -2px 5px rgba(255,111,0,0.4);
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container-fluid">

        <a class="navbar-brand d-flex align-items-center gap-2" href="#">
            <img src="imagenes/logouni.png" alt="Logo Universidad">
            <span class="fw-bold text-white">Politaste Hub</span>
        </a>

        <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#menuNav">
            <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuNav">

            <span class="mx-auto text-white fw-bold d-none d-lg-block" style="font-size: 1.3rem;">
                Elige, ordena, disfruta. Así de fácil
            </span>

            <ul class="navbar-nav ms-auto d-flex align-items-center">

                <li class="nav-item">
                    <a href="menu.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono: Casa -->
                            <path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5m0-2.5L2 12h3v8h6v-6h2v6h6v-8h3L12 3.19Z"/>
                        </svg>
                        <span>Inicio</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="sobre_nosotros.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono: Información -->
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1 15h-2v-6h2v6Zm0-8h-2V7h2v2Z"/>
                        </svg>
                        <span>Sobre nosotros</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="aviso_de_privacidad.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono: Documento / Aviso -->
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6Zm-1 15h-2v-2h2v2Zm0-4h-2V8h2v5Z"/>
                        </svg>
                        <span>Aviso de Privacidad</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="carrito.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono REPRESENTATIVO: Carrito de Compras (Shopping Cart) -->
                            <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2Zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2Zm-1.84-9.35c-.09-.38-.34-.73-.7-.95-.36-.22-.79-.31-1.22-.27H6.17l-.76-3.23c-.1-.4-.49-.68-.9-.68H3c-.55 0-1 .45-1 1s.45 1 1 1h1.33l2.76 11.49c.1.4.49.68.9.68h11.34c.55 0 1-.45 1-1s-.45-1-1-1H7.81l-.22-.73h10.45c.42 0 .79-.27.94-.65l2.67-6.23c.22-.51-.01-1.09-.52-1.31-.1-.05-.2-.07-.31-.08l-5.61-.43Z"/>
                        </svg>
                        <span>Carrito</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="pedidos.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono REPRESENTATIVO: Lista / Historial de Pedidos (Receipt) -->
                            <path d="M15 15H9V9h6v6Zm-2-4h-2v2h2V11Zm8-7v14c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V7l4-4h9c1.1 0 2 .9 2 2v2h2V6c0-1.1-.9-2-2-2Zm-2 4h-2V6h2v2Z"/>
                        </svg>
                        <span>Pedidos</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="profile.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono: Perfil de Usuario -->
                            <path d="M12 12c2.7 0 4.9-2.2 4.9-4.9S14.7 2.2 12 2.2 7.1 4.4 7.1 7.1 9.3 12 12 12zm0 2.2c-3.3 0-9.8 1.7-9.8 5v2.6h19.6V19.2c0-3.3-6.5-5-9.8-5z"/>
                        </svg>
                        <span>Perfil</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#resenaModal">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <!-- Icono: Estrella / Reseñas -->
                            <path d="M12 17.27L18.18 21 16.54 13.97 22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                        </svg>
                        <span>Reseñas</span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</nav>


<script src="mobileImprovements.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
 
<div class="modal fade" id="resenaModal" tabindex="-1" aria-labelledby="resenaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resenaModalLabel">Deja tu reseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="guardar_resena.php" method="post">
                <div class="modal-body">
                    <p>Tu opinión nos ayuda a mejorar. Revisa lo que otros han dicho y, si quieres, deja tu reseña abajo.</p>

                    <div id="resenasListWrapper" class="mb-3">
                        <div id="resenasList" class="text-center text-muted">Cargando reseñas...</div>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">Calificación</label>
                        <select name="calificacion" class="form-select" required>
                            <option value="">Selecciona...</option>
                            <option value="5">5 — Excelente</option>
                            <option value="4">4 — Muy buena</option>
                            <option value="3">3 — Buena</option>
                            <option value="2">2 — Regular</option>
                            <option value="1">1 — Mala</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="4" maxlength="1000" placeholder="Escribe tu reseña..." required></textarea>
                    </div>

                    <div class="form-text">Debes iniciar sesión como usuario para dejar una reseña.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn" style="background:#FF8F00;color:#fff;border-color:#FF8F00">Enviar reseña</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var resenaModal = document.getElementById('resenaModal');
    if (!resenaModal) return;

    resenaModal.addEventListener('show.bs.modal', function (event) {
        var list = document.getElementById('resenasList');
        if (!list) return;
        list.innerHTML = 'Cargando reseñas...';

        // Use absolute path to avoid 404s from different include locations
        var resenaUrl = '/Proyecto_DAW/mostrar_resena.php';
        console.log('Fetching reseñas from', resenaUrl);
        fetch(resenaUrl, { method: 'GET', credentials: 'same-origin' })
            .then(function(resp){ if (!resp.ok) throw new Error('HTTP ' + resp.status); return resp.text(); })
            .then(function(html){ list.innerHTML = html; })
            .catch(function(err){ console.error('Error cargando reseñas:', err); list.innerHTML = '<div class="text-muted">Error cargando reseñas.</div>'; });
    });
});
</script>