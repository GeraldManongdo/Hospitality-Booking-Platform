CREATE DATABASE IF NOT EXISTS hospitality_booking_platform
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE hospitality_booking_platform;

CREATE TABLE IF NOT EXISTS roles (
    id SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    description VARCHAR(255) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    role_id SMALLINT UNSIGNED NOT NULL,
    email VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(32) NULL,
    status ENUM('pending', 'active', 'suspended', 'disabled') NOT NULL DEFAULT 'pending',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role_status (role_id, status),
    CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles (id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS user_preferences (
    user_id BIGINT UNSIGNED NOT NULL,
    booking_updates TINYINT(1) NOT NULL DEFAULT 1,
    travel_inspiration TINYINT(1) NOT NULL DEFAULT 0,
    product_announcements TINYINT(1) NOT NULL DEFAULT 0,
    preferred_currency CHAR(3) NOT NULL DEFAULT 'USD',
    preferred_language VARCHAR(10) NOT NULL DEFAULT 'en',
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id),
    CONSTRAINT fk_user_preferences_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS destinations (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug VARCHAR(160) NOT NULL,
    name VARCHAR(160) NOT NULL,
    country VARCHAR(100) NOT NULL,
    description TEXT NULL,
    image_url VARCHAR(2048) NULL,
    status ENUM('draft', 'in_review', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_destinations_slug (slug),
    KEY idx_destinations_status_name (status, name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS properties (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    external_code VARCHAR(40) NULL,
    owner_user_id BIGINT UNSIGNED NULL,
    destination_id BIGINT UNSIGNED NOT NULL,
    slug VARCHAR(180) NOT NULL,
    name VARCHAR(180) NOT NULL,
    city VARCHAR(120) NOT NULL,
    country VARCHAR(100) NOT NULL,
    property_type VARCHAR(80) NOT NULL,
    description TEXT NOT NULL,
    latitude DECIMAL(10, 7) NULL,
    longitude DECIMAL(10, 7) NULL,
    base_nightly_rate DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('draft', 'in_review', 'published', 'rejected', 'archived') NOT NULL DEFAULT 'draft',
    is_sample TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_properties_external_code (external_code),
    UNIQUE KEY uq_properties_slug (slug),
    KEY idx_properties_destination_status (destination_id, status),
    KEY idx_properties_owner_status (owner_user_id, status),
    CONSTRAINT fk_properties_owner FOREIGN KEY (owner_user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_properties_destination FOREIGN KEY (destination_id) REFERENCES destinations (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_properties_base_rate CHECK (base_nightly_rate >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS property_images (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id BIGINT UNSIGNED NOT NULL,
    image_url VARCHAR(2048) NOT NULL,
    alt_text VARCHAR(255) NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_property_images_order (property_id, sort_order),
    CONSTRAINT fk_property_images_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS amenities (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(80) NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_amenities_name (name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS property_amenities (
    property_id BIGINT UNSIGNED NOT NULL,
    amenity_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (property_id, amenity_id),
    CONSTRAINT fk_property_amenities_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_property_amenities_amenity FOREIGN KEY (amenity_id) REFERENCES amenities (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS room_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    property_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(160) NOT NULL,
    description TEXT NULL,
    max_guests TINYINT UNSIGNED NOT NULL,
    total_units SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    nightly_rate DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('active', 'inactive', 'in_review') NOT NULL DEFAULT 'active',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_room_types_property_name (property_id, name),
    CONSTRAINT fk_room_types_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_room_types_guests CHECK (max_guests > 0),
    CONSTRAINT chk_room_types_rate CHECK (nightly_rate >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS room_inventory (
    room_type_id BIGINT UNSIGNED NOT NULL,
    inventory_date DATE NOT NULL,
    available_units SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    nightly_rate DECIMAL(10, 2) NULL,
    PRIMARY KEY (room_type_id, inventory_date),
    CONSTRAINT fk_room_inventory_room_type FOREIGN KEY (room_type_id) REFERENCES room_types (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT chk_room_inventory_rate CHECK (nightly_rate IS NULL OR nightly_rate >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS promotions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code VARCHAR(60) NOT NULL,
    description VARCHAR(255) NOT NULL,
    discount_type ENUM('percentage', 'fixed_amount') NOT NULL,
    discount_value DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NULL,
    starts_at DATETIME NULL,
    ends_at DATETIME NULL,
    max_redemptions INT UNSIGNED NULL,
    redemption_count INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('draft', 'scheduled', 'active', 'ended', 'disabled') NOT NULL DEFAULT 'draft',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_promotions_code (code),
    KEY idx_promotions_status_dates (status, starts_at, ends_at),
    CONSTRAINT chk_promotions_value CHECK (discount_value > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bookings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference VARCHAR(40) NOT NULL,
    guest_user_id BIGINT UNSIGNED NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    room_type_id BIGINT UNSIGNED NULL,
    promotion_id BIGINT UNSIGNED NULL,
    guest_first_name VARCHAR(100) NOT NULL,
    guest_last_name VARCHAR(100) NOT NULL,
    guest_email VARCHAR(254) NOT NULL,
    guest_phone VARCHAR(32) NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guest_count TINYINT UNSIGNED NOT NULL,
    room_count TINYINT UNSIGNED NOT NULL DEFAULT 1,
    nightly_rate DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    discount_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tax_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    service_fee_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    special_requests TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_bookings_reference (reference),
    KEY idx_bookings_guest_created (guest_user_id, created_at),
    KEY idx_bookings_property_dates (property_id, check_in, check_out),
    KEY idx_bookings_status_created (status, created_at),
    CONSTRAINT fk_bookings_guest FOREIGN KEY (guest_user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_bookings_room_type FOREIGN KEY (room_type_id) REFERENCES room_types (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_bookings_promotion FOREIGN KEY (promotion_id) REFERENCES promotions (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_bookings_dates CHECK (check_out > check_in),
    CONSTRAINT chk_bookings_guests CHECK (guest_count > 0 AND room_count > 0),
    CONSTRAINT chk_bookings_amounts CHECK (
        nightly_rate >= 0 AND subtotal >= 0 AND discount_amount >= 0
        AND tax_amount >= 0 AND service_fee_amount >= 0 AND total_amount >= 0
    )
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS payments (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    booking_id BIGINT UNSIGNED NOT NULL,
    reference VARCHAR(60) NOT NULL,
    payment_type ENUM('charge', 'refund') NOT NULL DEFAULT 'charge',
    provider VARCHAR(80) NULL,
    provider_transaction_id VARCHAR(190) NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'USD',
    status ENUM('pending', 'authorized', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'pending',
    processed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_payments_reference (reference),
    UNIQUE KEY uq_payments_provider_transaction (provider, provider_transaction_id),
    KEY idx_payments_booking_status (booking_id, status),
    CONSTRAINT fk_payments_booking FOREIGN KEY (booking_id) REFERENCES bookings (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_payments_amount CHECK (amount > 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wishlists (
    user_id BIGINT UNSIGNED NOT NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, property_id),
    CONSTRAINT fk_wishlists_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_wishlists_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference VARCHAR(40) NOT NULL,
    property_id BIGINT UNSIGNED NOT NULL,
    guest_user_id BIGINT UNSIGNED NULL,
    booking_id BIGINT UNSIGNED NULL,
    rating DECIMAL(2, 1) NOT NULL,
    title VARCHAR(180) NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'published', 'rejected', 'flagged') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reviews_reference (reference),
    UNIQUE KEY uq_reviews_booking (booking_id),
    KEY idx_reviews_property_status (property_id, status),
    CONSTRAINT fk_reviews_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_reviews_guest FOREIGN KEY (guest_user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_reviews_rating CHECK (rating >= 1 AND rating <= 5)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(180) NOT NULL,
    message TEXT NOT NULL,
    notification_type VARCHAR(50) NOT NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_notifications_user_created (user_id, created_at),
    KEY idx_notifications_user_read (user_id, read_at),
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS support_tickets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference VARCHAR(40) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    subject VARCHAR(180) NOT NULL,
    description TEXT NOT NULL,
    priority ENUM('low', 'normal', 'high', 'urgent') NOT NULL DEFAULT 'normal',
    status ENUM('open', 'in_progress', 'closed') NOT NULL DEFAULT 'open',
    assigned_to BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_support_tickets_reference (reference),
    KEY idx_support_tickets_status_priority (status, priority),
    CONSTRAINT fk_support_tickets_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_support_tickets_assignee FOREIGN KEY (assigned_to) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS property_staff (
    property_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    job_title VARCHAR(100) NOT NULL,
    status ENUM('pending', 'active', 'disabled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (property_id, user_id),
    KEY idx_property_staff_user_status (user_id, status),
    CONSTRAINT fk_property_staff_property FOREIGN KEY (property_id) REFERENCES properties (id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_property_staff_user FOREIGN KEY (user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS content_pages (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference VARCHAR(40) NOT NULL,
    slug VARCHAR(180) NOT NULL,
    title VARCHAR(200) NOT NULL,
    content_type VARCHAR(80) NOT NULL,
    body LONGTEXT NOT NULL,
    status ENUM('draft', 'in_review', 'scheduled', 'published', 'archived') NOT NULL DEFAULT 'draft',
    publish_at DATETIME NULL,
    author_user_id BIGINT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_content_pages_reference (reference),
    UNIQUE KEY uq_content_pages_slug (slug),
    KEY idx_content_pages_status_publish (status, publish_at),
    CONSTRAINT fk_content_pages_author FOREIGN KEY (author_user_id) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(100) NOT NULL,
    setting_value TEXT NOT NULL,
    description VARCHAR(255) NULL,
    updated_by BIGINT UNSIGNED NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (setting_key),
    CONSTRAINT fk_system_settings_user FOREIGN KEY (updated_by) REFERENCES users (id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO roles (name, description) VALUES
    ('customer', 'Guest account for searching and booking stays'),
    ('partner', 'Property owner or operator'),
    ('staff', 'Authorized property staff member'),
    ('support', 'Customer support operator'),
    ('admin', 'Platform administrator')
ON DUPLICATE KEY UPDATE description = VALUES(description);

INSERT INTO destinations (slug, name, country, description, image_url, status) VALUES
    ('lisbon-portugal', 'Lisbon', 'Portugal', 'Coastal charm, tiled streets and long, golden evenings.', 'https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=900&q=80', 'published'),
    ('kyoto-japan', 'Kyoto', 'Japan', 'Quiet gardens, timeless craft and culture around every corner.', 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=900&q=80', 'published'),
    ('amalfi-coast-italy', 'Amalfi Coast', 'Italy', 'Cliffside villages, sea air and la dolce vita.', 'https://images.unsplash.com/photo-1533587851505-d119e13fa0d7?auto=format&fit=crop&w=900&q=80', 'published'),
    ('bali-indonesia', 'Bali', 'Indonesia', 'Slow mornings, green landscapes and island spirit.', 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=900&q=80', 'published'),
    ('copenhagen-denmark', 'Copenhagen', 'Denmark', 'Waterfront living, good design and cosy neighbourhoods.', 'https://images.unsplash.com/photo-1513622470522-26c3c8a854bc?auto=format&fit=crop&w=900&q=80', 'published'),
    ('cape-town-south-africa', 'Cape Town', 'South Africa', 'A mountain-framed city with a wide-open horizon.', 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99?auto=format&fit=crop&w=900&q=80', 'published')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    country = VALUES(country),
    description = VALUES(description),
    image_url = VALUES(image_url),
    status = VALUES(status);

INSERT INTO properties (
    external_code, destination_id, slug, name, city, country, property_type,
    description, base_nightly_rate, currency, status, is_sample
) VALUES
    ('stay-101', (SELECT id FROM destinations WHERE slug = 'lisbon-portugal'), 'casa-do-mar-lisbon', 'Casa do Mar', 'Lisbon', 'Portugal', 'Boutique hotel', 'A calm, design-led hideaway close to Lisbon’s old town.', 186.00, 'USD', 'published', 1),
    ('stay-102', (SELECT id FROM destinations WHERE slug = 'kyoto-japan'), 'the-aoyama-house-kyoto', 'The Aoyama House', 'Kyoto', 'Japan', 'Ryokan', 'A quietly beautiful traditional retreat with a modern touch.', 224.00, 'USD', 'published', 1),
    ('stay-103', (SELECT id FROM destinations WHERE slug = 'amalfi-coast-italy'), 'villa-sorella-amalfi', 'Villa Sorella', 'Amalfi Coast', 'Italy', 'Villa', 'A sunlit hillside villa above the deep blue Tyrrhenian Sea.', 310.00, 'USD', 'published', 1),
    ('stay-104', (SELECT id FROM destinations WHERE slug = 'bali-indonesia'), 'sundara-garden-stay-ubud', 'Sundara Garden Stay', 'Ubud', 'Indonesia', 'Resort', 'A private, leafy sanctuary steps from Ubud’s cultural heart.', 158.00, 'USD', 'published', 1),
    ('stay-105', (SELECT id FROM destinations WHERE slug = 'copenhagen-denmark'), 'harbour-house-copenhagen', 'Harbour House', 'Copenhagen', 'Denmark', 'Design hotel', 'Thoughtful Scandinavian design by the city’s historic waterfront.', 205.00, 'USD', 'published', 1),
    ('stay-106', (SELECT id FROM destinations WHERE slug = 'cape-town-south-africa'), 'blue-hour-lodge-cape-town', 'Blue Hour Lodge', 'Cape Town', 'South Africa', 'Lodge', 'A warm and welcoming base between mountain and coastline.', 172.00, 'USD', 'published', 1)
ON DUPLICATE KEY UPDATE
    destination_id = VALUES(destination_id),
    name = VALUES(name),
    city = VALUES(city),
    country = VALUES(country),
    property_type = VALUES(property_type),
    description = VALUES(description),
    base_nightly_rate = VALUES(base_nightly_rate),
    currency = VALUES(currency),
    status = VALUES(status),
    is_sample = VALUES(is_sample);

INSERT INTO property_images (property_id, image_url, alt_text, sort_order, is_primary)
SELECT p.id, CONCAT('https://images.unsplash.com/', sample.image_id, '?auto=format&fit=crop&w=1200&q=85'),
       CONCAT('View of ', p.name), 0, 1
FROM properties AS p
JOIN (
    SELECT 'stay-101' AS external_code, 'photo-1566073771259-6a8506099945' AS image_id
    UNION ALL SELECT 'stay-102', 'photo-1542314831-068cd1dbfeeb'
    UNION ALL SELECT 'stay-103', 'photo-1613490493576-7fde63acd811'
    UNION ALL SELECT 'stay-104', 'photo-1571896349842-33c89424de2d'
    UNION ALL SELECT 'stay-105', 'photo-1445019980597-93fa8acb246c'
    UNION ALL SELECT 'stay-106', 'photo-1510798831971-661eb04b3739'
) AS sample ON sample.external_code = p.external_code
WHERE NOT EXISTS (
    SELECT 1
    FROM property_images AS existing_image
    WHERE existing_image.property_id = p.id
      AND existing_image.is_primary = 1
);
