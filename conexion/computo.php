<?php
$host = "localhost";
$usuario = "d52024";
$pass = "12345";
$bd = "computo";

try{
    $conect = new PDO("mysql: host:$host; bdname: $bd",$usuario,$pass);
    echo ("se conecto a computo");
}catch (PDOException $e){
    die('Error: '.$e ->getMessage());
}

?>