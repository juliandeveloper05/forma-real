-- =====================================================
-- PROYECTO: FORMA REAL
-- BASE DE DATOS: MySQL 8.0
-- =====================================================

-- =====================================================
-- TABLA: fr_forums (Categorías principales)
-- =====================================================
CREATE TABLE fr_forums (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    color VARCHAR(7) DEFAULT '#3b82f6',
    parent_id BIGINT UNSIGNED NULL,
    display_order INT DEFAULT 0,
    topic_count INT DEFAULT 0,
    reply_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_active (is_active),
    FOREIGN KEY (parent_id) REFERENCES fr_forums(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_topics (Temas del foro)
-- =====================================================
CREATE TABLE fr_topics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    forum_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content LONGTEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'approved',
    is_sticky TINYINT(1) DEFAULT 0,
    is_closed TINYINT(1) DEFAULT 0,
    view_count INT DEFAULT 0,
    reply_count INT DEFAULT 0,
    last_reply_id BIGINT UNSIGNED NULL,
    last_active_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_forum (forum_id),
    INDEX idx_user (user_id),
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_sticky (is_sticky),
    INDEX idx_last_active (last_active_time),
    FULLTEXT INDEX idx_fulltext (title, content),
    
    FOREIGN KEY (forum_id) REFERENCES fr_forums(id) ON DELETE CASCADE,
    -- NOTA: wp_users se asume existente en la instalación de WordPress
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_replies (Respuestas)
-- =====================================================
CREATE TABLE fr_replies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    content LONGTEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam', 'trash') DEFAULT 'approved',
    is_solution TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_topic (topic_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    FULLTEXT INDEX idx_content (content),
    
    FOREIGN KEY (topic_id) REFERENCES fr_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES fr_replies(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_user_profiles (Perfiles extendidos)
-- =====================================================
CREATE TABLE fr_user_profiles (
    user_id BIGINT UNSIGNED PRIMARY KEY,
    bio TEXT,
    fitness_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    fitness_goals TEXT,
    topic_count INT DEFAULT 0,
    reply_count INT DEFAULT 0,
    helpful_count INT DEFAULT 0,
    last_seen DATETIME DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_reports (Reportes de moderación)
-- =====================================================
CREATE TABLE fr_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reporter_id BIGINT UNSIGNED NOT NULL,
    reported_user_id BIGINT UNSIGNED NOT NULL,
    content_type ENUM('topic', 'reply'),
    content_id BIGINT UNSIGNED NOT NULL,
    reason ENUM('spam', 'offensive', 'misinformation', 'other'),
    description TEXT,
    status ENUM('pending', 'reviewed', 'dismissed') DEFAULT 'pending',
    reviewed_by BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_reporter (reporter_id),
    INDEX idx_reported (reported_user_id),
    INDEX idx_status (status),
    INDEX idx_content (content_type, content_id),
    
    FOREIGN KEY (reporter_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
    FOREIGN KEY (reported_user_id) REFERENCES wp_users(ID) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES wp_users(ID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_notifications (Notificaciones)
-- =====================================================
CREATE TABLE fr_notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('reply', 'mention', 'moderation', 'system'),
    content TEXT NOT NULL,
    link VARCHAR(500),
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at),
    
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_topic_views (Tracking de vistas)
-- =====================================================
CREATE TABLE fr_topic_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_topic (topic_id),
    INDEX idx_user (user_id),
    INDEX idx_viewed (viewed_at),
    
    FOREIGN KEY (topic_id) REFERENCES fr_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLA: fr_user_activity (Log de actividad)
-- =====================================================
CREATE TABLE fr_user_activity (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action_type ENUM('topic_created', 'reply_posted', 'topic_edited', 'reply_edited', 'login'),
    content_type VARCHAR(50),
    content_id BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user (user_id),
    INDEX idx_action (action_type),
    INDEX idx_created (created_at),
    
    FOREIGN KEY (user_id) REFERENCES wp_users(ID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
