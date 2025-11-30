<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Configuration Module Dietetic
 *
 * @author Eric Gilles SAGNA
 * @website https://maestrodan.art
 */

// ==================== MODE DEBUG ====================

/**
 * Mode debug du module Dietetic
 *
 * - true: Active tous les logs de debug dans activity log
 * - false: Désactive les logs de debug (RECOMMANDÉ EN PRODUCTION)
 *
 * Valeurs possibles:
 * - true: Activation manuelle
 * - false: Désactivation manuelle
 * - null: Auto (basé sur ENVIRONMENT)
 */
if (!defined('DIETETIC_DEBUG')) {
    // Auto-détection basée sur l'environnement Perfex/CodeIgniter
    if (defined('ENVIRONMENT')) {
        define('DIETETIC_DEBUG', ENVIRONMENT === 'development');
    } else {
        // Par défaut: DÉSACTIVÉ en production
        define('DIETETIC_DEBUG', false);
    }
}

// ==================== CONFIGURATION CACHE ====================

/**
 * Durée de cache pour les données statiques (en secondes)
 *
 * Données cachées:
 * - Liste des aliments (tbldietic_foods)
 * - Liste des activités (tbldietic_activities)
 * - Définitions des badges (tbldietic_badge_definitions)
 * - Paramètres du module (tbldietic_settings)
 */
if (!defined('DIETETIC_CACHE_TTL')) {
    define('DIETETIC_CACHE_TTL', 3600); // 1 heure
}

/**
 * Activer/désactiver le système de cache
 */
if (!defined('DIETETIC_CACHE_ENABLED')) {
    define('DIETETIC_CACHE_ENABLED', true);
}

// ==================== LIMITES & QUOTAS ====================

/**
 * Nombre maximum de photos par enquête alimentaire
 */
if (!defined('DIETETIC_MAX_PHOTOS_PER_SURVEY')) {
    define('DIETETIC_MAX_PHOTOS_PER_SURVEY', 10);
}

/**
 * Taille maximale des uploads (en Mo)
 */
if (!defined('DIETETIC_MAX_UPLOAD_SIZE_MB')) {
    define('DIETETIC_MAX_UPLOAD_SIZE_MB', 5);
}

/**
 * Types MIME autorisés pour les uploads
 */
if (!defined('DIETETIC_ALLOWED_UPLOAD_MIMES')) {
    define('DIETETIC_ALLOWED_UPLOAD_MIMES', 'jpg,jpeg,png,gif,pdf,doc,docx');
}

// ==================== GAMIFICATION ====================

/**
 * Activer/désactiver le système de gamification
 */
if (!defined('DIETETIC_GAMIFICATION_ENABLED')) {
    define('DIETETIC_GAMIFICATION_ENABLED', true);
}

/**
 * Points par action
 */
if (!defined('DIETETIC_POINTS_DAILY_CHECKIN')) {
    define('DIETETIC_POINTS_DAILY_CHECKIN', 10);
}

if (!defined('DIETETIC_POINTS_MEAL_LOGGED')) {
    define('DIETETIC_POINTS_MEAL_LOGGED', 5);
}

if (!defined('DIETETIC_POINTS_WATER_LOGGED')) {
    define('DIETETIC_POINTS_WATER_LOGGED', 3);
}

if (!defined('DIETETIC_POINTS_WEIGH_IN')) {
    define('DIETETIC_POINTS_WEIGH_IN', 20);
}

if (!defined('DIETETIC_POINTS_ACTIVITY')) {
    define('DIETETIC_POINTS_ACTIVITY', 15);
}

// ==================== NOTIFICATIONS ====================

/**
 * Nombre maximum de notifications par patient par jour
 */
if (!defined('DIETETIC_MAX_NOTIFICATIONS_PER_DAY')) {
    define('DIETETIC_MAX_NOTIFICATIONS_PER_DAY', 10);
}

/**
 * Durée de rétention des logs de notifications (en jours)
 */
if (!defined('DIETETIC_NOTIFICATION_LOGS_RETENTION_DAYS')) {
    define('DIETETIC_NOTIFICATION_LOGS_RETENTION_DAYS', 30);
}
