<?php
/**
 * Plugin Name: Archive Data Loader
 * Description: A plugin to load data from an archive database into WordPress.
 * Version: 1.0.0
 * Requires at least: 5.2
 * Requires PHP: 8.0
 * Author: Martin IS IT Services
 * Author URI: https://misits.ch
 * Text Domain: archive-data-loader
 * Domain Path: /languages
 */

namespace ArchiveDataLoader;

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

require 'utils/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
	'https://github.com/misits/archive-data-loader',
	__FILE__,
	'archive-data-loader'
);

/**
 * Main plugin class
 */
final class ArchiveDataLoader {
    /**
     * Plugin instance
     * @var ArchiveDataLoader
     */
    private static $instance = null;

    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Private constructor
     */
    private function __construct() {
        $this->define_constants();
        $this->register_autoloader();
        $this->init_hooks();
    }

    /**
     * Define plugin constants
     */
    private function define_constants() {
        define('ADL_VERSION', '1.0.0');
        define('ADL_PLUGIN_FILE', __FILE__);
        define('ADL_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('ADL_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('ADL_ENCRYPTION_KEY', NONCE_KEY);
        define('ADL_ENCRYPTION_IV', 'ec248072808be097');
    }

    /**
     * Register autoloader
     */
    private function register_autoloader() {
        spl_autoload_register(function ($class) {
            // Project-specific namespace prefix
            $prefix = 'ArchiveDataLoader\\';
            $base_dir = plugin_dir_path(__FILE__) . 'includes/';
        
            // Check if the class uses the namespace prefix
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                return;
            }
        
            // Get the relative class name
            $relative_class = substr($class, $len);
        
            // Convert class name to file name:
            // Example: AdminPage becomes class-admin-page.php
            $file_name = 'class-' . strtolower(str_replace('_', '-', 
                preg_replace('/([a-z])([A-Z])/', '$1-$2', $relative_class)
            )) . '.php';
        
            $file = $base_dir . $file_name;
        
            // If the file exists, require it
            if (file_exists($file)) {
                require $file;
            }
        });
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize plugin
        add_action('plugins_loaded', [$this, 'init_plugin']);

        // Admin hooks
        if (is_admin()) {
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
            add_action('wp_ajax_adl_preview_data', ['ArchiveDataLoader\PreviewHandler', 'preview_data']);
        }
    }

    /**
     * Initialize plugin
     */
    public function init_plugin() {
        // Load text domain if needed
        load_plugin_textdomain('archive-data-loader', false, dirname(plugin_basename(__FILE__)) . '/languages');
        
        // Initialize the loader
        Loader::init();
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on plugin pages
        if (!$this->is_plugin_page($hook)) {
            return;
        }

        // Enqueue CSS
        wp_enqueue_style(
            'adl-admin',
            ADL_PLUGIN_URL . 'assets/css/admin.css',
            [],
            ADL_VERSION
        );

        // Enqueue JavaScript
        wp_enqueue_script(
            'adl-admin',
            ADL_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            ADL_VERSION,
            true
        );

        // Localize script
        wp_localize_script('adl-admin', 'adl', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('adl_preview_data'),
        ]);
    }

    /**
     * Check if current page is plugin page
     */
    private function is_plugin_page($hook) {
        $plugin_pages = [
            'toplevel_page_archive-data-loader',
            'archive-data-loader_page_adl-settings'
        ];
        
        return in_array($hook, $plugin_pages);
    }
}

// Initialize the plugin
function adl_init() {
    return ArchiveDataLoader::get_instance();
}

// Start the plugin
adl_init();