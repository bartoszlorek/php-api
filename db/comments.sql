CREATE TABLE comments
(
    id          INT NOT NULL AUTO_INCREMENT,
    body        TEXT,
    user_id     INT NOT NULL,
    page_id     INT NOT NULL,
    created_at  DATETIME NOT NULL,
    updated_at  DATETIME NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (page_id) REFERENCES pages(id),
    PRIMARY KEY (id)
);