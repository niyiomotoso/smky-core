<?php

$query = "
CREATE TABLE IF NOT EXISTS defaultbios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    data TEXT NOT NULL,
    name VARCHAR(50),
    description VARCHAR(50),
    category VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1
); 
";
query($query);
