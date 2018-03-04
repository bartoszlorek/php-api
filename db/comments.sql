CREATE TABLE comments
(
    id          INT NOT NULL AUTO_INCREMENT,
    body        TEXT,
    page_id     INT NOT NULL,
    user_id     INT NOT NULL,
    created     DATETIME NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (page_id) REFERENCES pages(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);