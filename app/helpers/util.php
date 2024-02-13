<?php
define('MAX_UPLOAD', 500000); //Maximo de tamaño de archivos entre todos 500Kb
define('DIRIMAGEN',"app/uploads/"); //Ruta Directorio de imgusers


/*
 *  Funciones para limpiar la entrada de posibles inyecciones
 */

function limpiarEntrada(string $entrada):string{
    $salida = trim($entrada); // Elimina espacios antes y después de los datos
    $salida = strip_tags($salida); // Elimina marcas
    return $salida;
}
// Función para limpiar todos elementos de un array
function limpiarArrayEntrada(array &$entrada){
 
    foreach ($entrada as $key => $value ) {
        $entrada[$key] = limpiarEntrada($value);
    }
}


//Función par comprobar los datos envidos en Nuevo y Modificar
function chequeoDatos($datos,$imagen) : bool {

    //En caso de ERROR dentro de la funcion se le asigna la variable $_SESSION['msg']
    if (!chequeoImagen($datos,$imagen)) {
        $_SESSION['autofocus'] = "";
        return false;
    }

    //Comprobar Teléfono
    if (!preg_match("/\d{3}\-\d{3}\-\d{4}$/", $datos['telefono'])) {
        $_SESSION['msg'] = "Error numero dígitos en el teléfono";
        $_SESSION['autofocus'] = "telefono";
        return false;
    }
    
    //Comprobar Direccion IP
    if (!filter_var($datos['ip_address'], FILTER_VALIDATE_IP)) {
        $_SESSION['msg'] = "Error en dirección IP";
        $_SESSION['autofocus'] = "ip_address";
        return false;
    }

    //Comprbamos formato del correo Electrónico
    if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['msg'] = "Formato inválido de correo Electrónico";
        $_SESSION['autofocus'] = "email";
        return false;
    }

    //Comprobar que no se repite el correo electrónico
    $db = AccesoDatos::getModelo();
    if($db->correoRepetido($datos['email'],$datos['id'])) {
        $_SESSION['msg'] = "Correo Electrónico Duplicado";
        $_SESSION['autofocus'] = "email";
        return false;
    }

    $_SESSION['autofocus'] = 'first_name';
    return true;
}

function chequeoImagen($datos,$imagen) : bool {
    //Comprobar si estamos en Modificar o Nuevo.
    //Caso 1 (Nuevo): Comprobamos el siguiente Auto Icrement del Id
    //Caso 2 (Modificar): Asiganamos a Id el que nos vino en el POST
    if ($datos['id'] == "") {
        $db = AccesoDatos::getModelo();
        $id = $db->siguienteId();
    } else {
        $id = $datos['id'];
    }

    //Comprobamos qué tipo de error nos puede dar
    $error = $imagen['imagen']['error'];
    if ($error != 4) {
        //El error 0 es caso de éxito por parte de CLIENTE. Si es distinto se avisa el error
        if($error != 0) {
            $_SESSION['msg'] = codErrorImagen($error);
            return false;
        }

        //Comprobar por parte de SERVIDOR. Si hay texto en $comprobar es que hay error
        $comprobar = comprobarImagen($imagen);
        if ($error == 0 && $comprobar != "") {
            $_SESSION['msg'] = $comprobar;
            return false;
        }

        //Si hay texto en $comprobar es que no se ha habido error en mover la imagen
        $comprobar = moverImagen($id,$imagen);
        if ($comprobar != "") {
            $_SESSION['msg'] = $comprobar;
            return false;
        }
        
    }

    return true;

}

//Función para asignar una foto de perfil
function imagenPerfil($id) : string {
    $resu = "";

    $plantilla = "00000000";
    $foto = substr($plantilla,0,-strlen($id)).$id;
    $ruta = file_exists(DIRIMAGEN."$foto.jpg") ? DIRIMAGEN."$foto.jpg" : DIRIMAGEN."$foto.png";

    if (file_exists($ruta)) {
        $resu = $ruta;
    } else {
        $resu = "https://robohash.org/$foto";
    }

    return $resu;
}

//Seleccionar la bandera según IP
function banderaIp($ip) : string {
    $pais_JSON = file_get_contents("http://ip-api.com/json/$ip?fields=status,countryCode,query");
    $pais = json_decode($pais_JSON,true);
    if($pais['status']=="success") {
        $code = strtolower($pais['countryCode']);
        //return "https://flagcdn.com/36x27/$code.png";
        return "https://flagcdn.com/$code.svg";
    }

    //Retornar la bandera de las naciones unidas como bandera genérica
    return "https://flagcdn.com/un.svg";
}

//Funcion Para mover una Imagen
function moverImagen($id,$imagen) : string {
    $nombre = generarNombreImagen($id,$imagen['imagen']['name']);
    $temporal = $imagen['imagen']['tmp_name'];
    $msg = '';


    if ( is_dir(DIRIMAGEN) && is_writable (DIRIMAGEN)) {
        if(file_exists(DIRIMAGEN.$nombre)) unlink(DIRIMAGEN.$nombre);
        if (move_uploaded_file($temporal,  DIRIMAGEN . $nombre) == false) {
            $msg .= 'La imagen no se ha guardado correctamente <br />';
        }
    } else {
        $msg .= 'ERROR: No es un directorio correcto o no se tiene permiso de escritura <br />';
    }

    return $msg;
}

//Funcion Axiliar para generar nombre de la imagen para alta ed nuevo usuario
function generarNombreImagen($id,$nombre) : string {
    $plantilla = "00000000";
    $foto = substr($plantilla,0,-strlen($id)).$id;
    $extencion = pathinfo($nombre,PATHINFO_EXTENSION);

    $resu = $foto. "." . $extencion ;

    //Compruebo si existe ya una foto en la carpeta de uploads
    $fotoRepe = glob(DIRIMAGEN.$foto . ".*");
    if(!empty($fotoRepe)) unlink($fotoRepe[0]);

    return $resu;
}



/**
 * Funciones para Tratar la imagen de perfil
 * 
*/

//Devolver el tipo de error
function codErrorImagen($error) : string {
    $codigosErrorSubida= [ 
        UPLOAD_ERR_OK         => 'Subida correcta',  // Valor 0
        UPLOAD_ERR_INI_SIZE   => 'El tamaño del archivo excede el admitido por el servidor',  // directiva upload_max_filesize en php.ini
        UPLOAD_ERR_FORM_SIZE  => 'El tamaño del archivo excede el admitido por el cliente',  // directiva MAX_FILE_SIZE en el formulario HTML
        UPLOAD_ERR_PARTIAL    => 'El archivo no se pudo subir completamente',
        UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo para ser subido',
        UPLOAD_ERR_NO_TMP_DIR => 'No existe un directorio temporal donde subir el archivo',
        UPLOAD_ERR_CANT_WRITE => 'No se pudo guardar el archivo en disco',  // permisos
        UPLOAD_ERR_EXTENSION  => 'Una extensión PHP evito la subida del archivo',  // extensión PHP
    
    ]; 

    return $codigosErrorSubida[$error];
}

//Hacer las diferentes comprobaciones a la imagen
function comprobarImagen($imagen) : string {
    $resu = '';

    $resu .= comprobarTamanio($imagen['imagen']['size']);
    $resu .= comprobarFormato($imagen['imagen']['type']);

    return $resu;
}

//Comprobar Tamaño de imagen
function comprobarTamanio($tamanio) : string {
    return ($tamanio > MAX_UPLOAD) ? "Tamaño Exedido en Imagen<br>" : "";
}

//Comprobar Formato Imagen
function comprobarFormato($formato) : string {
    return ($formato == "image/jpeg" || $formato == "image/png") ? "" : "Error Formato en Imagen";
}