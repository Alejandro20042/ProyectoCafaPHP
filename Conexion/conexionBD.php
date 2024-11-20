<?php
global $conn; // Definimos la conexión como global
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proyectocafa";

// $servername = "localhost";
// $username = "root";
// $password = "";  
// $dbname = "Proyectonissan";   
// $con=mysqli_connect($hostname,$username,$password,$database);   

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>