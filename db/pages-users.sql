CREATE TABLE pages_users
(
    user_id     INT NOT NULL,
    page_id     INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (page_id) REFERENCES pages(id)
);