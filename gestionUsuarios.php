<?php
session_start();
include 'conexion.php';

// Manejo de formularios de POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Acción: cambiar contraseña como admin
	if (isset($_POST['action']) && $_POST['action'] === 'cambiar_contrasena_admin') {
		$target = mysqli_real_escape_string($conn, $_POST['target_matricula']);
		$nueva = isset($_POST['nueva_contrasena']) ? $_POST['nueva_contrasena'] : '';
		$confirm = isset($_POST['confirm_contrasena']) ? $_POST['confirm_contrasena'] : '';
		$admin_pass = isset($_POST['admin_password']) ? $_POST['admin_password'] : '';

		if (empty($nueva) || strlen($nueva) < 6) {
			$mensaje_error = 'La nueva contraseña debe tener al menos 6 caracteres.';
		} elseif ($nueva !== $confirm) {
			$mensaje_error = 'Las contraseñas nuevas no coinciden.';
		} else {
			// Determinar matrícula del administrador en sesión
			$admin_matricula = null;
			if (!empty($_SESSION['admin_id'])) $admin_matricula = $_SESSION['admin_id'];
			elseif (!empty($_SESSION['matricula'])) $admin_matricula = $_SESSION['matricula'];
			elseif (!empty($_SESSION['usuario'])) $admin_matricula = $_SESSION['usuario'];

			if (empty($admin_matricula)) {
				$mensaje_error = 'No se pudo verificar la sesión de administrador. Inicia sesión nuevamente.';
			} else {
				// Usar helper para detectar la columna de contraseña
				$password_col = function_exists('getPasswordColumn') ? getPasswordColumn($conn, 'usuarios') : null;

				if (is_null($password_col)) {
					$mensaje_error = 'No se pudo determinar la columna de contraseña en la base de datos.';
				} else {
					$adminIdentifier = mysqli_real_escape_string($conn, $admin_matricula);
					$admin_found = false;

					// Intentar verificar al administrador en la tabla administradores
					$password_col_admin = function_exists('getPasswordColumn') ? getPasswordColumn($conn, 'administradores') : null;
					if (!is_null($password_col_admin)) {
						if (ctype_digit($adminIdentifier)) {
							$q = "SELECT `" . $password_col_admin . "` AS current_pass FROM administradores WHERE id_admin = '" . $adminIdentifier . "' LIMIT 1";
						} else {
							$q = "SELECT `" . $password_col_admin . "` AS current_pass FROM administradores WHERE usuario = '" . $adminIdentifier . "' LIMIT 1";
						}
						$r = mysqli_query($conn, $q);
						if ($r && $row = mysqli_fetch_assoc($r)) { $admin_found = true; }
					}

					// Si no se encontró en administradores, fallback a usuarios
					if (!$admin_found) {
						$q = "SELECT `" . $password_col . "` AS current_pass FROM usuarios WHERE matricula = '" . mysqli_real_escape_string($conn, $admin_matricula) . "' LIMIT 1";
						$r = mysqli_query($conn, $q);
						if ($r && $row = mysqli_fetch_assoc($r)) { $admin_found = true; }
					}

					if ($admin_found) {
						$hash_admin = $row['current_pass'];
						$admin_valid = ($admin_pass === $hash_admin);

						if (!$admin_valid) {
							$mensaje_error = 'Contraseña de administrador incorrecta.';
						} else {
							$upd_col = $password_col;
							$upd = "UPDATE usuarios SET `" . $upd_col . "` = '" . mysqli_real_escape_string($conn, $nueva) . "' WHERE matricula = '" . mysqli_real_escape_string($conn, $target) . "'";
							if (mysqli_query($conn, $upd)) {
								$mensaje_exito = 'Contraseña actualizada correctamente para la matrícula ' . htmlspecialchars($target) . '.';
							} else {
								$mensaje_error = 'Error al actualizar la contraseña.';
							}
						}
					} else {
						$mensaje_error = 'No se encontró la cuenta del administrador para verificar.';
					}
				}
			}
		}
	}
	
	// Acción: dar de baja usuario
	if (isset($_POST['action']) && $_POST['action'] === 'dar_baja_usuario') {
		$target = mysqli_real_escape_string($conn, $_POST['target_matricula_baja']);
		$admin_pass = isset($_POST['admin_password_baja']) ? $_POST['admin_password_baja'] : '';

		// Verificación admin igual que en cambio de contraseña
		$admin_matricula = null;
		if (!empty($_SESSION['admin_id'])) $admin_matricula = $_SESSION['admin_id'];
		elseif (!empty($_SESSION['matricula'])) $admin_matricula = $_SESSION['matricula'];
		elseif (!empty($_SESSION['usuario'])) $admin_matricula = $_SESSION['usuario'];

		$password_col = function_exists('getPasswordColumn') ? getPasswordColumn($conn, 'usuarios') : null;
		$adminIdentifier = mysqli_real_escape_string($conn, $admin_matricula);
		$admin_found = false;
		$password_col_admin = function_exists('getPasswordColumn') ? getPasswordColumn($conn, 'administradores') : null;
		if (!is_null($password_col_admin)) {
			if (ctype_digit($adminIdentifier)) {
				$q = "SELECT `" . $password_col_admin . "` AS current_pass FROM administradores WHERE id_admin = '" . $adminIdentifier . "' LIMIT 1";
			} else {
				$q = "SELECT `" . $password_col_admin . "` AS current_pass FROM administradores WHERE usuario = '" . $adminIdentifier . "' LIMIT 1";
			}
			$r = mysqli_query($conn, $q);
			if ($r && $row = mysqli_fetch_assoc($r)) { $admin_found = true; }
		}
		if (!$admin_found) {
			$q = "SELECT `" . $password_col . "` AS current_pass FROM usuarios WHERE matricula = '" . mysqli_real_escape_string($conn, $admin_matricula) . "' LIMIT 1";
			$r = mysqli_query($conn, $q);
			if ($r && $row = mysqli_fetch_assoc($r)) { $admin_found = true; }
		}
		if ($admin_found) {
			$hash_admin = $row['current_pass'];
			$admin_valid = ($admin_pass === $hash_admin);
			if (!$admin_valid) {
				$mensaje_error = 'Contraseña de administrador incorrecta.';
			} else {
				// Eliminar usuario
				$del = "DELETE FROM usuarios WHERE matricula = '" . $target . "'";
				if (mysqli_query($conn, $del)) {
					$mensaje_exito = 'Usuario dado de baja correctamente (matrícula ' . htmlspecialchars($target) . ').';
				} else {
					$mensaje_error = 'Error al dar de baja al usuario.';
				}
			}
		} else {
			$mensaje_error = 'No se encontró la cuenta del administrador para verificar.';
		}
	}

	// Acción: actualizar datos de usuario (correo / bloqueado)
	if (isset($_POST['matricula']) && (!isset($_POST['action']) || $_POST['action'] !== 'cambiar_contrasena_admin')) {
		$matricula = mysqli_real_escape_string($conn, $_POST['matricula']);
		$nuevoEstado = isset($_POST['bloqueado']) ? intval($_POST['bloqueado']) : 0;
		$nuevoCorreo = isset($_POST['correo']) ? mysqli_real_escape_string($conn, $_POST['correo']) : '';

		$consultaActual = "SELECT bloqueado, correo FROM usuarios WHERE matricula = '" . mysqli_real_escape_string($conn, $matricula) . "'";
		$resultadoActual = mysqli_query($conn, $consultaActual);
		$datosActuales = mysqli_fetch_assoc($resultadoActual);
		$estadoActual = intval($datosActuales['bloqueado']);
		$correoActual = $datosActuales['correo'];

		if (!filter_var($nuevoCorreo, FILTER_VALIDATE_EMAIL)) {
			$mensaje_error = "El formato del correo electrónico no es válido.";
		} else {
			if ($nuevoCorreo !== $correoActual) {
				$consultaCorreo = "SELECT matricula FROM usuarios WHERE correo = '" . mysqli_real_escape_string($conn, $nuevoCorreo) . "' AND matricula != '" . mysqli_real_escape_string($conn, $matricula) . "'";
				$resultadoCorreo = mysqli_query($conn, $consultaCorreo);
				if (mysqli_num_rows($resultadoCorreo) > 0) {
					$mensaje_error = "El correo electrónico ya está registrado por otro usuario.";
				}
			}

			if (!isset($mensaje_error)) {
				$update = "UPDATE usuarios SET bloqueado = " . intval($nuevoEstado) . ", correo = '" . mysqli_real_escape_string($conn, $nuevoCorreo) . "' WHERE matricula = '" . mysqli_real_escape_string($conn, $matricula) . "'";
				if (mysqli_query($conn, $update)) {
					$cambios = [];
					if ($nuevoCorreo !== $correoActual) $cambios[] = "correo electrónico";
					if ($estadoActual !== $nuevoEstado) $cambios[] = "estado de bloqueo";
					if (!empty($cambios)) {
						$mensaje_exito = "Usuario actualizado correctamente (" . implode(", ", $cambios) . ").";
					} else {
						$mensaje_info = "No se realizaron cambios en los datos del usuario.";
					}
				} else {
					$mensaje_error = "Error al actualizar los datos del usuario.";
				}
			}
		}
	}
}

$consulta = "SELECT matricula, nombre, correo, bloqueado, fecha_registro FROM usuarios";
$resultado = mysqli_query($conn, $consulta);
?>
<?php include 'encabezadoAdmin.php'; ?>
	<style>
	/* Aumentar grosor de texto para mejorar visibilidad */
	h1 {
		font-weight: 700;
		text-align: center;
	}

	th {
		font-weight: 700;
	}

	td {
		font-weight: 600;
	}

@media screen and (max-width: 768px) {
	table, thead, tbody, td, tr {
		display: block;
	}
	th {
		display: none !important;
	}
	thead tr {
		display: none !important;
	}
	table tr:first-child {
		display: none !important;
	}
	tr {
		background: white;
		border: 1px solid #ccc;
		border-radius: 8px;
		padding: 15px;
		margin-bottom: 15px;
		box-shadow: 0 2px 5px rgba(0,0,0,0.1);
	}
	form {
		width: 100%;
		display: block;
	}
	td {
		border: none;
		border-bottom: 1px solid #eee;
		position: relative;
		padding: 10px 0 10px 120px !important;
		text-align: left;
		min-width: auto !important;
		font-size: 14px !important;
	}
	td:nth-of-type(1):before { content: "Matrícula: "; }
	td:nth-of-type(2):before { content: "Nombre: "; }
	td:nth-of-type(3):before { content: "Correo: "; }
	td:nth-of-type(4):before { content: "Bloqueado: "; }
	td:nth-of-type(5):before { content: "Fecha registro: "; }
	td:nth-of-type(6):before { content: "Actualizar: "; }
	td:nth-of-type(7):before { content: "Contraseña: "; }
	td:before {
		position: absolute;
		left: 10px;
		width: 100px;
		padding-right: 10px;
		white-space: nowrap;
		font-weight: bold;
		color: #e67e22;
		font-size: 13px;
	}
	td:last-child {
		border-bottom: none;
	}
	input[type="email"] {
		width: 100% !important;
		max-width: 200px !important;
		padding: 6px !important;
		font-size: 13px !important;
		min-width: auto !important;
	}
	select {
		padding: 6px !important;
		font-size: 13px !important;
		width: auto !important;
	}
	button {
		padding: 8px 12px !important;
		font-size: 13px !important;
		margin: 5px 0 !important;
		width: auto !important;
	}
	h1 {
		font-size: 1.5rem !important;
		margin: 1rem 0 !important;
		text-align: center;
	}
	.table-container {
		overflow-x: visible !important;
		-webkit-overflow-scrolling: auto !important;
	}
	div[style*="background-color: #d4edda"],
	div[style*="background-color: #fff3cd"],
	div[style*="background-color: #f8d7da"],
	div[style*="background-color: #d1ecf1"] {
		width: 95% !important;
		margin: 10px auto !important;
		padding: 12px !important;
		font-size: 14px !important;
		text-align: center !important;
	}
}

@media screen and (max-width: 480px) {
	tr {
		padding: 12px;
		margin-bottom: 12px;
	}
	td {
		padding: 8px 0 8px 110px !important;
		font-size: 13px !important;
	}
	td:before {
		width: 90px;
		font-size: 12px;
		left: 8px;
	}
	input[type="email"] {
		max-width: 180px !important;
		padding: 5px !important;
		font-size: 12px !important;
	}
	button {
		padding: 6px 10px !important;
		font-size: 12px !important;
	}
	h1 {
		font-size: 1.3rem !important;
	}
	body > button {
		padding: 8px 16px !important;
		font-size: 13px !important;
		margin-bottom: 15px !important;
	}
}

	</style>
</head>
<body>
	<h1>Gestión de Usuarios</h1>
  
	<?php
	if (isset($mensaje_exito)): ?>
		<div style="background-color: #d4edda; color: #155724; padding: 10px; max-width:1100px; width:95%; margin: 10px auto; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
			<?php echo $mensaje_exito; ?>
		</div>
	<?php endif; ?>
  
	<?php if (isset($mensaje_advertencia)): ?>
		<div style="background-color: #fff3cd; color: #856404; padding: 10px; max-width:1100px; width:95%; margin: 10px auto; border: 1px solid #ffeaa7; border-radius: 5px; text-align: center;">
			<?php echo $mensaje_advertencia; ?>
		</div>
	<?php endif; ?>
  
	<?php if (isset($mensaje_error)): ?>
		<div style="background-color: #f8d7da; color: #721c24; padding: 10px; max-width:1100px; width:95%; margin: 10px auto; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;">
			<?php echo $mensaje_error; ?>
		</div>
	<?php endif; ?>
  
	<?php if (isset($mensaje_info)): ?>
		<div style="background-color: #d1ecf1; color: #0c5460; padding: 10px; max-width:1100px; width:95%; margin: 10px auto; border: 1px solid #b8daff; border-radius: 5px; text-align: center;">
			<?php echo $mensaje_info; ?>
		</div>
	<?php endif; ?>
  


	<div class="table-container" style="margin: 0 auto; max-width: 1100px; background: #f9f9f9; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.07); padding: 24px;">
		<table style="width:100%; border-collapse:collapse; background:#fff; border-radius:8px; overflow:hidden;">
			<tr style="background:#ff6f00; color:#fff;">
				<th style="padding:12px; font-weight:700;">Matrícula</th>
				<th style="padding:12px; font-weight:700;">Nombre</th>
				<th style="padding:12px; font-weight:700;">Correo</th>
				<th style="padding:12px; font-weight:700;">Bloqueado</th>
				<th style="padding:12px; font-weight:700;">Fecha de registro</th>
				<th style="padding:12px; font-weight:700;">Modificar</th>
				<th style="padding:12px; font-weight:700;">Acciones</th>
			</tr>
			<?php while ($user = mysqli_fetch_assoc($resultado)) { ?>
			<tr style="border-bottom:1px solid #eee;">
				<form method="POST">
					<td style="font-weight:600; color:#e67e22; padding:10px;"><?php echo htmlspecialchars($user['matricula']); ?></td>
					<td style="font-weight:600; color:#333; padding:10px;"><?php echo htmlspecialchars($user['nombre']); ?></td>
					<td style="padding:10px;">
						<input type="email" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" style="width:100%; padding:6px; border:1px solid #ccc; border-radius:4px; font-size:14px;" required>
					</td>
					<td style="padding:10px;">
						<select name="bloqueado" style="padding:6px; border:1px solid #e67e22; border-radius:4px; font-size:14px;">
							<option value="0" <?= $user['bloqueado'] == 0 ? 'selected' : '' ?>>No</option>
							<option value="1" <?= $user['bloqueado'] == 1 ? 'selected' : '' ?>>Sí</option>
						</select>
					</td>
					<td style="color:#888; font-size:13px; padding:10px;"><?php echo htmlspecialchars($user['fecha_registro']); ?></td>
					<td style="padding:10px;">
						<input type="hidden" name="matricula" value="<?= $user['matricula'] ?>">
						<button type="submit" style="background:#27ae60; color:#fff; border:none; border-radius:6px; padding:8px 18px; font-size:15px; font-weight:600; box-shadow:0 1px 4px rgba(39,174,96,0.08); transition:background 0.2s; cursor:pointer;">Actualizar</button>
					</td>
				</form>
				<td style="padding:10px; background:#fff; border-radius:8px; text-align:center; vertical-align:middle;">
					<div style="display:flex; gap:12px; justify-content:center; align-items:center;">
						<button type="button" style="background:#2980b9; color:#fff; border:none; border-radius:6px; padding:8px 18px; font-size:15px; font-weight:600; box-shadow:0 1px 4px rgba(41,128,185,0.08); transition:background 0.2s;" data-bs-toggle="modal" data-bs-target="#modalChange<?= $user['matricula'] ?>">
							Cambiar Contraseña
						</button>
						<button type="button" style="background:#c0392b; color:#fff; border:none; border-radius:6px; padding:8px 18px; font-size:15px; font-weight:600; box-shadow:0 1px 4px rgba(192,57,43,0.08); transition:background 0.2s;" data-bs-toggle="modal" data-bs-target="#modalBaja<?= $user['matricula'] ?>">
							Dar de baja
						</button>
					</div>

					<!-- Modal cambio de contraseña -->
					<div class="modal fade" id="modalChange<?= $user['matricula'] ?>" tabindex="-1" aria-labelledby="modalChangeLabel<?= $user['matricula'] ?>" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content" style="border-radius:10px;">
								<form method="post" action="gestionUsuarios.php">
									<div class="modal-header" style="background:#2980b9; color:#fff; border-radius:10px 10px 0 0;">
										<h5 class="modal-title" id="modalChangeLabel<?= $user['matricula'] ?>">Cambiar contraseña - <?= htmlspecialchars($user['nombre']) ?></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
									</div>
									<div class="modal-body">
										<div class="mb-3">
											<label class="form-label">Nueva contraseña</label>
											<input type="password" name="nueva_contrasena" class="form-control" required minlength="6">
										</div>
										<div class="mb-3">
											<label class="form-label">Confirmar nueva contraseña</label>
											<input type="password" name="confirm_contrasena" class="form-control" required minlength="6">
										</div>
										<div class="mb-3">
											<label class="form-label">Tu contraseña de administrador</label>
											<input type="password" name="admin_password" class="form-control" required>
										</div>
										<input type="hidden" name="target_matricula" value="<?= htmlspecialchars($user['matricula']) ?>">
										<input type="hidden" name="action" value="cambiar_contrasena_admin">
									</div>
									<div class="modal-footer" style="border-radius:0 0 10px 10px;">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
										<button type="submit" class="btn btn-primary">Confirmar cambio</button>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Modal Dar de baja -->
					<div class="modal fade" id="modalBaja<?= $user['matricula'] ?>" tabindex="-1" aria-labelledby="modalBajaLabel<?= $user['matricula'] ?>" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content" style="border-radius:10px;">
								<form method="post" action="gestionUsuarios.php">
									<div class="modal-header" style="background:#c0392b; color:#fff; border-radius:10px 10px 0 0;">
										<h5 class="modal-title" id="modalBajaLabel<?= $user['matricula'] ?>">Dar de baja usuario - <?= htmlspecialchars($user['nombre']) ?></h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
									</div>
									<div class="modal-body">
										<p>¿Seguro que deseas dar de baja al usuario <strong><?= htmlspecialchars($user['nombre']) ?></strong> (Matrícula: <?= htmlspecialchars($user['matricula']) ?>)? Esta acción no se puede deshacer.</p>
										<div class="mb-3">
											<label class="form-label">Tu contraseña de administrador</label>
											<input type="password" name="admin_password_baja" class="form-control" required>
										</div>
										<input type="hidden" name="target_matricula_baja" value="<?= htmlspecialchars($user['matricula']) ?>">
										<input type="hidden" name="action" value="dar_baja_usuario">
									</div>
									<div class="modal-footer" style="border-radius:0 0 10px 10px;">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
										<button type="submit" class="btn btn-danger">Confirmar baja</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				</td>
			</tr>
			<?php } ?>
		</table>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php include 'pie.html'; ?>

