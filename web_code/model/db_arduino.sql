DROP DATABASE IF EXISTS db_arduino;
CREATE DATABASE db_arduino;

USE db_arduino;

CREATE TABLE t_switch (
    idSwitch int(11) NOT NULL AUTO_INCREMENT,
    swiValue int(2) NOT NULL,
    PRIMARY KEY (idSwitch)
);

INSERT INTO `t_switch` (`idSwitch`, `swiValue`) VALUES
(1,1);
