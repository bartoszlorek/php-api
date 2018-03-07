CREATE TABLE pages
(
    id          INT NOT NULL AUTO_INCREMENT,
    guid        VARCHAR(255) NOT NULL UNIQUE,
    type        VARCHAR(255) NOT NULL,
    status      VARCHAR(255) NOT NULL,
    title       VARCHAR(255) NOT NULL,
    state       TEXT,
    body        TEXT,
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    PRIMARY KEY (id)
);