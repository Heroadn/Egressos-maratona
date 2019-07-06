create schema zadmin_hackathon;
use zadmin_hackathon;

create table campus(
	id_campus INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(80) NOT NULL
);

create table curso(
	id_curso INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_campus INTEGER NOT NULL,
    curso VARCHAR(80) NOT NULL,
    FOREIGN KEY (id_campus) REFERENCES campus(id_campus)
);

create table turma(
	id_turma INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_curso INTEGER NOT NULL,
    turma VARCHAR(80) NOT NULL,
    FOREIGN KEY (id_curso) REFERENCES curso(id_curso)
);

create table grupo(
	id_grupo INTEGER PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(80) NOT NULL,
    ano INTEGER NOT NULL,
    descricao VARCHAR(300) NOT NULL,
    id_status INTEGER NOT NULL
);

create table usuario(
	id_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
    id_tipo_usuario INTEGER NOT NULL,
    nome VARCHAR(50) NOT NULL,
    ultimo_nome VARCHAR(50) NOT NULL,
    email VARCHAR(80) NOT NULL,
    senha char(64) NOT NULL,
    pergunta VARCHAR(200) NOT NULL,
	resposta VARCHAR(200) NOT NULL,
    descricao VARCHAR(200) NOT NULL,
    id_status INTEGER NOT NULL,
    ano_egresso INTEGER NOT NULL,
    nome_completo VARCHAR(100) NOT NULL,
    token VARCHAR(100) NOT NULL,
    id_turma INTEGER NOT NULL,
    id_grupo INTEGER NOT NULL,
    FOREIGN KEY (id_turma) REFERENCES turma(id_turma),
    FOREIGN KEY (id_grupo) REFERENCES grupo(id_grupo)
);

create table midia(
	id_midia INTEGER PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(80) NOT NULL,
    file_size INTEGER NOT NULL,
    data_insercao DATETIME NOT NULL,
    status_id_status INTEGER
);

create table midia_usuario(
	id_midia_usuario INTEGER PRIMARY KEY AUTO_INCREMENT,
	data_alteracao DATETIME NOT NULL,
	midia_file_ID INTEGER NOT NULL,
    usuario_id_usuario INTEGER NOT NULL,
    FOREIGN KEY (midia_file_ID) REFERENCES midia(id_midia),
    FOREIGN KEY (usuario_id_usuario) REFERENCES usuario(id_usuario)
);

create table midia_grupo(
	id_midia_grupo INTEGER PRIMARY KEY AUTO_INCREMENT,
	data_alteracao DATETIME NOT NULL,
	midia_file_ID INTEGER NOT NULL,
    grupo_id_grupo INTEGER NOT NULL,
    FOREIGN KEY (midia_file_ID) REFERENCES midia(id_midia),
    FOREIGN KEY (grupo_id_grupo) REFERENCES grupo(id_grupo)
);

INSERT INTO campus(nome) VALUES ('Campus1'), ('ECampus2'), ('Campus3');
INSERT INTO curso(id_campus, curso) VALUES (1, 'Curso1'), (2, 'Curso2'),
(3, 'Curso3');
INSERT INTO turma(id_curso, turma) VALUES (1, 'Turma1'), (2, 'Turma2'), (3, 'Turma3');