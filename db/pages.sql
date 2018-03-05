CREATE TABLE pages
(
    id          INT NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(255) NOT NULL UNIQUE,
    title       VARCHAR(255) NOT NULL UNIQUE,
    body        TEXT,
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    PRIMARY KEY (id)
);