<?php
include ("contacto_automatico_geobolivia.php");
include ("config.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Pruebsa para LDAP</title>
		<meta content="text/html; charset=ISO-8859-15" lang="es" />
		<style>
			ul {
				list-style: none;
			}
			label {
				width: 120px;
				float: left;
			}

		</style>
	</head>
	<body>
		<form method="post">
			<ul>
				<li>
					<label>usuario</label>
					<input type=text name="usuario">
				</li>
				<li>
					<label>email</label>
					<input type=text name="email">
				</li>
				<li>
					<label>Password</label>
					<input type=text name="pass">
				</li>
				<li>
					<label>Telefono</label>
					<input type=text name="email">
				</li>
				<li>
					<input type="submit" value="Verificar"</li>

					<?php
					if ($_POST['usuario']) {
						$v = verifica($ldapconfig, $_POST);
						if ($v == 1)
							echo $_POST['usuario'] . " ya existe";
						else {
							if ($v == 2)
								echo $_POST['email'] . " ya existe";
							else {
								if ($v == 3)
									echo $_POST['usuario'] . " y el email " . $_POST['email'] . " ya existen";
								else
									echo $_POST['usuario'] . " y el email " . $_POST['email'] . " NO existen";
							}
						}

					} else
						echo "
					<br>
					No hay Valor enviado por POST";
					?>
			</ul>
		</form>
	</body>
</html>
