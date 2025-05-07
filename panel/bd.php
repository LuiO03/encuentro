<?php 
    $servidor="localhost";
    $baseDatos="encuentro";
    $usuario="root";
    $contrasenia="";
    
    try{
        $conect= new PDO("mysql:host=$servidor;dbname=$baseDatos", $usuario, $contrasenia);
    }catch(Exception $error){
        echo $error->getMessage();
    }

    session_start();
?>