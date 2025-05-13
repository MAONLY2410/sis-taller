use ventas;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE Usuario(
    id int NOT NULL,
    id_usuario varchar(15) NOT NULL,
    correo varchar(100) NOT NULL,
    rol int NOT NULL,
    pasword varchar(255) character set utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
    ultimo_Acceso datetime DEFAULT NULL,
    tkR varchar(255) DEFAULT NULL
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

ALTER TABLE 'Usuario'
    ADD PRIMARY KEY ('id'),
    ADD UNIQUE KEY 'idx_Usuario' ('id_usuario');

ALTER TABLE 'Usuario'
    MODIFY 'id' int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT;