CREATE TABLE product_ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),  -- Assumes you have a users table
    FOREIGN KEY (product_id) REFERENCES products(id)  -- Assumes you have a products table
);