create schema zadmin_hackthon;
use zadmin_hackthon;

create table turma(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(80) NOT NULL
);

create table curso(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(80) NOT NULL
);

create table usuarios(
	id INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    ultimo_nome VARCHAR(50) NOT NULL,
    email VARCHAR(80) NOT NULL,
    senha char(64) NOT NULL,
    pergunta VARCHAR(200) NOT NULL,
	resposta VARCHAR(200) NOT NULL,
    turma VARCHAR(20) NOT NULL,
    ano INTEGER NOT NULL,
    id_turma INTEGER,
    id_curso INTEGER
);