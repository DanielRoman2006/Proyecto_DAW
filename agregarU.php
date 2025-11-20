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

		button {
			background-color: #e67e22;
			color: white;
			padding: 10px 18px;
			border: none;
			border-radius: 6px;
			font-size: 16px;
			cursor: pointer;
			margin-top: 15px;
			transition: background 0.3s ease;
		}

		button:hover {
			background-color: #ca6b1e;
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
			<input type="text" name="matricula" placeholder="Matrícula" required>
			<input type="text" name="nombre" placeholder="Nombre completo" required>
			<input type="email" name="correo" placeholder="Correo institucional" required>
			<input type="password" name="contraseña" placeholder="Contraseña" required>

			<button type="submit">Registrar</button>
			<button type="button" onclick="window.location.href='paginaAdmin.php'">Regresar</button>
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

 