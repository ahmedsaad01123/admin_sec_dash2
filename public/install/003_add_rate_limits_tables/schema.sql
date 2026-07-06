CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `ip_address` VARCHAR(45) NOT NULL COMMENT 'بيدعم IPv4 و IPv6',
    `identifier` VARCHAR(191) NOT NULL COMMENT 'يوزرنيم أو إيميل أو أي معرّف تاني',

    `attempt_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `window_start` DATETIME NOT NULL,
    `last_attempt` DATETIME NOT NULL,

    `is_locked` TINYINT(1) NOT NULL DEFAULT 0,
    `locked_until` DATETIME NULL DEFAULT NULL,

    PRIMARY KEY (`id`),

    UNIQUE KEY `rate_limits_ip_identifier_unique` (`ip_address`, `identifier`),
    INDEX `rate_limits_locked_until_index` (`locked_until`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `rate_limit_violations` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

    `ip_address` VARCHAR(45) NOT NULL,
    `identifier` VARCHAR(191) NOT NULL,
    `attempt_count` INT UNSIGNED NOT NULL DEFAULT 0,
    `violation_type` VARCHAR(50) NOT NULL DEFAULT 'login',
    `user_agent` TEXT NULL DEFAULT NULL,

    `violation_time` DATETIME NOT NULL,

    PRIMARY KEY (`id`),

    INDEX `rate_limit_violations_ip_index` (`ip_address`),
    INDEX `rate_limit_violations_time_index` (`violation_time`)

) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_unicode_ci;
