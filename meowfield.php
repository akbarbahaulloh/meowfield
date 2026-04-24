<?php
/**
 * Plugin Name: MeowField
 * Plugin URI: https://github.com/akbarbahaulloh/meowfield
 * Description: Premium Content Modeling plugin. Build Custom Post Types, Taxonomies, and Custom Fields with OpenStreetMap support.
 * Version: 1.0.0
 * Author: Antigravity
 * Text Domain: meowfield
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('MEOWFIELD_VERSION', '1.0.0');
define('MEOWFIELD_PATH', plugin_dir_path(__FILE__));
define('MEOWFIELD_URL', plugin_dir_url(__FILE__));
define('MEOWFIELD_PLUGIN_FILE', __FILE__);

// Autoload classes
spl_autoload_register(function ($class) {
    $prefix = 'MeowField\\';
    $base_dir = MEOWFIELD_PATH . 'includes/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    
    // Split into parts to handle subdirectories
    $parts = explode('\\', $relative_class);
    $class_name = array_pop($parts);
    $filename = 'class-' . strtolower(str_replace('_', '-', $class_name)) . '.php';
    $sub_path = '';
    if (!empty($parts)) {
        $sub_path = strtolower(implode('/', $parts)) . '/';
    }

    $file = $base_dir . $sub_path . $filename;

    if (file_exists($file)) {
        require $file;
    }
});

// Initialize the plugin
function meowfield_init() {
    new \MeowField\Main();
}
add_action('plugins_loaded', 'meowfield_init');
