-- KSRF TBI incubatee applications
-- Import this in phpMyAdmin / Hostinger hPanel / A2 cPanel.

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS applications (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entrepreneur_name VARCHAR(200) NOT NULL,
  age TINYINT UNSIGNED NULL,
  date_of_birth DATE NULL,
  communication_address TEXT NOT NULL,
  permanent_address TEXT NOT NULL,
  phone_res VARCHAR(40) NULL,
  phone_off VARCHAR(40) NULL,
  phone_mobile VARCHAR(40) NOT NULL,
  email VARCHAR(190) NOT NULL,
  skills_experience TEXT NULL,
  type_of_business VARCHAR(200) NULL,
  organization_name VARCHAR(200) NULL,
  product_description TEXT NOT NULL,
  startup_year VARCHAR(10) NULL,
  why_entrepreneur TEXT NULL,
  legal_position VARCHAR(40) NULL,
  services_expected TEXT NULL,
  team_details TEXT NULL,
  employees_fulltime VARCHAR(20) NULL,
  employees_parttime VARCHAR(20) NULL,
  employees_consultants VARCHAR(20) NULL,
  employees_org VARCHAR(20) NULL,
  promoter_name VARCHAR(200) NULL,
  promoter_qualification VARCHAR(200) NULL,
  promoter_designation VARCHAR(200) NULL,
  promoter_experience VARCHAR(80) NULL,
  promoter_communication_address TEXT NULL,
  promoter_permanent_address TEXT NULL,
  promoter_phone_res VARCHAR(40) NULL,
  promoter_phone_off VARCHAR(40) NULL,
  promoter_phone_mobile VARCHAR(40) NULL,
  promoter_email VARCHAR(190) NULL,
  promoter_fax VARCHAR(40) NULL,
  place VARCHAR(120) NULL,
  agreed_rules TINYINT(1) NOT NULL DEFAULT 0,
  ip_address VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created (created_at),
  INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS education (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  class_course VARCHAR(160) NULL,
  college VARCHAR(200) NULL,
  branch VARCHAR(160) NULL,
  university_board VARCHAR(160) NULL,
  year_of_pass VARCHAR(20) NULL,
  percent_secured VARCHAR(20) NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT fk_edu_app FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS project_costs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  item_name VARCHAR(200) NULL,
  amount VARCHAR(40) NULL,
  sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
  CONSTRAINT fk_cost_app FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_references (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  ref_number TINYINT UNSIGNED NOT NULL,
  ref_name VARCHAR(200) NULL,
  designation VARCHAR(160) NULL,
  address TEXT NULL,
  phone VARCHAR(40) NULL,
  email VARCHAR(190) NULL,
  CONSTRAINT fk_ref_app FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS application_files (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  application_id INT UNSIGNED NOT NULL,
  kind VARCHAR(40) NOT NULL,
  original_name VARCHAR(255) NOT NULL,
  stored_name VARCHAR(255) NOT NULL,
  mime VARCHAR(120) NOT NULL,
  size_bytes INT UNSIGNED NOT NULL,
  CONSTRAINT fk_file_app FOREIGN KEY (application_id) REFERENCES applications (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
