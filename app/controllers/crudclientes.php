<?php

function crudBorrar ($id){    
    $db = AccesoDatos::getModelo();
    $resu = $db->borrarCliente($id);
    if ( $resu){
         $_SESSION['msg'] = " El usuario ".$id. " ha sido eliminado.";
    } else {
         $_SESSION['msg'] = " Error al eliminar el usuario ".$id.".";
    }

}

function crudTerminar(){
    AccesoDatos::closeModelo();
    session_destroy();
}
 
function crudAlta(){
    $cli = new Cliente();
    $orden= "Nuevo";
    include_once "app/views/formulario.php";
}

function crudDetalles($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/detalles.php";
}

function crudDetallesSiguiente($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);

    //Obtengo el tipo de ordenacion y el dato actual para mostrar el siguiente
    $ordenacion = $_SESSION['ordenacion'];
    $dato_actual = $cli->$ordenacion;

    //Si el nuevoId es 0 es que estamos en el último dato y no hará falta hacer consultar por un cliente nuevo
    $nuevoId = $db->getClienteSiguiente($ordenacion,$dato_actual);
    $cli = ($nuevoId != 0) ?  $db->getCliente($nuevoId) : $cli;
    include_once "app/views/detalles.php";
}

function crudDetallesAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);

    //Obtengo el tipo de ordenacion y el dato actual para mostrar el anterior
    $ordenacion = $_SESSION['ordenacion'];
    $dato_actual = $cli->$ordenacion;

    //Si el nuevoId es 0 es que estamos en el primer dato y no hará falta hacer consultar por un cliente nuevo
    $nuevoId = $db->getClienteAnterior($ordenacion,$dato_actual);
    $cli = ($nuevoId != 0) ?  $db->getCliente($nuevoId) : $cli;
    include_once "app/views/detalles.php";
}

function crudModificarSiguiente($id)
{
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);

    //Obtengo el tipo de ordenacion y el dato actual para mostrar el anterior
    $ordenacion = $_SESSION['ordenacion'];
    $dato_actual = $cli->$ordenacion;

    //Si el nuevoId es 0 es que estamos en el primer dato y no hará falta hacer consultar por un cliente nuevo
    $nuevoId = $db->getClienteSiguiente($ordenacion,$dato_actual);
    $cli = ($nuevoId != 0) ?  $db->getCliente($nuevoId) : $cli;
    $orden = "Modificar";
    include_once "app/views/formularioModificar.php";
}

function crudModificarAnterior($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);

    //Obtengo el tipo de ordenacion y el dato actual para mostrar el anterior
    $ordenacion = $_SESSION['ordenacion'];
    $dato_actual = $cli->$ordenacion;
    
    //Si el nuevoId es 0 es que estamos en el primer dato y no hará falta hacer consultar por un cliente nuevo
    $nuevoId = $db->getClienteAnterior($ordenacion,$dato_actual);
    $cli = ($nuevoId != 0) ?  $db->getCliente($nuevoId) : $cli;
    $orden = "Modificar";
    include_once "app/views/formularioModificar.php";
}

function crudModificar($id){
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    $orden="Modificar";
    include_once "app/views/formularioModificar.php";
}

function crudPostAlta(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código
    // !!!!!! No se controlan que los datos sean correctos

    if (!chequeoDatos($_POST,$_FILES)) {
        die(crudPostAltaRecuperacion($_POST));
    } 
    $cli = new Cliente();
    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();
    if ( $db->addCliente($cli) ) {
           $_SESSION['msg'] = " El usuario ".$cli->first_name." se ha dado de alta ";
        } else {
            $_SESSION['msg'] = " Error al dar de alta al usuario ".$cli->first_name."."; 
        }
}

function crudPostModificar(){
    limpiarArrayEntrada($_POST); //Evito la posible inyección de código

    if (!chequeoDatos($_POST,$_FILES)) {
        die(crudPostModificarRecuperacion($_POST));
    } 

    $cli = new Cliente();

    $cli->id            =$_POST['id'];
    $cli->first_name    =$_POST['first_name'];
    $cli->last_name     =$_POST['last_name'];
    $cli->email         =$_POST['email'];	
    $cli->gender        =$_POST['gender'];
    $cli->ip_address    =$_POST['ip_address'];
    $cli->telefono      =$_POST['telefono'];
    $db = AccesoDatos::getModelo();
    if ( $db->modCliente($cli) ){
        $_SESSION['msg'] = " El usuario ha sido modificado";
    } else {
        $_SESSION['msg'] = " Error al modificar el usuario ";
    }
    
}

function crudPostAltaRecuperacion($datos) {
    ob_start();
    $cli = new Cliente();

    $cli->id            =$datos['id'];
    $cli->first_name    =$datos['first_name'];
    $cli->last_name     =$datos['last_name'];
    $cli->email         =$datos['email'];	
    $cli->gender        =$datos['gender'];
    $cli->ip_address    =$datos['ip_address'];
    $cli->telefono      =$datos['telefono'];

    $orden="Nuevo";
    require_once "app/views/formulario.php";


    $contenido = ob_get_clean();
    $msg = $_SESSION['msg'];
    include_once "app/views/principal.php";
    exit();
}

function crudPostModificarRecuperacion($datos) {
    ob_start();
    $cli = new Cliente();

    $cli->id            =$datos['id'];
    $cli->first_name    =$datos['first_name'];
    $cli->last_name     =$datos['last_name'];
    $cli->email         =$datos['email'];	
    $cli->gender        =$datos['gender'];
    $cli->ip_address    =$datos['ip_address'];
    $cli->telefono      =$datos['telefono'];

    $orden="Modificar";
    require_once "app/views/formularioModificar.php";


    $contenido = ob_get_clean();
    $msg = $_SESSION['msg'];
    include_once "app/views/principal.php";
    exit();
}

function imprimirUsuario($id) {
    ob_start();
    $db = AccesoDatos::getModelo();
    $cli = $db->getCliente($id);
    include_once "app/views/detallesImprimir.php";
    $contenido = ob_get_clean();


    require_once "vendor/autoload.php";

    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($contenido);
    $mpdf->Output();
}


function accesoControl($login,$password) : bool {
    $db = AccesoDatos::getModelo();
    return $db->verificarAcceso($login,$password);
}

function rolUsuario($login) : int {
    $db = AccesoDatos::getModelo();
    return $db->getRol($login);
}
