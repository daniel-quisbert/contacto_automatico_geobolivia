<?php 
include ("contacto_automatico_geobolivia.php");
include ("config.php");
?>
<!DOCTYPE html>
<html>
<head><title>Pruebsa para LDAP</title></head>
<body>
<form method="post">
	<ul>
		<li><balel>usuario</label><input type=text name="usuario"></li>
		<li><balel>Password</label><input type=text name="pass"></li>
		<li><balel>email</label><input type=text name="email"></li>
		<li><input type=submit value="Verifica User"></li>
		<?php
		if($_POST['usuario']){
			$v=verifica($ldapconfig,$_POST);
			if($v == 1)
				echo $_POST['usuario']." ya existe";
			else{
				if($v == 2)
					echo $_POST['email']." ya existe";
				else 
					if($v == 3)
						echo $_POST['usuario']." y el email ". $_POST['email']." ya existen";	
					else
						echo $_POST['usuario']." y el email ". $_POST['email']." NO existen";	
			}
				 
				
		}	
		else
			echo"No hay Valor enviado por POST";
		?>
	</ul>
</form>
</body>
</html>
