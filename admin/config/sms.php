<?php

declare(strict_types=1);

// SMS Gateway Configuration
// Store sensitive credentials securely - never expose to frontend
class SMSConfig {
    // Text.lk API Configuration
    private const API_KEY = '2012|LhGw8Qt21esR5oTAK4jjtkily5V2KeYf2hMz89De28928987';
    private const API_URL = 'https://app.text.lk/api/v3/sms/send';
    private const SENDER_ID = 'SahanaLK'; // Max 11 characters, alphanumeric
    
    // Testing configuration
    private const TESTING_MODE = false; // Set to false to send SMS to actual customer numbers
    private const TEST_MOBILE = '94778553032'; // Format: 94XXXXXXXXX (without +)
    
    // SSL verification (enable in production). You can override via env SMS_VERIFY_SSL=true/false
    private const VERIFY_SSL_DEFAULT = false;
    
    // Rate limiting
    private const MAX_REQUESTS_PER_MINUTE = 60;
    
    /**
     * Get API Key
     */
    public static function getApiKey(): string {
        return self::API_KEY;
    }
    
    /**
     * Get API URL
     */
    public static function getApiUrl(): string {
        return self::API_URL;
    }
    
    /**
     * Get Sender ID
     */
    public static function getSenderId(): string {
        $env = getenv('TEXTLK_SENDER_ID');
        if ($env !== false && $env !== '') {
            return substr(preg_replace('/[^A-Za-z0-9]/', '', (string)$env), 0, 11);
        }
        return self::SENDER_ID;
    }
    
    /**
     * Check if testing mode is enabled
     */
    public static function isTestingMode(): bool {
        return self::TESTING_MODE;
    }
    
    /**
     * Get test mobile number (for testing purposes)
     */
    public static function getTestMobile(): string {
        return self::TEST_MOBILE;
    }
    
    /** Determine whether to verify SSL certificates (true in production). */
    public static function shouldVerifySSL(): bool {
        $env = getenv('SMS_VERIFY_SSL');
        if ($env !== false) {
            $normalized = strtolower((string)$env);
            return in_array($normalized, ['1','true','yes','on'], true);
        }
        return self::VERIFY_SSL_DEFAULT;
    }
    
    /**
     * Format phone number to Text.lk format (94XXXXXXXXX)
     * Removes +, spaces, and ensures country code format
     */
    public static function formatPhoneNumber(string $phone): string {
        // Remove all non-digit characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 94
        if (substr($phone, 0, 1) === '0') {
            $phone = '94' . substr($phone, 1);
        }
        
        // If doesn't start with 94, add it
        if (substr($phone, 0, 2) !== '94') {
            $phone = '94' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Validate phone number format
     */
    public static function isValidPhoneNumber(string $phone): bool {
        $formatted = self::formatPhoneNumber($phone);
        // Sri Lankan mobile numbers: 94 + 9 digits (total 11 digits after country code)
        return strlen($formatted) === 11 && substr($formatted, 0, 2) === '94';
    }
    
    /**
     * Get recipient number (use test number in testing mode)
     */
    public static function getRecipientNumber(string $customerMobile): string {
        if (self::isTestingMode()) {
            return self::getTestMobile();
        }
        return self::formatPhoneNumber($customerMobile);
    }
}

