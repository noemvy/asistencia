<?php 
$host = "localhost";
$usuario = "d52024";
$pass = "12345";
$db = "conciliacion";
try{
    $conect = mysqli_connect("$host","$usuario","$pass","$db");
}catch (mysqli_exception $e){
    echo 'error'.$e->getMessage();
}


?>






