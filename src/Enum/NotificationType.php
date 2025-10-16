<?php

namespace App\Enum;

/**
 * Enum NotificationType
 *
 * Represents all possible Notification type
 *
 * This enum centralizes notification usage across the application.
 * Add new token types here as needed (Welcome, information...).
 */
enum NotificationType: string
{
    /** Notification simple */
    case SIMPLE = 'simple';
    /** Notification needing more attention */
    case WARNING = 'warning';
    /** Notification very important */
    case ALERT = 'alert';

}
