/* Fisico */

CREATE TABLE Review (
	review_id INT PRIMARY KEY,
	review_type VARCHAR(255),
	score VARCHAR(255),
	review_date DATE,
	content TEXT,
	fk_Critico_critic_id INT,
	fk_Filme_rotten_tomatoes_link VARCHAR(255)
);

CREATE TABLE Critico (
	critic_id INT PRIMARY KEY,
	name VARCHAR(255),
	top_critic BOOLEAN,
	publisher_name VARCHAR(255)
);

CREATE TABLE Filme (
	rotten_tomatoes_link VARCHAR(255) PRIMARY KEY,
	title VARCHAR(255),
	info TEXT,
	critic_concensus TEXT,
	content_rating VARCHAR(255),
	release_date DATE,
	runtime INT,
	fk_tomatometer_tomatometer_PK INT,
	fk_Produtora_name VARCHAR(255)
);

CREATE TABLE Produtora (
	name VARCHAR(255) PRIMARY KEY,
	country VARCHAR(255),
	foundation_year INT
);

CREATE TABLE Genero (
	name VARCHAR(255) PRIMARY KEY
);

CREATE TABLE Diretor (
	fk_Filme_rotten_tomatoes_link VARCHAR(255),
	name VARCHAR(255)
);

CREATE TABLE tomatometer (
	tomatometer_PK INT NOT NULL PRIMARY KEY,
	status VARCHAR(255),
	rating INT,
	tomatometer_count INT
);

CREATE TABLE Possui (
	fk_Genero_name VARCHAR(255),
	fk_Filme_rotten_tomatoes_link VARCHAR(255),
	ordem INT,
	PRIMARY KEY (fk_Filme_rotten_tomatoes_link, fk_Genero_name)
);

ALTER TABLE Review ADD CONSTRAINT FK_Review_2
	FOREIGN KEY (fk_Critico_critic_id)
	REFERENCES Critico(critic_id)
	ON DELETE CASCADE;

ALTER TABLE Review ADD CONSTRAINT FK_Review_3
	FOREIGN KEY (fk_Filme_rotten_tomatoes_link)
	REFERENCES Filme(rotten_tomatoes_link)
	ON DELETE CASCADE;

ALTER TABLE Filme ADD CONSTRAINT FK_Filme_2
	FOREIGN KEY (fk_tomatometer_tomatometer_PK)
	REFERENCES tomatometer(tomatometer_PK)
	ON DELETE SET NULL;

ALTER TABLE Filme ADD CONSTRAINT FK_Filme_3
	FOREIGN KEY (fk_Produtora_name)
	REFERENCES Produtora(name)
	ON DELETE CASCADE;

ALTER TABLE Diretor ADD CONSTRAINT FK_Diretor_2
	FOREIGN KEY (fk_Filme_rotten_tomatoes_link)
	REFERENCES Filme(rotten_tomatoes_link)
	ON DELETE CASCADE;

ALTER TABLE Possui ADD CONSTRAINT FK_Possui_1
	FOREIGN KEY (fk_Genero_name)
	REFERENCES Genero(name)
	ON DELETE RESTRICT;

ALTER TABLE Possui ADD CONSTRAINT FK_Possui_2
	FOREIGN KEY (fk_Filme_rotten_tomatoes_link)
	REFERENCES Filme(rotten_tomatoes_link)
	ON DELETE RESTRICT;
