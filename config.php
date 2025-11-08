<?php
/**
 * Blog_Chain - Configuration with .env Support
 * This file loads environment variables and sets up database connection
 */

// ============================================
// LOAD ENVIRONMENT VARIABLES FROM .env FILE
// ============================================

/**
 * Simple .env file parser
 * Reads the .env file and loads variables into $_ENV
 */
function loadEnv($path = '.env') {
    if (!file_exists($path)) {
        die("Error: .env file not found! Please create .env file with your configuration.");
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip comments and empty lines
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            // Set as environment variable
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

// Load environment variables
loadEnv(__DIR__ . '/.env');

/**
 * Helper function to get environment variable
 * @param string $key - The environment variable key
 * @param mixed $default - Default value if key not found
 * @return mixed
 */
function env($key, $default = null) {
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

// ============================================
// ERROR REPORTING CONFIGURATION
// ============================================
$displayErrors = env('DISPLAY_ERRORS', '1');
$errorReporting = env('ERROR_REPORTING', 'E_ALL');

if ($displayErrors === '1' || $displayErrors === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// TIMEZONE CONFIGURATION
// ============================================
$timezone = env('TIMEZONE', 'Asia/Colombo');
date_default_timezone_set($timezone);

// ============================================
// DATABASE CONFIGURATION
// Load from environment variables
// ============================================
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_NAME', env('DB_NAME', 'blog_chain'));

// ============================================
// SITE CONFIGURATION
// ============================================
define('SITE_NAME', env('SITE_NAME', 'Blog_Chain'));
define('SITE_TAGLINE', env('SITE_TAGLINE', 'Linking thoughts, one post at a time'));

// ============================================
// FILE UPLOAD CONFIGURATION
// ============================================
define('UPLOAD_DIR', env('UPLOAD_DIR', 'uploads/'));
define('MAX_FILE_SIZE', env('MAX_FILE_SIZE', 5 * 1024 * 1024)); // Default 5MB

// ============================================
// SESSION CONFIGURATION
// ============================================
define('SESSION_LIFETIME', env('SESSION_LIFETIME', 3600)); // Default 1 hour

// ============================================
// DATABASE CONNECTION
// ============================================
try {
    // Attempt to connect to database
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check for connection errors
    if ($conn->connect_error) {
        // Log error (in production, log to file instead of displaying)
        if (env('ENVIRONMENT') === 'development') {
            die("Database Connection Error: " . $conn->connect_error . 
                "<br><br>Please check your .env file configuration.");
        } else {
            die("Database connection failed. Please contact the administrator.");
        }
    }
    
    // Set character set to UTF-8 for proper encoding
    $conn->set_charset("utf8mb4");
    
} catch (Exception $e) {
    if (env('ENVIRONMENT') === 'development') {
        die("Connection Error: " . $e->getMessage() . 
            "<br><br>Please verify your database credentials in .env file.");
    } else {
        die("Database connection failed. Please contact the administrator.");
    }
}

// ============================================
// SESSION MANAGEMENT
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    // Configure session parameters
    ini_set('session.cookie_lifetime', SESSION_LIFETIME);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    
    // Start session
    session_start();
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in (redirect if not)
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Sanitize user input for database
 * @param string $data - The data to sanitize
 * @return string - Sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = mysqli_real_escape_string($conn, $data);
    return $data;
}

/**
 * Get current logged-in user ID
 * @return int|null
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current logged-in username
 * @return string|null
 */
function getCurrentUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

/**
 * Handle image upload securely
 * @param array $file - The $_FILES array for the uploaded file
 * @return string|false - Returns filename on success, false on failure
 */
function handleImageUpload($file) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return false;
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    // Allowed image types
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    
    // Verify it's a real image using getimagesize
    $imageInfo = @getimagesize($file['tmp_name']);
    
    if ($imageInfo === false) {
        return false;
    }
    
    // Check mime type
    if (!in_array($imageInfo['mime'], $allowedTypes)) {
        return false;
    }
    
    // Create uploads directory if it doesn't exist
    if (!file_exists(UPLOAD_DIR)) {
        @mkdir(UPLOAD_DIR, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('blog_', true) . '.' . $extension;
    $filepath = UPLOAD_DIR . $filename;
    
    // Move uploaded file
    if (@move_uploaded_file($file['tmp_name'], $filepath)) {
        // Set proper permissions
        @chmod($filepath, 0644);
        return $filename;
    }
    
    return false;
}

/**
 * Delete an image file
 * @param string $filename - The filename to delete
 * @return bool - True on success, false on failure
 */
function deleteImage($filename) {
    if (empty($filename)) {
        return false;
    }
    
    $filepath = UPLOAD_DIR . $filename;
    
    if (file_exists($filepath)) {
        return @unlink($filepath);
    }
    
    return false;
}

/**
 * Get full URL for an image
 * @param string $filename - The image filename
 * @return string - Full image URL or empty string
 */
function getImageUrl($filename) {
    if (empty($filename)) {
        return '';
    }
    return UPLOAD_DIR . $filename;
}

/**
 * Display success message
 * @param string $message - The message to display
 * @return string - HTML formatted success message
 */
function showSuccess($message) {
    return '<div class="alert alert-success">' . htmlspecialchars($message) . '</div>';
}

/**
 * Display error message
 * @param string $message - The message to display
 * @return string - HTML formatted error message
 */
function showError($message) {
    return '<div class="alert alert-error">' . htmlspecialchars($message) . '</div>';
}

/**
 * Format date to readable format
 * @param string $date - The date to format
 * @return string - Formatted date
 */
function formatDate($date) {
    return date('F d, Y', strtotime($date));
}

/**
 * Calculate reading time based on word count
 * @param string $content - The content to analyze
 * @return int - Estimated reading time in minutes
 */
function calculateReadingTime($content) {
    $wordCount = str_word_count(strip_tags($content));
    return max(1, ceil($wordCount / 200)); // Average 200 words per minute
}

/**
 * Validate email address
 * @param string $email - Email to validate
 * @return bool - True if valid, false otherwise
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token for forms
 * @return string - CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token - Token to verify
 * @return bool - True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// ENVIRONMENT CHECK
// ============================================
if (env('ENVIRONMENT') === 'development') {
    // Development mode - show detailed errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Production mode - hide errors
    error_reporting(0);
    ini_set('display_errors', 0);
}

// ============================================
// SUCCESS MESSAGE
// Configuration loaded successfully
// ============================================
// Uncomment below line for debugging during setup
// echo "<!-- Blog_Chain Configuration Loaded Successfully -->";
?>