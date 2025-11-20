<?php
session_start();
include 'conexion.php';

date_default_timezone_set('America/Mexico_City');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$matricula    = mysqli_real_escape_string($conn, $_POST['matricula']);
	$nombre       = mysqli_real_escape_string($conn, $_POST['nombre']);
	$correo       = mysqli_real_escape_string($conn, $_POST['correo']);
	$contraseña   = mysqli_real_escape_string($conn, $_POST['contraseña']);
	$bloqueado    = isset($_POST['bloqueado']) ? 1 : 0;
	$fecha        = date('Y-m-d H:i:s');

	$verificar_matricula = "SELECT matricula FROM usuarios WHERE matricula = '$matricula'";
	$resultado_matricula = mysqli_query($conn, $verificar_matricula);
    
	$verificar_correo = "SELECT correo FROM usuarios WHERE correo = '$correo'";
	$resultado_correo = mysqli_query($conn, $verificar_correo);

	if (mysqli_num_rows($resultado_matricula) > 0) {
		$mensaje = "La matrícula <strong>$matricula</strong> ya está registrada.";
	} elseif (mysqli_num_rows($resultado_correo) > 0) {
		$mensaje = "El correo electrónico <strong>$correo</strong> ya está registrado con otra cuenta.";
	} else {
		$insertar = "INSERT INTO usuarios (matricula, nombre, correo, contraseña, bloqueado, fecha_registro)
								 VALUES ('$matricula', '$nombre', '$correo', '$contraseña', $bloqueado, '$fecha')";

		if (mysqli_query($conn, $insertar)) {
			$mensaje = "Usuario registrado correctamente.";
		} else {
			$mensaje = "Error al registrar: " . mysqli_error($conn);
		}
	}
}
?>
<?php include 'encabezadoadmin.php'; ?>


<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Registrar Usuario</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="estilo.css">
	<style>
		form {
			background-color: #fff3e0;
			padding: 25px;
			max-width: 400px;
			margin: 30px auto;
			border-radius: 12px;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
		}

		input[type="text"],
		input[type="email"],
		input[type="password"] {
			width: 100%;
			padding: 10px;
			margin: 10px 0;
			border: 1px solid #ddd;
			border-radius: 6px;
			background-color: #fff8f0;
			color: #333;
			font-size: 16px;
		}

		h1 {
			color: #d35400;
			margin-bottom: 20px;
			text-align: center;
			font-size: 30px;
		}

		.button-row {
			display: flex;
			gap: 10px;
			margin-top: 15px;
		}

		.btn-custom {
			background-color: #e67e22;
			color: white;
			padding: 10px 18px;
			border: none;
			border-radius: 6px;
			font-size: 16px;
			cursor: pointer;
			transition: background 0.3s ease;
		}

		.btn-custom:hover { background-color: #ca6b1e; }

		.btn-outline-custom {
			background-color: transparent;
			color: #e67e22;
			padding: 10px 18px;
			border: 2px solid #e67e22;
			border-radius: 6px;
			font-size: 16px;
			cursor: pointer;
			transition: background 0.3s ease, color 0.3s ease;
		}

		.btn-outline-custom:hover {
			background-color: #e67e22;
			color: #fff;
		}

		.mensaje {
			margin-top: 20px;
			text-align: center;
			font-size: 17px;
			font-weight: bold;
			color: #d35400;
		}
	</style>
</head>
<body>
	<div class="container py-4">
		<h1>Alta de nuevo usuario</h1>

		<form method="POST" class="needs-validation" novalidate>
			<div class="mb-3">
				<input type="text" name="matricula" class="form-control" placeholder="Matrícula" required>
			</div>
			<div class="mb-3">
				<input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
			</div>
			<div class="mb-3">
				<input type="email" name="correo" class="form-control" placeholder="Correo institucional" required>
			</div>
			<div class="mb-3">
				<input type="password" name="contraseña" class="form-control" placeholder="Contraseña" required>
			</div>

			<div class="button-row">
				<button type="submit" class="btn-custom flex-fill">Registrar</button>
				<button type="button" class="btn-outline-custom flex-fill" onclick="window.location.href='paginaAdmin.php'">Regresar</button>
			</div>
		</form>

		<?php if (isset($mensaje)) echo "<div class='mensaje'>$mensaje</div>"; ?>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		(function () {
			'use strict'
			const forms = document.querySelectorAll('.needs-validation')
			Array.from(forms).forEach(function (form) {
				form.addEventListener('submit', function (event) {
					if (!form.checkValidity()) {
						event.preventDefault()
						event.stopPropagation()
					}
					form.classList.add('was-validated')
				}, false)
			})
		})()
	</script>
</body>
</html>
<?php include 'pie.html'; ?>

 