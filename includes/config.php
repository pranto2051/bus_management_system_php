<?php
/**
 * Base path configuration
 * Automatically detects the base path of the application
 */

if (!defined('BASE_PATH')) {
    // Get the script name (e.g., /bus/pages/dashboard.php)
    $script_name = $_SERVER['SCRIPT_NAME'];
    
    // Normalize path separators
    $script_name = str_replace('\\', '/', $script_name);
    
    // Remove leading slash and split into parts
    $path_parts = explode('/', trim($script_name, '/'));
    
    // Find where the application structure starts (includes, pages, admin, assets, index.php)
    $base_path_parts = [];
    
    foreach ($path_parts as $part) {
        // Stop when we hit application directories
        if (in_array($part, ['includes', 'pages', 'admin', 'assets']) || 
            (count($base_path_parts) === 0 && $part === 'index.php')) {
            break;
        }
        if (!empty($part) && $part !== 'index.php') {
            $base_path_parts[] = $part;
        }
    }
    
    // Build base path
    if (!empty($base_path_parts)) {
        $base_path = '/' . implode('/', $base_path_parts);
    } else {
        $base_path = '';
    }
    
    // Define BASE_PATH constant
    define('BASE_PATH', $base_path);
}

