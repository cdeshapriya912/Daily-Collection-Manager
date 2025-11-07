<?php
/**
 * SMTP Configuration
 * 
 * Configure your SMTP settings here.
 * This file contains all SMTP-related settings for email sending.
 */

return [
    // SMTP Server Settings
    'host' => 'localhost',           // SMTP server hostname
    'port' => 1025,                  // SMTP server port (1025 for Mailpit)
    'encryption' => 'none',          // Encryption: 'none', 'ssl', or 'tls'
    
    // Authentication (leave empty if not required)
    'username' => '',                // SMTP username (empty for Mailpit)
    'password' => '',                // SMTP password (empty for Mailpit)
    
    // Default Email Settings
    'from_email' => 'test@example.com',
    'from_name' => 'Email Test',
    
    // Connection Settings
    'timeout' => 30,                 // Connection timeout in seconds
    
    // Security (for production use)
    'verify_peer' => false,          // Verify SSL certificate (set to true in production)
    'verify_peer_name' => false,     // Verify peer name (set to true in production)
];









