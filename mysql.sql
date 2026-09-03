-- =========================================================
-- NAVA FADE STUDIO DATABASE
-- =========================================================

CREATE DATABASE IF NOT EXISTS nava_fade_studio
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nava_fade_studio;


-- =========================================================
-- SERVICES TABLE
-- =========================================================

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    description TEXT NOT NULL,

    price DECIMAL(10,2) NOT NULL,

    duration VARCHAR(50) NOT NULL,

    image VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- =========================================================
-- NAVA FADE STUDIO SERVICES
-- =========================================================

INSERT INTO services
(name, description, price, duration, image)
VALUES

(
    'Hair Treatment',
    'Nourishes and revitalizes your hair for a healthier, refreshed look.',
    800.00,
    '30 to 60 minutes',
    'hair-treatment.png'
),

(
    'Hair Cut',
    'Get a clean and stylish haircut customized to your preferred look.',
    100.00,
    '30 minutes',
    'hair-cut.png'
),

(
    'Facial & Wash',
    'A refreshing facial and wash treatment for a clean and refreshed appearance.',
    550.00,
    '30 to 60 minutes',
    'facial-wash.png'
),

(
    'Hot Towel Treatment',
    'Relax with a warm towel treatment designed to refresh and soothe.',
    150.00,
    '15 minutes',
    'hot-towel.png'
),

(
    'Beard Trimming',
    'Professional beard trimming and shaping for a clean and polished look.',
    150.00,
    '15 to 30 minutes',
    'beard-trimming.png'
),

(
    'Hair Wash',
    'A refreshing hair wash that leaves your hair clean and revitalized.',
    50.00,
    '5 to 15 minutes',
    'hair-wash.png'
),

(
    'Clean Shave',
    'Enjoy a smooth and professional clean shave.',
    250.00,
    '20 to 30 minutes',
    'clean-shave.png'
),

(
    'Hair Styling',
    'Professional hair styling customized to your preferred look.',
    300.00,
    '30 minutes',
    'hair-styling.png'
);


-- =========================================================
-- CHECK DATABASE
-- =========================================================

SELECT * FROM services;


CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    contact_number VARCHAR(20) NOT NULL,
    service VARCHAR(100) NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);