use taller;

DELIMITER $$
DROP PROCEDURE IF EXISTS eliminarCliente$$
CREATE PROCEDURE eliminarCliente (_id INT(1))
begin
    declare _cant int;
    declare _resp int;
    set _resp = 0;
    select count(id) into _cant from cliente where id = _id;
    if _cant > 0 then
        set _resp = 1;
        select count(id) into _cant from artefacto where idCliente = _id;
        if _cant = 0 then
            delete from cliente where id = _id;
        else 
            -- select 2 into _resp;
            set _resp = 2;
        end if;
    end if;
    select _resp as resp;
end$$

DELIMITER ;


DELIMITER $$ 
CREATE TRIGGER eliminar_cliente AFTER DELETE ON cliente FOR EACH ROW
BEGIN

 DELETE FROM usuario WHERE usuario.idUsuario = OLD.id;

END$$
DELIMITER ;


DELIMITER $$ --
DROP PROCEDURE IF EXISTS IniciarSesion$$
CREATE PROCEDURE IniciarSesion(_id int, _passw varchar(255))
BEGIN
    select idUsuario, rol from usuario where id = _id and passw = _passw;
END$$

DELIMITER ;


DROP FUNCTION IF EXISTS modificarToken;
DROP PROCEDURE IF EXISTS verificarTokenR;

DELIMITER $$

CREATE FUNCTION modificarToken (_idUsuario VARCHAR(15), _tkR varchar(255)) RETURNS INT(1)
READS SQL DATA DETERMINISTIC
begin
    declare _cant int;
    select count(idUsuario) into _cant from usuario where idUsuario = _idUsuario;
    if _cant > 0 then
        update usuario set
                tkR = _tkR
                where idUsuario = _idUsuario;
        if _tkR <> "" then
            update usuario set
                ultimoAcceso = now()
                where idUsuario = _idUsuario;
        end if;
    end if;
    return _cant;
end$$

CREATE PROCEDURE verificarTokenR (_idUsuario VARCHAR(15), _tkR varchar(255))
begin
    select rol from usuario where idUsuario = _idUsuario and tkR = _tkR;
end$$

DELIMITER ;