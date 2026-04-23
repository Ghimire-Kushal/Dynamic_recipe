-- =========================================
-- DATABASE: dynamic_recipe
-- =========================================

-- USERS TABLE
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(120) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- RECIPES TABLE
CREATE TABLE IF NOT EXISTS recipes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  ingredients TEXT NOT NULL,
  steps TEXT NOT NULL,
  category VARCHAR(50) DEFAULT NULL,
  image VARCHAR(255) DEFAULT NULL,
  author_id INT DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_recipes_user
    FOREIGN KEY (author_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);

-- COMMENTS TABLE
CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  recipe_id INT NOT NULL,
  user_id INT NOT NULL,
  comment TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_comments_recipe
    FOREIGN KEY (recipe_id)
    REFERENCES recipes(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_comments_user
    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);

-- DEFAULT ADMIN USER
INSERT INTO users (username, email, password, role)
VALUES (
  'admin',
  'admin@example.com',
  '$2y$10$6n8JxqN0tQGfCq0n3S5l6e7oC3mQk3gBq9q0o8d3bS9l9q9o2Gqtu',
  'admin'
);
