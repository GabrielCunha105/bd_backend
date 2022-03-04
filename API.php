<?php
  include("connect.php");
  header('Content-Type: application/json; charset=utf-8');
  
  if(!($_GET and isset($_GET["route"]))) {
    http_response_code(422);
    die(json_encode((object)["erro" => "rota não especificada"]));
  }

  switch($_GET["route"]) {

    case "1":
      // PEGA OS FILMES COM 3 HORAS OU MAIS DE DURAÇÃO (Grupo 1)
      $query = "
      SELECT title, runtime
      FROM Filme
      WHERE runtime >= 240;";
      break;

    case "2":
      // PEGA OS NOMES DOS FILMES DA PRODUTORA DISNEY (Grupo 2)
      $query = "
      SELECT title
      FROM Filme LEFT JOIN Produtora
      ON Filme.fk_Produtora_name = Produtora.name
      WHERE Filme.fk_Produtora_name = \"Disney\";";
      break;

    case "3":
      // PEGA OS 10 REVIEWS MAIS RECENTES DO FILME CRIMINAL (Grupo 2)
      $query = "
      SELECT content, review_date
      FROM Review INNER JOIN Filme
      ON Review.fk_Filme_rotten_tomatoes_link = Filme.rotten_tomatoes_link AND 
      Filme.title = \"Criminal\" 
      ORDER BY review_date DESC LIMIT 10;";
      break;

    case "4":
      // PEGA A QUANTIDADE DE FILMES DO GÊNERO AÇÃO DE CADA FAIXA ETÁRIA (Grupo 3)
      $query = "
      SELECT content_rating, count(*)
      FROM Filme INNER JOIN Possui 
      ON Filme.rotten_tomatoes_link = Possui.fk_Filme_rotten_tomatoes_link
      INNER JOIN Genero
      ON Possui.fk_Genero_name = Genero.name
      WHERE Genero.name = \"Action & Adventure\"
      GROUP BY content_rating;";
      break;

    case "5":
      // PEGA OS 10 FILMES COM MAIOR NOTA DOS ESTÚDIOS DISNEY NO ROTTEN TOMATOS (Grupo 3)
      $query = "
      SELECT title, tomatometer.rating
      FROM Filme INNER JOIN tomatometer INNER JOIN Produtora
      ON Filme.fk_tomatometer_tomatometer_PK = tomatometer.tomatometer_PK AND 
      Filme.fk_Produtora_name = Produtora.name AND Filme.fk_Produtora_name LIKE \"%Disney%\" 
      ORDER BY tomatometer.rating DESC limit 10;";
      break;

    case "6":
      // RETORNA OS FILMES DIRIGIDOS PELO STEVEN SPIELBERG DE AÇÃO E AVENTURA (Grupo 4)
      $query = "
      SELECT title FROM (
        SELECT fk_Filme_rotten_tomatoes_link FROM Diretor WHERE name = \"Steven Spielberg\"
        AND fk_Filme_rotten_tomatoes_link IN
        (SELECT fk_Filme_rotten_tomatoes_link FROM Possui WHERE fk_Genero_name = \"Action & Adventure\")
      ) as IDs
      INNER JOIN Filme ON IDs.fk_Filme_rotten_tomatoes_link = Filme.rotten_tomatoes_link";
      break;

    case "7":
      // RETORNA OS NOMES DAS 10 PRODUTORAS COM MAIS REVIEWS "Rotten" (Grupo 5)
      $query = "
      SELECT publisher_name, COUNT(*) AS rotten_reviews
      FROM Review INNER JOIN Critico 
      ON Critico.critic_id = Review.fk_Critico_critic_id
      WHERE review_type = \"Rotten\"
      GROUP BY publisher_name
      ORDER BY rotten_reviews DESC LIMIT 10;";
      break;

    case "8":
      // RETORNA A QUANTIDADE DE FILMES QUE SAIRAM EM CADA ANO (Grupo 5)
      $query = "
      SELECT YEAR(release_date) as release_year, count(*)
      FROM Filme 
      GROUP BY release_year 
      ORDER BY release_year DESC;";
      break;

    case "9":
      // DA A QUANTIDADE TOTAL DE FILMES DAS 10 PRODUTORAS QUE PRODUZIRAM MAIS FILMES (Grupo 5)
      $query = "
      SELECT fk_Produtora_name, COUNT(*) AS num_films
      FROM Filme 
      WHERE fk_Produtora_name != \"\"
      GROUP BY fk_Produtora_name
      ORDER BY num_films DESC LIMIT 10;";
      break;

    case "10":
      // LISTA AS 10 MELHORES AVALIAÇÕES MÉDIAS DE TODAS AS PRODUTORAS COM PELO MENOS 5 FILMES (Grupo 6)
      $query = "
      SELECT x.fk_Produtora_name, avg(x.avaliacoes) AS average_rating
      FROM (SELECT title, fk_Produtora_name, avg(tomatometer.rating) as avaliacoes
      FROM Filme INNER JOIN tomatometer
      ON Filme.fk_tomatometer_tomatometer_PK = tomatometer.tomatometer_PK
      WHERE fk_Produtora_name != \"\" AND fk_Produtora_name IN (SELECT fk_Produtora_name FROM 
      (SELECT fk_Produtora_name, count(*) AS num_films FROM Filme GROUP BY fk_Produtora_name) 
      AS produtora_num_filmes WHERE num_films >= 5) 
      GROUP BY title, fk_Produtora_name) as x
      GROUP BY x.fk_Produtora_name
      ORDER BY average_rating DESC LIMIT 10;";
      break;

    default:
      http_response_code(422);
      die(json_encode((object)["erro" => "rota inexistente"]));

  }

  $result = $conn->query($query);

  if (!$result) {
    http_response_code(500);
    die(json_encode((object)["erro" => "Erro ao realizar consulta no banco de dados"]));
  }

  echo(json_encode($result->fetch_all(MYSQLI_ASSOC)));

?>