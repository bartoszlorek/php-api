CREATE TABLE users
(
    id          INT NOT NULL AUTO_INCREMENT,
    email       VARCHAR(255) NOT NULL UNIQUE,
    username    VARCHAR(255) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    role        VARCHAR(255) NOT NULL DEFAULT "user",
    token       VARCHAR(255),
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    PRIMARY KEY (id)
);