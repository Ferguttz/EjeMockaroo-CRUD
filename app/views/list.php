
<form>
<button type="submit" name="orden" value="Nuevo"> Cliente Nuevo </button><br>
</form>
<br>
<?php $titulos = ['id','first_name','email','gender','ip_address']; ?>
<table>
<tr>
    <?php foreach ($titulos as $valor): ?>
        <th><a href="<?=$_SERVER['PHP_SELF'].'?ordenacion='.$valor?>"><?= $valor ?></a></th>
    <?php endforeach ?>
    <th><a href="<?=$_SERVER['PHP_SELF'].'?ordenacion=telefono'?>">teléfono</a></th>
</tr>
<?php foreach ($tvalores as $valor): ?>
<tr>
<td><?= $valor->id ?> </td>
<td><?= $valor->first_name ?> </td>
<td><?= $valor->email ?> </td>
<td><?= $valor->gender ?> </td>
<td><?= $valor->ip_address ?> </td>
<td><?= $valor->telefono ?> </td>
<td><a href="#" onclick="confirmarBorrar('<?=$valor->first_name?>',<?=$valor->id?>);" >Borrar</a></td>
<td><a href="?orden=Modificar&id=<?=$valor->id?>">Modificar</a></td>
<td><a href="?orden=Detalles&id=<?=$valor->id?>" >Detalles</a></td>

<tr>
<?php endforeach ?>
</table>

<form>
<br>
<button type="submit" name="nav" value="Primero"> << </button>
<button type="submit" name="nav" value="Anterior"> < </button>
<button type="submit" name="nav" value="Siguiente"> > </button>
<button type="submit" name="nav" value="Ultimo"> >> </button>
</form>
