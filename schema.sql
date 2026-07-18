-- Project 3: Database Integration
-- 1:Many relationship: one user has many tasks

CREATE DATABASE IF NOT EXISTS decodelabs_project3;
USE decodelabs_project3;

CREATE TABLE users (
    user_id     INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tasks (
    task_id     INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    title       VARCHAR(200) NOT NULL,
    status      VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_tasks_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE,
    CONSTRAINT chk_status
        CHECK (status IN ('pending', 'in_progress', 'done'))
);

-- sample seed data
INSERT INTO users (name, email) VALUES
    ('Ada Lovelace', 'ada@example.com'),
    ('Grace Hopper', 'grace@example.com');

INSERT INTO tasks (user_id, title, status) VALUES
    (1, 'Design database schema', 'done'),
    (1, 'Build PHP CRUD API', 'in_progress'),
    (2, 'Write test cases', 'pending');
