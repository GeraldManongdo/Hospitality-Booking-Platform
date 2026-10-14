-- Apply only to an existing database created before coordinates were added to schema.sql.
USE hospitality_booking_platform;

ALTER TABLE properties
    ADD COLUMN latitude DECIMAL(10, 7) NULL AFTER description,
    ADD COLUMN longitude DECIMAL(10, 7) NULL AFTER latitude;
