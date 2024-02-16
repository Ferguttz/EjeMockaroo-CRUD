<?php

/*
 * Acceso a datos con BD Usuarios : 
 * Usando la librería PDO *******************
 * Uso el Patrón Singleton :Un único objeto para la clase
 * Constructor privado, y métodos estáticos 
 */

//SELECT * FROM clientes WHERE id > 5 ORDER BY id LIMIT 1;

use Mpdf\Tag\B;

class AccesoDatos {
    
    private static $modelo = null;
    private $dbh = null;
    
    public static function getModelo(){
        if (self::$modelo == null){
            self::$modelo = new AccesoDatos();
        }
        return self::$modelo;
    }
    
    

   // Constructor privado  Patron singleton
   
    private function __construct(){
        try {
            $dsn = "mysql:host=".DB_SERVER.";dbname=".DATABASE.";charset=utf8";
            $this->dbh = new PDO($dsn,DB_USER,DB_PASSWD);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e){
            echo "Error de conexión ".$e->getMessage();
            exit();
        }  

    }

    // Cierro la conexión anulando todos los objectos relacioanado con la conexión PDO (stmt)
    public static function closeModelo(){
        if (self::$modelo != null){
            $obj = self::$modelo;
            // Cierro la base de datos
            $obj->dbh = null;
            self::$modelo = null; // Borro el objeto.
        }
    }


    // Devuelvo cuantos filas tiene la tabla

    public function numClientes ():int {
      $result = $this->dbh->query("SELECT id FROM Clientes");
      $num = $result->rowCount();  
      return $num;
    } 
    

    // SELECT Devuelvo la lista de Usuarios
    public function getClientes ($primero,$cuantos,$ordenacion):array {
        $tuser = [];
        // Crea la sentencia preparada
       // echo "<h1> $primero : $cuantos  </h1>";
        $stmt_usuarios  = $this->dbh->prepare("select * from Clientes ORDER BY $ordenacion limit $primero,$cuantos");
        // Si falla termina el programa
        $stmt_usuarios->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
    
        if ( $stmt_usuarios->execute() ){
            while ( $user = $stmt_usuarios->fetch()){
               $tuser[]= $user;
            }
        }
                // Devuelvo el array de objetos
        return $tuser;
    }
    
      
    // SELECT Devuelvo un usuario o false
    public function getCliente (int $id) {
        $cli = false;
        $stmt_cli   = $this->dbh->prepare("select * from Clientes where id=:id");
        $stmt_cli->setFetchMode(PDO::FETCH_CLASS, 'Cliente');
        $stmt_cli->bindParam(':id', $id);
        if ( $stmt_cli->execute() ){
             if ( $obj = $stmt_cli->fetch()){
                $cli= $obj;
            }
        }
        return $cli;
    }

     
    public function getClienteSiguiente($ordenacion,$dato){
        $id = 0;
        
        //Preparo la sentencia en bsae a la ordenacion impuesta y el dato ordenado actual
        $stmt_siguiente   = $this->dbh->prepare("select id from Clientes where $ordenacion > ? ORDER BY $ordenacion limit 1");
        $stmt_siguiente->bindParam(1,$dato);
        $stmt_siguiente->execute();

        //Si la no han sido afectadas ninguna fila es que estamos en la última dato de la base
        if ( $stmt_siguiente->rowCount() == 1){
            $id = $stmt_siguiente->fetch()[0];
        }


        return $id;
    }

    public function getClienteAnterior($ordenacion,$dato){
        $id = 0;
        
        //Preparo la sentencia en bsae a la ordenacion impuesta y el dato ordenado actual
        $stmt_siguiente   = $this->dbh->prepare("select id from Clientes where $ordenacion < ? ORDER BY $ordenacion DESC limit 1");
        $stmt_siguiente->bindParam(1,$dato);
        $stmt_siguiente->execute();

        //Si la no han sido afectadas ninguna fila es que estamos en la última dato de la base
        if ( $stmt_siguiente->rowCount() == 1){
            $id = $stmt_siguiente->fetch()[0];
        }


        return $id;
    }




    // UPDATE TODO
    public function modCliente($cli):bool{
      
        $stmt_moduser   = $this->dbh->prepare("update Clientes set first_name=:first_name,last_name=:last_name".
        ",email=:email,gender=:gender, ip_address=:ip_address,telefono=:telefono WHERE id=:id");
        $stmt_moduser->bindValue(':first_name', $cli->first_name);
        $stmt_moduser->bindValue(':last_name'   ,$cli->last_name);
        $stmt_moduser->bindValue(':email'       ,$cli->email);
        $stmt_moduser->bindValue(':gender'      ,$cli->gender);
        $stmt_moduser->bindValue(':ip_address'  ,$cli->ip_address);
        $stmt_moduser->bindValue(':telefono'    ,$cli->telefono);
        $stmt_moduser->bindValue(':id'          ,$cli->id);

        $stmt_moduser->execute();
        $resu = ( ($stmt_moduser->rowCount () == 1) || ($stmt_moduser->rowCount () == 0));
        return $resu;
    }

  
    //INSERT 
    public function addCliente($cli):bool{
       
        // El id se define automáticamente por autoincremento.
        $stmt_crearcli  = $this->dbh->prepare(
            "INSERT INTO `Clientes`( `first_name`, `last_name`, `email`, `gender`, `ip_address`, `telefono`)".
            "Values(?,?,?,?,?,?)");
        $stmt_crearcli->bindValue(1,$cli->first_name);
        $stmt_crearcli->bindValue(2,$cli->last_name);
        $stmt_crearcli->bindValue(3,$cli->email);
        $stmt_crearcli->bindValue(4,$cli->gender);
        $stmt_crearcli->bindValue(5,$cli->ip_address);
        $stmt_crearcli->bindValue(6,$cli->telefono);    
        $stmt_crearcli->execute();
        $resu = ($stmt_crearcli->rowCount () == 1);
        return $resu;
    }

    public function correoRepetido($correo,$id) : bool {
        $stmt_correo = $this->dbh->prepare("SELECT email,id FROM clientes WHERE email=:email");
        $stmt_correo->bindValue(':email',$correo);

        $stmt_correo->execute();

        //Si las filas resultantes son mayores que 0 hay repetido
        $resu = ($stmt_correo->rowCount() >= 1) ? true : false;

        //Para tratar el caso de PostModificar
        if($resu) $id_correo = $stmt_correo->fetch()[1];
        if ($stmt_correo->rowCount() == 1 && $id == $id_correo) {
            $resu = false;
        }

        return $resu;
    }

    public function siguienteId() : int {
        //Forma de obtener el siguiente número del AutoIncrement
        $stmt_nexId = $this->dbh->prepare("SELECT `AUTO_INCREMENT` 
        FROM INFORMATION_SCHEMA.TABLES 
        WHERE TABLE_SCHEMA = :database 
        AND TABLE_NAME = 'clientes'");
        
        $stmt_nexId->bindValue(':database',DATABASE);
        $stmt_nexId->execute();

        $resu = $stmt_nexId->fetch();
        return $resu[0];
    }
   
    //DELETE 
    public function borrarCliente(int $id):bool {


        $stmt_boruser   = $this->dbh->prepare("delete from Clientes where id =:id");

        $stmt_boruser->bindValue(':id', $id);
        $stmt_boruser->execute();
        $resu = ($stmt_boruser->rowCount () == 1);
        return $resu;
        
    }
    
    function verificarAcceso($login,$password) : bool {
        $stmt = $this->dbh->prepare("SELECT login,password FROM user WHERE login=:login");
        $stmt->bindValue(':login', $login);
        $stmt->execute();
        //Si no existe el login devuelve falso
        if ($stmt->rowCount() != 1) {
            return false;
        }

        //Se devulve según si la contraseña dada coincide con le hash de la base
        $password_Base = $stmt->fetch()[1];
        return md5($password) === $password_Base;
    }


    function getRol($login) : int {
        $stmt = $this->dbh->prepare("SELECT rol FROM user WHERE login=:login");
        $stmt->bindValue(':login', $login);
        $stmt->execute();

        return $stmt->fetch()[0];
    }
    
    
     // Evito que se pueda clonar el objeto. (SINGLETON)
    public function __clone()
    { 
        trigger_error('La clonación no permitida', E_USER_ERROR); 
    }

    
}



