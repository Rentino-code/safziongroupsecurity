<?php
/**
 * Autoloading function
 * 
 * @param string $class The fully qualified class name
 */
spl_autoload_register(function ($class) {
    // Convert the namespace to a full file path
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    
    // Check if the file exists and include it
    if (file_exists($file)) {
        require_once $file;
    }
});