
<hr>
<form  enctype="multipart/form-data"  method="POST">
<table>
 <tr><td>id:</td> 
 <td><input type="number" name="id" value="<?=$cli->id ?>"  readonly  ></td>
    <td rowspan="7">
        <input type="hidden" name="MAX_FILE_SIZE" value="500000" /> 
        <input name="imagen" type="file" accept="image/png, image/jpeg">
    </td>
</tr>
 </tr>
 <tr><td>first_name:</td> 
 <td><input type="text" name="first_name" value="<?=$cli->first_name ?>" <?= $_SESSION['autofocus'] == "first_name" ? "autofocus" : "" ?>  ></td></tr>
 </tr>
 <tr><td>last_name:</td> 
 <td><input type="text" name="last_name" value="<?=$cli->last_name ?>"  ></td></tr>
 </tr>
 <tr><td>email:</td> 
 <td><input type="email" name="email" value="<?=$cli->email ?>" <?= $_SESSION['autofocus'] == "email" ? "autofocus" : "" ?> ></td></tr>
 </tr>
 <tr><td>gender</td> 
 <td><input type="text" name="gender" value="<?=$cli->gender ?>"  ></td></tr>
 </tr>
 <tr><td>ip_address:</td> 
 <td><input type="text" name="ip_address" value="<?=$cli->ip_address ?>" <?= $_SESSION['autofocus'] == "ip_address" ? "autofocus" : "" ?> ></td></tr>
 </tr>
 <tr><td>telefono:</td> 
 <td><input type="tel" name="telefono" value="<?=$cli->telefono ?>" <?= $_SESSION['autofocus'] == "telefono" ? "autofocus" : "" ?> ></td></tr>
 </tr>
 </table>
 <input type="submit"	 name="orden" 	value="<?=$orden?>">
 <input type="submit"	 name="orden" 	value="Volver">
</form> 

