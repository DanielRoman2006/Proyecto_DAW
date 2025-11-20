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
            width: 24px;
            height: 24px;
            margin-bottom: 2px;
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
                            <path d="M12 5.69l5 4.5V18h-2v-6H9v6H7v-7.81l5-4.5m0-2.5L2 12h3v8h6v-6h2v6h6v-8h3L12 3.19Z" fill="white"/>
                        </svg>
                        <span>Inicio</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="sobre_nosotros.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm1 15h-2v-6h2v6Zm0-8h-2V7h2v2Z" fill="white"/>
                        </svg>
                        <span>Sobre nosotros</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="aviso_de_privacidad.php" class="nav-link">
                        <svg class="nav-icon" viewBox="0 0 24 24">
                            <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6Zm-1 15h-2v-2h2v2Zm0-4h-2V8h2v5Z" fill="white"/>
                        </svg>
                        <span>Aviso de Privacidad</span>
                    </a>
                </li>

                  <li class="nav-item ms-3">
        <a href="login.php" class="btn btn-outline-light btn-sm" 
           style="transition: 0.3s; color: white; border-color: white;"
           onmouseover="this.style.backgroundColor='#fff'; this.style.color='#ff6f00';"
           onmouseout="this.style.backgroundColor='transparent'; this.style.color='white';">
            Iniciar sesión
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

        fetch('mostrar_resenas.php', { method: 'GET', credentials: 'same-origin' })
            .then(function(resp){ return resp.text(); })
            .then(function(html){ list.innerHTML = html; })
            .catch(function(){ list.innerHTML = '<div class="text-muted">Error cargando reseñas.</div>'; });
    });
});
</script>
