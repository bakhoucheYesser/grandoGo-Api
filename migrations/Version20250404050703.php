<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404050703 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE addresses (id BIGSERIAL NOT NULL, customer_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, address_type VARCHAR(20) DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, street_address VARCHAR(255) NOT NULL, apartment_unit VARCHAR(50) DEFAULT NULL, city VARCHAR(100) NOT NULL, state VARCHAR(100) NOT NULL, postal_code VARCHAR(20) NOT NULL, country VARCHAR(100) NOT NULL, latitude NUMERIC(10, 8) NOT NULL, longitude NUMERIC(11, 8) NOT NULL, is_default BOOLEAN NOT NULL, delivery_instructions TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_6FCA7516D17F50A6 ON addresses (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6FCA75169395C3F3 ON addresses (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN addresses.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking_items (id BIGSERIAL NOT NULL, booking_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, category VARCHAR(100) DEFAULT NULL, quantity INT NOT NULL, weight NUMERIC(10, 2) DEFAULT NULL, weight_unit VARCHAR(20) DEFAULT NULL, length NUMERIC(10, 2) DEFAULT NULL, width NUMERIC(10, 2) DEFAULT NULL, height NUMERIC(10, 2) DEFAULT NULL, dimension_unit VARCHAR(20) DEFAULT NULL, description TEXT DEFAULT NULL, requires_special_handling BOOLEAN NOT NULL, special_handling_instructions TEXT DEFAULT NULL, is_fragile BOOLEAN NOT NULL, image_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_706214F8D17F50A6 ON booking_items (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_706214F83301C60 ON booking_items (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN booking_items.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking_status_history (id BIGSERIAL NOT NULL, booking_id BIGINT NOT NULL, updated_by BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, status VARCHAR(50) NOT NULL, previous_status VARCHAR(50) DEFAULT NULL, notes TEXT DEFAULT NULL, updated_via VARCHAR(20) DEFAULT NULL, metadata JSON DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_B405FC3ED17F50A6 ON booking_status_history (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B405FC3E3301C60 ON booking_status_history (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B405FC3E16FE72E1 ON booking_status_history (updated_by)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN booking_status_history.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE bookings (id BIGSERIAL NOT NULL, customer_id BIGINT NOT NULL, provider_id BIGINT DEFAULT NULL, vehicle_id BIGINT DEFAULT NULL, vehicle_type_id BIGINT NOT NULL, pickup_address_id BIGINT NOT NULL, delivery_address_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, scheduled_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, actual_pickup_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, actual_delivery_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, items_description TEXT DEFAULT NULL, distance NUMERIC(10, 2) NOT NULL, estimated_duration INT NOT NULL, base_fare NUMERIC(10, 2) NOT NULL, labor_fee NUMERIC(10, 2) NOT NULL, mileage_fee NUMERIC(10, 2) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, status VARCHAR(50) NOT NULL, cancellation_reason VARCHAR(255) DEFAULT NULL, customer_cancelled BOOLEAN NOT NULL, provider_cancelled BOOLEAN NOT NULL, system_cancelled BOOLEAN NOT NULL, payment_intent_id VARCHAR(100) DEFAULT NULL, payment_status VARCHAR(50) DEFAULT NULL, is_reviewed BOOLEAN NOT NULL, commission_rate NUMERIC(5, 2) DEFAULT NULL, commission_amount NUMERIC(10, 2) DEFAULT NULL, provider_payout NUMERIC(10, 2) DEFAULT NULL, route_polyline JSON DEFAULT NULL, provider_alternatives JSON DEFAULT NULL, is_customer_selected BOOLEAN NOT NULL, is_premium_selection BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7A853C35D17F50A6 ON bookings (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C359395C3F3 ON bookings (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C35A53A8AA ON bookings (provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C35545317D1 ON bookings (vehicle_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C35DA3FD1FC ON bookings (vehicle_type_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C35A72D874B ON bookings (pickup_address_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7A853C35EBF23851 ON bookings (delivery_address_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN bookings.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE customers (id BIGSERIAL NOT NULL, user_id BIGINT NOT NULL, default_address_id BIGINT DEFAULT NULL, referred_by BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, saved_card_token VARCHAR(255) DEFAULT NULL, payment_method_id VARCHAR(100) DEFAULT NULL, stripe_customer_id VARCHAR(100) DEFAULT NULL, referral_code VARCHAR(20) DEFAULT NULL, total_bookings INT NOT NULL, completed_bookings INT NOT NULL, cancelled_bookings INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_62534E21D17F50A6 ON customers (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_62534E216447454A ON customers (referral_code)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_62534E21A76ED395 ON customers (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62534E21BD94FB16 ON customers (default_address_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62534E218C0C9F8A ON customers (referred_by)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN customers.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE provider_availabilities (id BIGSERIAL NOT NULL, provider_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, day_of_week VARCHAR(20) NOT NULL, start_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, end_time TIME(0) WITHOUT TIME ZONE DEFAULT NULL, is_available BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8F218E47D17F50A6 ON provider_availabilities (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8F218E47A53A8AA ON provider_availabilities (provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_PROVIDER_SCHEDULE ON provider_availabilities (provider_id, day_of_week)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN provider_availabilities.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE provider_reviews (id BIGSERIAL NOT NULL, provider_id BIGINT NOT NULL, customer_id BIGINT NOT NULL, booking_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, rating INT NOT NULL, review_text TEXT DEFAULT NULL, response_text TEXT DEFAULT NULL, response_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_flagged BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_992849A8D17F50A6 ON provider_reviews (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_992849A8A53A8AA ON provider_reviews (provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_992849A89395C3F3 ON provider_reviews (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_992849A83301C60 ON provider_reviews (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_REVIEW_BOOKING ON provider_reviews (booking_id, customer_id, provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN provider_reviews.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE provider_service_areas (id BIGSERIAL NOT NULL, provider_id BIGINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, boundaries JSON DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4441E686D17F50A6 ON provider_service_areas (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4441E686A53A8AA ON provider_service_areas (provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN provider_service_areas.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE provider_vehicles (id BIGSERIAL NOT NULL, provider_id BIGINT NOT NULL, vehicle_type_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, make VARCHAR(50) NOT NULL, model VARCHAR(50) NOT NULL, year VARCHAR(20) NOT NULL, color VARCHAR(20) DEFAULT NULL, type VARCHAR(20) NOT NULL, license_plate VARCHAR(20) NOT NULL, available BOOLEAN NOT NULL, verified BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_578C0713D17F50A6 ON provider_vehicles (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_578C0713F5AA79D0 ON provider_vehicles (license_plate)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_578C0713A53A8AA ON provider_vehicles (provider_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_578C0713DA3FD1FC ON provider_vehicles (vehicle_type_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN provider_vehicles.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE providers (id BIGSERIAL NOT NULL, user_id BIGINT NOT NULL, account_manager_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, company_name VARCHAR(255) DEFAULT NULL, business_license VARCHAR(100) DEFAULT NULL, tax_id VARCHAR(50) DEFAULT NULL, stripe_connect_id VARCHAR(100) DEFAULT NULL, commission_rate NUMERIC(5, 2) NOT NULL, service_area_radius INT NOT NULL, is_available BOOLEAN NOT NULL, current_location_lat NUMERIC(10, 8) DEFAULT NULL, current_location_lng NUMERIC(11, 8) DEFAULT NULL, location_updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, rating NUMERIC(3, 2) DEFAULT NULL, review_count INT NOT NULL, acceptance_rate NUMERIC(5, 2) DEFAULT NULL, completion_rate NUMERIC(5, 2) DEFAULT NULL, account_balance NUMERIC(10, 2) NOT NULL, total_earnings NUMERIC(10, 2) NOT NULL, verification_status VARCHAR(20) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E225D417D17F50A6 ON providers (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_E225D417A76ED395 ON providers (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E225D41784A5C6C7 ON providers (account_manager_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN providers.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE transactions (id BIGSERIAL NOT NULL, customer_id BIGINT NOT NULL, booking_id BIGINT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, type VARCHAR(50) NOT NULL, amount NUMERIC(10, 2) NOT NULL, payment_method VARCHAR(100) NOT NULL, stripe_transaction_id VARCHAR(255) DEFAULT NULL, status VARCHAR(50) NOT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_EAA81A4CD17F50A6 ON transactions (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EAA81A4C9395C3F3 ON transactions (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EAA81A4C3301C60 ON transactions (booking_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN transactions.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id BIGSERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(20) NOT NULL, first_name VARCHAR(100) NOT NULL, last_name VARCHAR(100) NOT NULL, profile_image VARCHAR(255) DEFAULT NULL, user_type VARCHAR(20) NOT NULL, status VARCHAR(20) NOT NULL, last_login_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, verification_token VARCHAR(100) DEFAULT NULL, password_reset_token VARCHAR(100) DEFAULT NULL, password_reset_expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, notification_preferences JSON NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1483A5E9D17F50A6 ON users (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_USERS_EMAIL ON users (email)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN users.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE vehicle_types (id BIGSERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, version INT DEFAULT 1 NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, uuid UUID NOT NULL, name VARCHAR(100) NOT NULL, description TEXT DEFAULT NULL, base_rate NUMERIC(10, 2) DEFAULT NULL, enabled BOOLEAN NOT NULL, passenger_capacity INT NOT NULL, luggage_capacity INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3B9F09DCD17F50A6 ON vehicle_types (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN vehicle_types.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses ADD CONSTRAINT FK_6FCA75169395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_items ADD CONSTRAINT FK_706214F83301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_status_history ADD CONSTRAINT FK_B405FC3E3301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_status_history ADD CONSTRAINT FK_B405FC3E16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C359395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A53A8AA FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35545317D1 FOREIGN KEY (vehicle_id) REFERENCES provider_vehicles (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35DA3FD1FC FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35A72D874B FOREIGN KEY (pickup_address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings ADD CONSTRAINT FK_7A853C35EBF23851 FOREIGN KEY (delivery_address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers ADD CONSTRAINT FK_62534E21A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers ADD CONSTRAINT FK_62534E21BD94FB16 FOREIGN KEY (default_address_id) REFERENCES addresses (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers ADD CONSTRAINT FK_62534E218C0C9F8A FOREIGN KEY (referred_by) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_availabilities ADD CONSTRAINT FK_8F218E47A53A8AA FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews ADD CONSTRAINT FK_992849A8A53A8AA FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews ADD CONSTRAINT FK_992849A89395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews ADD CONSTRAINT FK_992849A83301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_service_areas ADD CONSTRAINT FK_4441E686A53A8AA FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_vehicles ADD CONSTRAINT FK_578C0713A53A8AA FOREIGN KEY (provider_id) REFERENCES providers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_vehicles ADD CONSTRAINT FK_578C0713DA3FD1FC FOREIGN KEY (vehicle_type_id) REFERENCES vehicle_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE providers ADD CONSTRAINT FK_E225D417A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE providers ADD CONSTRAINT FK_E225D41784A5C6C7 FOREIGN KEY (account_manager_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C9395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions ADD CONSTRAINT FK_EAA81A4C3301C60 FOREIGN KEY (booking_id) REFERENCES bookings (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE addresses DROP CONSTRAINT FK_6FCA75169395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_items DROP CONSTRAINT FK_706214F83301C60
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_status_history DROP CONSTRAINT FK_B405FC3E3301C60
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking_status_history DROP CONSTRAINT FK_B405FC3E16FE72E1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C359395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35A53A8AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35545317D1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35DA3FD1FC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35A72D874B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bookings DROP CONSTRAINT FK_7A853C35EBF23851
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers DROP CONSTRAINT FK_62534E21A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers DROP CONSTRAINT FK_62534E21BD94FB16
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE customers DROP CONSTRAINT FK_62534E218C0C9F8A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_availabilities DROP CONSTRAINT FK_8F218E47A53A8AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews DROP CONSTRAINT FK_992849A8A53A8AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews DROP CONSTRAINT FK_992849A89395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_reviews DROP CONSTRAINT FK_992849A83301C60
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_service_areas DROP CONSTRAINT FK_4441E686A53A8AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_vehicles DROP CONSTRAINT FK_578C0713A53A8AA
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE provider_vehicles DROP CONSTRAINT FK_578C0713DA3FD1FC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE providers DROP CONSTRAINT FK_E225D417A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE providers DROP CONSTRAINT FK_E225D41784A5C6C7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4C9395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE transactions DROP CONSTRAINT FK_EAA81A4C3301C60
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE addresses
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking_items
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking_status_history
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE bookings
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE provider_availabilities
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE provider_reviews
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE provider_service_areas
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE provider_vehicles
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE providers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE transactions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE vehicle_types
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
