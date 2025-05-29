create table usuario(id int auto_increment primary key, nome_completo char(100), email char(50), data_nasc date, senha char(50));

insert into usuario (nome_completo, email, data_nasc, senha) values
('Ricardo Oliveira', 'ricardo@email.com', '1987-03-25', 'ric#ardo'),
('Beatriz Souza', 'bia.souza@email.com', '2001-07-08', 'b!a2001');
