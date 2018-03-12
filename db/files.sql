CREATE TABLE files
(
    id          INT NOT NULL AUTO_INCREMENT,
    path        VARCHAR(255) NOT NULL,
    name        VARCHAR(255) NOT NULL,
    type        VARCHAR(255) NOT NULL,
    comment_id  INT NOT NULL,
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    FOREIGN KEY (comment_id) REFERENCES comments(id),
    PRIMARY KEY (id)
);