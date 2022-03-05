<?php

  include("connect.php");
  include("./parser/parser.php");

  // Lê os dados das tabelas csv
  $tables = getTables();

  migrateTables();

  echo("Banco de dados migrado com sucesso.");

  /**
   * Migra as tabelas no banco de dados
   */
  function migrateTables() {
    migrateTomatometers();
    migrateCompanies();
    migrateMovies();
    migrateGenres();
    migrateHas();
    migrateDirectors();
    migrateCritics();
    migrateReviews();
  }

  /**
   * Migra a tabela tomatometer no Banco de dados
   */
  function migrateTomatometers(){

    global $tables;
    global $conn;

    $tomatometers_query = "INSERT INTO tomatometer (tomatometer_PK, status, rating, " .
      "tomatometer_count) VALUES";

    foreach($tables->tomatometers as $tomatometer) {
      $tomatometers_query .= sprintf("(%s, \"%s\", %s, %s),",
        $tomatometer->tomatometer_PK, $tomatometer->status, 
        $tomatometer->rating? $tomatometer->rating : "0" , 
        $tomatometer->tomatometer_count? $tomatometer->tomatometer_count : "0" );
    }
    $tomatometers_query[strlen($tomatometers_query)-1]= ";";

    $tomatometers_result = $conn->query($tomatometers_query);

    // Retorna erro em caso de falha na query
    if(!$tomatometers_result) {
      http_response_code(500);
      die("Erro ao salvar tomatometros no banco de dados.");
    }

    $tables->tomatometers = null;
  }

  /**
   * Migra a tabela Produtoras no Banco de dados
   */
  function migrateCompanies(){

    global $tables;
    global $conn;

    $companies_query = "INSERT INTO Produtora (name, country, " .
      "foundation_year) VALUES";

    foreach($tables->companies as $company) {
      $companies_query .= sprintf("(\"%s\", \"%s\", %s),",
        $company->name, $company->country, $company->foundation_year);
    }

    $companies_query[strlen($companies_query)-1]= ";";

    $companies_result = $conn->query($companies_query);

    // Retorna erro em caso de falha na query
    if(!$companies_result) {
      http_response_code(500);
      die("Erro ao salvar produtoras no banco de dados.");
    }

    $tables->companies = null;
  }

  /**
   * Migra a tabela Filme no Banco de dados
   */
  function migrateMovies() {

    global $tables;
    global $conn;

    $movies_query = "INSERT INTO Filme (rotten_tomatoes_link, title, info, " .
      "critic_concensus, content_rating, release_date, runtime, " . 
      "fk_tomatometer_tomatometer_PK, fk_Produtora_name) VALUES";

    foreach($tables->movies as $movie) {
      $movies_query .= sprintf("(\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", %s, %s, %s, \"%s\"),",
        $movie->rotten_tomatoes_link, $movie->title, mysqli_real_escape_string($conn, $movie->info),
        mysqli_real_escape_string($conn, $movie->critic_concensus),
        $movie->content_rating,
        $movie->release_date? "\"" . $movie->release_date . "\"" : "null",
        $movie->runtime? $movie->runtime : "null",
        $movie->fk_tomatometer_tomatometer_PK, $movie->fk_Produtora_name );
    }
    $movies_query[strlen($movies_query)-1]= ";";

    $movies_result = $conn->query($movies_query);

    // Retorna erro em caso de falha na query
    if(!$movies_result) {
      http_response_code(500);
      die("Erro ao salvar filmes no banco de dados.");
    }

    $tables->movies = null;
  }

  /**
   * Migra a tabela Genero no Banco de dados
   */
  function migrateGenres() {

    global $tables;
    global $conn;

    $genres_query = "INSERT INTO Genero (name) VALUES";

    foreach($tables->genres as $genre) {
      if ($genre)
        $genres_query .= sprintf("(\"%s\"),", $genre );
    }
    $genres_query[strlen($genres_query)-1]= ";";

    $genres_result = $conn->query($genres_query);

    // Retorna erro em caso de falha na query
    if(!$genres_result) {
      http_response_code(500);
      die("Erro ao salvar gêneros no banco de dados.");
    }

    $tables->genres = null;
  }

  /**
   * Migra a tabela Possui no Banco de dados
   */
  function migrateHas() {

    global $tables;
    global $conn;

    $has_query = "INSERT INTO Possui (fk_Genero_name, fk_Filme_rotten_tomatoes_link, ordem) VALUES";

    foreach($tables->has as $has) {
      if ($has->fk_Genero_name)
        $has_query .= sprintf("(\"%s\", \"%s\", %s),",
          $has->fk_Genero_name, $has->fk_Filme_rotten_tomatoes_link, $has->ordem);
    }
    $has_query[strlen($has_query)-1]= ";";

    $has_result = $conn->query($has_query);

    // Retorna erro em caso de falha na query
    if(!$has_result) {
      http_response_code(500);
      die("Erro ao salvar tabela \"Possui\" no banco de dados.");
    }

    $tables->has = null;
  }

  /**
   * Migra a tabela Diretor no Banco de dados
   */
  function migrateDirectors() {

    global $tables;
    global $conn;

    $directors_query = "INSERT INTO Diretor (fk_Filme_rotten_tomatoes_link, name) VALUES";

    foreach($tables->directors as $director) {
      $directors_query .= sprintf("(\"%s\", \"%s\"),",
        $director->fk_Filme_rotten_tomatoes_link, 
        mysqli_real_escape_string($conn, $director->name));
    }
    $directors_query[strlen($directors_query)-1]= ";";

    $directors_result = $conn->query($directors_query);

    // Retorna erro em caso de falha na query
    if(!$directors_result) {
      http_response_code(500);
      die("Erro ao salvar diretores no banco de dados.");
    }

    $tables->directors = null;
  }

  /**
   * Migra a tabela Critico no Banco de dados
   */
  function migrateCritics() {

    global $tables;
    global $conn;

    $critics_query = "INSERT INTO Critico (critic_id, name, top_critic, publisher_name) VALUES";

    foreach($tables->critics as $critic) {
      $critics_query .= sprintf("(%s, \"%s\", %s, \"%s\"),",
        $critic->critic_id, mysqli_real_escape_string($conn, $critic->name,),
        $critic->top_critic == "True"? "TRUE" : "FALSE",
        mysqli_real_escape_string($conn, $critic->publisher_name));
    }
    $critics_query[strlen($critics_query)-1]= ";";

    $critics_result = $conn->query($critics_query);

    // Retorna erro em caso de falha na query
    if(!$critics_result) {
      http_response_code(500);
      die("Erro ao salvar criticos no banco de dados.");
    }

    $tables->critics = null;
  }

  /**
   * Migra a tabela Review no Banco de dados
   */
  function migrateReviews() {

    global $tables;
    global $conn;

    $reviews_query = "INSERT INTO Review (review_id, review_type, score, review_date, " . 
      "content, fk_Critico_critic_id, fk_Filme_rotten_tomatoes_link) VALUES";

    $i = 0;
    foreach($tables->reviews as $review) {
      $reviews_query .= sprintf("(%s, \"%s\", \"%s\", \"%s\", \"%s\", %s, \"%s\"),",
        $review->review_id, $review->type, $review->score, $review->review_date,
        mysqli_real_escape_string($conn, $review->content), 
        $review->fk_Critico_critic_id, $review->fk_Filme_rotten_tomatoes_link );
        
      $i++;

      // Quebra a query de inserção em querys menores devido ao grade volume de
      // dados das reviews
      if ($i % 100000 == 0) {
        $reviews_query[strlen($reviews_query)-1]= ";";
      
        $reviews_result = $conn->query($reviews_query);
      
        // Retorna erro em caso de falha na query
        if(!$reviews_result) {
          http_response_code(500);
          printf("Error message: %s\n", $conn->error);
          die("Erro ao salvar reviews no banco de dados.");
        }

        $reviews_query = "INSERT INTO Review (review_id, review_type, score, review_date, " . 
          "content, fk_Critico_critic_id, fk_Filme_rotten_tomatoes_link) VALUES";
      }
    }
    $reviews_query[strlen($reviews_query)-1]= ";";

    $reviews_result = $conn->query($reviews_query);

    // Retorna erro em caso de falha na query
    if(!$reviews_result) {
      http_response_code(500);
      printf("Error message: %s\n", $conn->error);
      die("Erro ao salvar reviews no banco de dados.");
    }

    $tables->reviews = null;
  }
  
?>
