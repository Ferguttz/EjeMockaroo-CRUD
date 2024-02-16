<hr>
<head>
<script type="text/javascript" src="web/js/funciones.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>
<br><br>

<table>
 <tr><td>id:</td> 
 <td><input type="number" name="id" value="<?=$cli->id ?>"  readonly > </td>
    <td rowspan="7">
        <img width="250px" src=<?= imagenPerfil($cli->id) ?>></img>
    </td>
    <td rowspan="7">
    <div id="map" style="height: 200px; width: 370px;"></div>
    </td>  
</tr>
 <tr><td>first_name:</td> 
 <td><input type="text" name="first_name" value="<?=$cli->first_name ?>" readonly > </td></tr>
 </tr>
 <tr><td>last_name:</td> 
 <td><input type="text" name="last_name" value="<?=$cli->last_name ?>" readonly ></td></tr>
 </tr>
 <tr><td>email:</td> 
 <td><input type="email" name="email" value="<?=$cli->email ?>"   readonly  ></td></tr>
 </tr>
 <tr><td>gender</td> 
 <td><input type="text" name="gender" value="<?=$cli->gender ?>" readonly ></td></tr>
 </tr>
 <tr><td>ip_address:</td> 
 <td><input type="text" name="ip_address" value="<?=$cli->ip_address ?>" readonly >
 <img src=<?= banderaIp($cli->ip_address) ?> style="width: 24px;height: 21px;border-radius: unset;margin-bottom: -5px; margin-left: 10px"></td>
</tr>
 </tr>
 <tr><td>telefono:</td> 
 <td><input type="tel" name="telefono" value="<?=$cli->telefono ?>" readonly ></td></tr>
 </tr>
 </table>