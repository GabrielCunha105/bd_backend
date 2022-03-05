<?php

/** Lista de países */
$country_list = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua &amp; Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia &amp; Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Cape Verde", "Cayman Islands", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cruise Ship", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kuwait", "Kyrgyz Republic", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Mauritania", "Mauritius", "Mexico", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Namibia", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Norway", "Oman", "Pakistan", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre &amp; Miquelon", "Samoa", "San Marino", "Satellite", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "South Africa", "South Korea", "Spain", "Sri Lanka", "St Kitts &amp; Nevis", "St Lucia", "St Vincent", "St. Lucia", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad &amp; Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks &amp; Caicos", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "Uruguay", "Uzbekistan", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];

/** IDs de filmes que possuem reviews mas não estão registrados como filmes */
$ghost_movies = ["m/-_man", "m/-cule_valley_of_the_lost_ants",
  "m/patton_oswalt_tragedy_+_comedy_equals_time", "m/+_one_2019", "m/+h", 
  "m/sympathy-for-the-devil-one-+-one"];

/**
 * Retorna um objeto contendo os dados estruturados a serem inseridos no banco
 * de dados. Os dados são lidos dos arquivos csv que devem estar na pasta "dados"
 */
function getTables()
{
  // Abre os arquivos CSV
  $reviews_handle = fopen("./parser/dados/rotten_tomatoes_critic_reviews.csv", "r");
  $movies_handle = fopen("./parser/dados/rotten_tomatoes_movies.csv", "r");

  // Retona erro se não conseguir ler o arquivo
  if (!($reviews_handle && $movies_handle)) {
    exit("Erro ao ler arquivos csv.");
  }

  // Aumenta o limite de memória utilizada pelo programa
  ini_set('memory_limit', '2048M');

  // Faz o parsing nos dados no arquivo rotten_tomatoes_movies.csv
  $tables = new \stdClass();
  $out = parseMoviesCSV($movies_handle);
  $tables->movies = $out->movies;
  $tables->tomatometers = $out->tomatometers;
  $tables->directors = $out->directors;
  $tables->has = $out->has;
  $tables->genres = $out->genres;
  $tables->companies = seedCompanies($out->companies);

  fclose($movies_handle);

  // Faz o parsing nos dados no arquivo rotten_tomatoes_critic_reviews.csv
  $out = parseReviewsCSV($reviews_handle);
  $tables->reviews = $out->reviews;
  $tables->critics = $out->critics;

  fclose($reviews_handle);

  return $tables;
}

/**
 * Lê e faz o parsing nos dados no arquivo rotten_tomatoes_movies.csv e retorna
 * um objeto contendo as tabelas Filmes, tomatometers, Diretores, Possui, Generos
 * e Produtoras
 */
function parseMoviesCSV($handle)
{

  // Descarta a primeira linha
  fgetcsv($handle, 0, ",");

  $count = 0;
  $directors_count = 0;
  $genres_count = 0;
  $movies = [];
  $tomatometers = [];
  $directors = [];
  $has = [];
  $genres = [];
  $companies = [];

  // Itera por todos os registros do CSV
  while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

    // Cria um Filme
    $movies[$count] = new \stdClass();
    $movies[$count]->rotten_tomatoes_link = $data[0];
    $movies[$count]->title = $data[1];
    $movies[$count]->info = $data[2];
    $movies[$count]->critic_concensus = $data[3];
    $movies[$count]->content_rating = $data[4];
    $movies[$count]->release_date = $data[9];
    $movies[$count]->runtime = $data[11];
    $movies[$count]->fk_tomatometer_tomatometer_PK = $count + 1;
    $movies[$count]->fk_Produtora_name = $data[12];

    // Cria uma produtora
    $companies[$count] = str_replace("ä", "a", $data[12]);

    // Cria um tomatometro para o filme
    $tomatometers[$count] = new \stdClass();
    $tomatometers[$count]->tomatometer_PK = $count + 1;
    $tomatometers[$count]->status = $data[13];
    $tomatometers[$count]->rating = $data[14];
    $tomatometers[$count]->tomatometer_count = $data[15];

    $directors_arr = str_getcsv($data[6]);
    foreach ($directors_arr as $director) {

      //Cria um diretor
      $directors[$directors_count] = new \stdClass();
      $directors[$directors_count]->fk_Filme_rotten_tomatoes_link = $data[0];
      $directors[$directors_count]->name = trim($director);
      $directors_count++;
    }

    $genres_arr = str_getcsv($data[5]);
    $genre_movie = 1;
    foreach ($genres_arr as $genre) {

      // Cria um Genero
      $genres[$genres_count] = trim($genre);

      // Cria uma entrada na table  Possui
      $has[$genres_count] = new \stdClass();
      $has[$genres_count]->fk_Genero_name = trim($genre);
      $has[$genres_count]->fk_Filme_rotten_tomatoes_link = $data[0];
      $has[$genres_count]->ordem = $genre_movie;

      $genres_count++;
      $genre_movie++;
    }

    $count++;
  }

  // Retorna as tabelas criadas
  $out = new \stdClass();
  $out->movies = $movies;
  $out->tomatometers = $tomatometers;
  $out->directors = $directors;
  $out->has = $has;
  $out->genres = array_unique($genres);
  $out->companies = array_unique($companies);

  return $out;
}

/**
 * Lê e faz o parsing nos dados no arquivo rotten_tomatoes_critic_reviews.csv e retorna
 * um objeto contendo as tabelas Reviews e Criticos.
 */
function parseReviewsCSV($handle)
{
  global $ghost_movies;

  // Descarta a primeira linha
  fgetcsv($handle, 0, ",");

  $reviews = [];
  $critics = [];
  $count = 0;

  // Itera por todos os registros do CSV
  while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

    // Ignora os filmes fantasma
    if (in_array($data[0], $ghost_movies)) {
      continue;
    }

    // Cria uma review
    $reviews[$count] = new \stdClass();
    $reviews[$count]->review_id = $count + 1;
    $reviews[$count]->type = $data[4];
    $reviews[$count]->score = $data[5];
    $reviews[$count]->review_date = $data[6];
    $reviews[$count]->content = $data[7];
    $reviews[$count]->fk_Critico_critic_id = $count + 1;
    $reviews[$count]->fk_Filme_rotten_tomatoes_link = $data[0];

    // Cria um crítico
    $critics[$count] = new \stdClass();
    $critics[$count]->critic_id = $count + 1;
    $critics[$count]->name = $data[1];
    $critics[$count]->top_critic = $data[2];
    $critics[$count]->publisher_name = $data[3];

    $count++;
  }

  // Retorna as tabelas criadas
  $out = new \stdClass();
  $out->reviews = $reviews;
  $out->critics = $critics;

  return $out;
}

/**
 * Gera dados aleatórios para as produtoras para atender às especificações
 * mínimas do trabalho final
 */
function seedCompanies($names)
{
  global $country_list;
  $companies = [];
  $i = 0;
  foreach ($names as $name) {
    $companies[$i] = new \stdClass();
    $companies[$i]->name = $name;
    $companies[$i]->foundation_year = random_int(1910, 2010);
    $companies[$i]->country = $country_list[random_int(0, count($country_list) - 1)];
    $i++;
  }
  return $companies;
}
