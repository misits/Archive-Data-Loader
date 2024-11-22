<?php

namespace ArchiveDataLoader;

use ArchiveDataLoader\AdminPage;
use ArchiveDataLoader\Logger;

class Loader
{
    /**
     * Initialize the plugin
     */
    public static function init()
    {
        // Check encryption requirements
        if (!self::check_encryption_requirements()) {
            return false;
        }

        // Load required files
        self::load_files();

        // Register hooks
        self::register_hooks();

        return true;
    }

    /**
     * Check if encryption requirements are met
     */
    private static function check_encryption_requirements()
    {
        if (!defined('ADL_ENCRYPTION_KEY') || empty(ADL_ENCRYPTION_IV)) {
            Logger::log_message(__('Encryption key or IV missing. Please configure properly.', 'archive-data-loader'));
            return false;
        }
        return true;
    }

    /**
     * Load required plugin files
     */
    private static function load_files()
    {
        // Get plugin root directory
        $plugin_dir = dirname(plugin_dir_path(__FILE__));

        // Load main classes
        require_once $plugin_dir . '/includes/class-data-loader.php';
        require_once $plugin_dir . '/includes/class-logger.php';
        require_once $plugin_dir . '/includes/class-admin-page.php';

        // Load admin handlers if in admin
        if (is_admin()) {
            // require add_settings_error
            require_once ABSPATH . 'wp-admin/includes/template.php';
            require_once $plugin_dir . '/admin/handlers/settings-handler.php';
            require_once $plugin_dir . '/admin/handlers/data-handler.php';
            require_once $plugin_dir . '/admin/handlers/database-handler.php';
            require_once $plugin_dir . '/admin/handlers/log-handler.php';
            require_once $plugin_dir . '/admin/handlers/dump-handler.php';
        }
    }

    /**
     * Register WordPress hooks
     */
    private static function register_hooks()
    {
        // Admin menu
        add_action('admin_menu', [__CLASS__, 'register_admin_menu']);

        // Admin assets
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
    }

    /**
     * Register the admin menu
     */
    public static function register_admin_menu()
    {
        add_menu_page(
            'Archive Data Loader',     // Page title
            'Archive Data Loader',     // Menu title
            'manage_options',          // Capability
            'archive-data-loader',     // Menu slug
            [__CLASS__, 'render_admin_page'], // Callback
            'dashicons-database',      // Icon
            20                         // Position
        );
    }

    /**
     * Render the admin page
     */
    public static function render_admin_page()
    {
        AdminPage::render();
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets($hook)
    {
        // Only load on our plugin page
        if ('toplevel_page_archive-data-loader' !== $hook) {
            return;
        }

        $plugin_dir_url = plugin_dir_url(dirname(__FILE__));

        wp_enqueue_style(
            'adl-admin-styles',
            $plugin_dir_url . 'assets/css/admin.css',
            [],
            filemtime(dirname(__FILE__, 2) . '/assets/css/admin.css')
        );

        wp_enqueue_script(
            'adl-admin-script',
            $plugin_dir_url . 'assets/js/admin.js',
            ['jquery'],
            filemtime(dirname(__FILE__, 2) . '/assets/js/admin.js'),
            true
        );

        // Add localized script data
        wp_localize_script('adl-admin-script', 'adlData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('adl_nonce')
        ]);
    }
}
