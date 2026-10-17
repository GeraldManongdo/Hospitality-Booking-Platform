ALTER TABLE payments
    ADD COLUMN provider_checkout_url varchar(500) DEFAULT NULL AFTER provider_transaction_id;
