CREATE TABLE pages
(
    id          INT NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    title       VARCHAR(255) NOT NULL UNIQUE,
    body        TEXT,
    created     DATETIME NOT NULL,
    updated     DATETIME NOT NULL,
    PRIMARY KEY (id)
);