<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>CRUD DE USUARIOS</title>
<link href="web/css/default.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="container" style="width: 600px;">
<div id="header">
<h1><h1>GESTIÓN DE USUARIOS ACCESO</h1></h1>
</div>
<div id="content">
<hr>
<?= $msg ?><br>
<form method="GET">
INTRODUZCA LOGIN 
<input name="login" type="text"><br>
INTRODUZCA CONTRASEÑA
<input name="password" type="password"><br>
<input type="submit" value="Enviar">
</form>
</div>
</div>
</body>
</html>