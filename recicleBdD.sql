CREATE TABLE usuarios(
id SERIAL PRIMARY KEY,
email VARCHAR(50) UNIQUE NOT NULL,
senha VARCHAR(32) NOT NULL,
tipo_usuario VARCHAR(10),
data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE catadores(
id SERIAL PRIMARY KEY,
usuario_id INT UNIQUE NOT NULL,
nome_completo VARCHAR(80),
cpf CHAR(11) UNIQUE NOT NULL,
foto_perfil VARCHAR(255),
telefone VARCHAR(20),
tipo_material VARCHAR(10),
descricao TEXT,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE empresas(
id SERIAL PRIMARY KEY,
usuario_id INT UNIQUE NOT NULL,
nome_empresa VARCHAR(100),
cnpj CHAR(14) UNIQUE NOT NULL,
endereco VARCHAR(150),
foto_empresa VARCHAR(255),
descricao TEXT,
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

INSERT INTO usuarios(email, senha, tipo_usuario)
VALUES('thallys@gmail.com', '1234565', 'catador'),
('whiteltda@gmail.com', 'whi2324', 'empresa');

INSERT INTO catadores(usuario_id, nome_completo, cpf, foto_perfil, telefone, tipo_material, descricao)
VALUES('1','Cicero Thallys Ferreira de Oliveira', '12345678914', 'uploads/joao.png', '88988017654', 'plastico', 'coletor de reciclaveis');

INSERT INTO empresas(usuario_id, nome_empresa, cnpj, endereco, foto_empresa, descricao)
VALUES('2', 'WHITE LTDA', '12345678000199', 'Rua Central 200', 'uploads/empresa.png', 'Empresa de Jogos');

SELECT * FROM usuarios;
