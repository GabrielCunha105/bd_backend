# Trabalho Final - Banco de dados (ICP-489) 2021/2

## Servir a aplicação
Para servir a aplicação deve-se seguir as seguintes etapas:

1. Criar um servidor Apache e um banco de dados MySQL e extrair todos os arquivos do repositório no servidor Apache.
2. No banco de dados MySQL, executar o script no arquivo `sql/fisico.sql`.
3. Após verificar se o script foi executado com sucesso. No arquivo `connect.php` é necessário atualizar o valor das seguintes variáveis
   * `$servername` - Nome ou endereço do servidor.
   * `$database` - Nome do banco de dados criado.
   * `$username` - Nome de um usuário que tenha total acesso ao banco de dados.
   * `$password` - Senha do usuário.
4. É preciso inserir os arquivos `rotten_tomatoes_critic_reviews.csv` e `rotten_tomatoes_movies.csv`, disponíveis [neste link](https://www.kaggle.com/stefanoleone992/rotten-tomatoes-movies-and-critic-reviews-dataset), na pasta `parser/dados`. Estes arquivos não foram incluídos no repositório pois eles ultrapassam o limite de 100 MB por arquivo do GitHub.
5. Para testar a conexão da aplicação com o banco de dados, acesse a rota \
`[ENDEREÇO_DA_APLICAÇÃO]/connect.php?teste=1`
6. Se o teste da etapa 5 indica uma conexão bem sucedida, então acesse a rota `[ENDEREÇO_DA_APLICAÇÃO]/migrate.php` para migrar os dados dos arquivos csv para o banco de dados (para realizar essa etapa pode ser necessário alterar algumas configurações do MySQL e/ou do Apache. As alterações exatas dependem da instalação e da máquina utilizadas).

## Rotas da aplicação
* `[ENDEREÇO_DA_APLICAÇÃO]/API.php?route=[X]`, onde `X` é o número da query desejada (não incluir as chaves).

### As querys disponíveis são

| X | Descrição da query |
|---|-------------------|
| 1 | PEGA OS FILMES COM 3 HORAS OU MAIS DE DURAÇÃO |
| 2 | PEGA OS NOMES DOS FILMES DA PRODUTORA DISNEY |
| 3 | PEGA OS 10 REVIEWS MAIS RECENTES DO FILME CRIMINAL |
| 4 | PEGA A QUANTIDADE DE FILMES DO GÊNERO AÇÃO DE CADA FAIXA ETÁRIA |
| 5 | PEGA OS 10 FILMES COM MAIOR NOTA DOS ESTÚDIOS DISNEY NO ROTTEN TOMATOS |
| 6 | RETORNA OS FILMES DIRIGIDOS PELO STEVEN SPIELBERG DE AÇÃO E AVENTURA |
| 7 | RETORNA OS NOMES DAS 10 PRODUTORAS COM MAIS REVIEWS "Rotten" |
| 8 | RETORNA A QUANTIDADE DE FILMES QUE SAIRAM EM CADA ANO |
| 9 | DA A QUANTIDADE TOTAL DE FILMES DAS 10 PRODUTORAS QUE PRODUZIRAM MAIS FILMES |
| 10 | LISTA AS 10 MELHORES AVALIAÇÕES MÉDIAS DE TODAS AS PRODUTORAS COM PELO MENOS 5 FILMES |
