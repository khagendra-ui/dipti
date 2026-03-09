-- CareOnCall Database Schema
-- Create Database
CREATE DATABASE IF NOT EXISTS careoncall;
USE careoncall;

-- Users Table (for all user types)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    user_type ENUM('client', 'caretaker', 'admin') NOT NULL,
    profile_picture VARCHAR(255),
    status ENUM('active', 'inactive', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (email),
    INDEX (user_type)
);

-- Caretaker Details Table
CREATE TABLE IF NOT EXISTS caretaker_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    experience_years INT,
    skills VARCHAR(255),
    bio TEXT,
    hourly_rate DECIMAL(10, 2),
    certification_document VARCHAR(255),
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verification_date DATETIME,
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (verification_status)
);

-- Caretaker Availability Table
CREATE TABLE IF NOT EXISTS caretaker_availability (
    id INT PRIMARY KEY AUTO_INCREMENT,
    caretaker_id INT NOT NULL,
    day_of_week ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (caretaker_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_availability (caretaker_id, day_of_week)
);

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_id INT NOT NULL,
    caretaker_id INT NOT NULL,
    booking_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    location TEXT NOT NULL,
    service_type VARCHAR(100),
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    special_requirements TEXT,
    total_hours INT,
    total_cost DECIMAL(10, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (caretaker_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (client_id),
    INDEX (caretaker_id),
    INDEX (booking_date),
    INDEX (status)
);

-- Booking Requests Table (for caretaker approval workflow)
CREATE TABLE IF NOT EXISTS booking_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    caretaker_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    caretaker_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at DATETIME,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (caretaker_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX (status)
);

-- Reviews/Feedback Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    reviewee_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (booking_id, reviewer_id)
);

-- Admin Activity Log Table
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_user_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (target_user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (created_at)
);

-- Insert default admin user (password: admin@123)
INSERT INTO users (name, email, password, user_type, status) 
VALUES ('Admin', 'admin@careoncall.com', '$2y$10$abcdefghijklmnopqrstuvwxyz', 'admin', 'active')
ON DUPLICATE KEY UPDATE id=id;
