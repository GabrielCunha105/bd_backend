<?php
/** Nome do servidor do banco de dados */
$servername = "localhost";

/** Nome do banco de dados */
$database = "rotten_tomatoes";

/** Nome do usuário */
$username = "root";

/** Senha do usuário */
$password = "";

/** Conexão com o banco de dados */
$conn = new mysqli($servername, $username, $password, $database);

// Verifica a conexão
if ($conn->connect_error) {
  die("Conexão falhou: " . $conn->connect_error);
}

if($_GET and isset($_GET["teste"]) and boolVal($_GET["teste"])) {
  echo("Conexão bem sucedida.");
}

?>
