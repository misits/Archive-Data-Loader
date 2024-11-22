<?php

namespace ArchiveDataLoader;

use ArchiveDataLoader\Logger;
use ArchiveDataLoader\DataLoader;

class AdminPage
{
    private static function get_template_data()
    {
        return [
            'src_tables' => DataLoader::get_archive_tables(),
            'dest_tables' => DataLoader::get_current_tables(),
            'post_types' => get_post_types(['public' => true], 'objects'),
            'is_src_database_registered' => DataLoader::is_src_database_registered(),
            'is_src_database_wp' => DataLoader::is_src_database_wp(),
            'db_host' => get_option('adl_db_host'),
            'db_user' => get_option('adl_db_user'),
            'db_name' => get_option('adl_db_name'),
            'db_password' => self::get_decrypted_password(),
            'log_files' => Logger::get_log_files(),
            'dump_files' => DataLoader::get_dump_files(),
            'restore_log' => Logger::get_last_log()
        ];
    }

    private static function get_decrypted_password()
    {
        $encrypted_password = get_option('adl_db_password');
        if ($encrypted_password) {
            return openssl_decrypt($encrypted_password, 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV);
        }
        return '';
    }

    public static function render()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        // Get plugin root directory
        $plugin_dir = dirname(dirname(__FILE__));

        // Include handlers
        require_once $plugin_dir . '/admin/handlers/settings-handler.php';
        require_once $plugin_dir . '/admin/handlers/data-handler.php';
        require_once $plugin_dir . '/admin/handlers/database-handler.php';
        require_once $plugin_dir . '/admin/handlers/log-handler.php';

        // Get template data
        $data = self::get_template_data();

        // Include main template
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/templates/admin-template.php';
    }
}
