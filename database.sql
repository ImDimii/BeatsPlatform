-- Creazione del database
CREATE DATABASE IF NOT EXISTS beats_db;
USE beats_db;

-- Tabella users
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella tracks
CREATE TABLE IF NOT EXISTS tracks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    artist VARCHAR(255) NOT NULL,
    bpm INT NOT NULL,
    key_signature VARCHAR(50) NOT NULL,
    genre VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    cover_image VARCHAR(255),
    plays INT DEFAULT 0,
    description TEXT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabella comments
CREATE TABLE IF NOT EXISTS comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    track_id INT NOT NULL,
    user_name VARCHAR(50) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE
);

-- Tabella reactions
CREATE TABLE IF NOT EXISTS reactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    track_id INT NOT NULL,
    user_name VARCHAR(50) NOT NULL,
    reaction_type ENUM('like', 'dislike') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (track_id) REFERENCES tracks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_reaction (track_id, user_name)
);

CREATE TABLE IF NOT EXISTS site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(50) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabella per i messaggi di contatto
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserimento delle impostazioni di default
INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_title', 'Beats'),
('contact_email', 'info@example.com'),
('contact_phone', '+39 123 456 7890'),
('contact_address', 'Milano, Italia'),
('social_instagram', '#'),
('social_facebook', '#'),
('social_youtube', '#'),
('social_soundcloud', '#'); 

-- Inserisco un utente admin di default (password: admin123)
INSERT INTO users (username, password) VALUES ('dimi', 'Syri777.'); 