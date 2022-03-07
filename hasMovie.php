<?php
  include("connect.php");
  header('Content-Type: application/json; charset=utf-8');
  header('Access-Control-Allow-Origin: *');
  
  // Retorna erro se o número da query não foi especificado
  if(!($_GET and isset($_GET["movie_title"]))) {
    http_response_code(422);
    die(json_encode((object)["erro" => "filme não especificado"]));
  }

  $query = "SELECT title FROM Filme WHERE title = " . $_GET["movie_title"];

  $result = $conn->query($query);

  // Retorna o resultado da query
  if (mysqli_num_rows($result) == 0) {
    echo("{\"hasMovie\" : false}");
  } else {
    echo("{\"hasMovie\" : true}");
  }

?>