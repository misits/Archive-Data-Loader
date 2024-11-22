<?php

namespace ArchiveDataLoader;

use ArchiveDataLoader\Logger;

class DataLoader
{
    public static function load_data_from_table($src_table, $dest_table, $post_type, $field_mappings = null)
    {
        if (!is_array($field_mappings)) {
            Logger::log_message('Invalid field mappings provided.');
            return false;
        }

        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        if ($archive_db->connect_error) {
            Logger::log_message('Database connection failed: ' . $archive_db->connect_error);
            return false;
        }

        $archive_query = "SELECT * FROM " . $archive_db->real_escape_string($src_table);
        $result = $archive_db->query($archive_query);

        if (!$result) {
            Logger::log_message('Query failed: ' . $archive_db->error);
            return false;
        }

        $processed = 0;
        while ($row = $result->fetch_assoc()) {
            Logger::log_message('Processing row: ' . print_r($row, true));

            $post_data = [
                'post_title'    => $row[$field_mappings['post_title']] ?? 'Untitled Post ' . uniqid(),
                'post_content'  => $row[$field_mappings['post_content']] ?? '',
                'post_status'   => 'publish',
                'post_author'   => get_current_user_id() ?: 1,
                'post_type'     => $post_type,
                'post_date'     => current_time('mysql')
            ];

            $post_id = null;

            // Check if the post already exists
            $existing_post = new \WP_Query([
                'post_type' => $post_type,
                'meta_query' => [
                    [
                        'key' => '_unique_identifier',
                        'value' => $row[$field_mappings['post_title']],
                        'compare' => '='
                    ]
                ],
                'posts_per_page' => 1
            ]);

            if ($existing_post->have_posts()) {
                $post_id = $existing_post->posts[0]->ID;
                Logger::log_message('Existing post found: ' . $post_id);
            } else {
                $post_id = wp_insert_post($post_data);
                if (is_wp_error($post_id)) {
                    Logger::log_message('Failed to insert post: ' . $post_id->get_error_message());
                    continue;
                }
                Logger::log_message('Inserted new post with ID: ' . $post_id);
            }
            wp_reset_postdata();

            // Handle dynamic meta fields
            if ($post_id && !empty($field_mappings['post_meta'])) {
                foreach ($field_mappings['post_meta'] as $meta_mapping) {
                    if (!empty($meta_mapping['name']) && array_key_exists($meta_mapping['value'], $row)) {
                        $meta_value = $row[$meta_mapping['value']];
                        if ($meta_value === '' || $meta_value === null) {
                            Logger::log_message('Meta field has an empty value: ' . $meta_mapping['name']);
                            continue;
                        }
                        $meta_key = sanitize_key($meta_mapping['name']);
                        update_post_meta($post_id, $meta_key, $meta_value);
                        Logger::log_message('Updated post meta: ' . $meta_key . ' = ' . $meta_value);
                    } else {
                        Logger::log_message('Invalid meta mapping or missing row value for: ' . print_r($meta_mapping, true));
                    }
                }

                if (!empty($row[$field_mappings['post_title']])) {
                    $unique_identifier = $row[$field_mappings['post_title']];
                    update_post_meta($post_id, '_unique_identifier', $unique_identifier);
                    Logger::log_message('Unique identifier saved: ' . $unique_identifier);
                }
            }

            $processed++;
        }

        $archive_db->close();
        Logger::log_message('Processed ' . $processed . ' rows.');
        return $processed;
    }


    /**
     * Get the tables in the archive database.
     *
     * @return array
     */
    public static function get_archive_tables()
    {
        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        if ($archive_db->connect_error) {
            return [];
        }

        $tables = [];
        $result = $archive_db->query("SHOW TABLES");
        if ($result) {
            while ($row = $result->fetch_array()) {
                $tables[] = $row[0];
            }
        }

        $archive_db->close();
        return $tables;
    }

    /**
     * Get the tables in the current WordPress database.
     *
     * @return array
     */
    public static function get_current_tables()
    {
        global $wpdb;
        return $wpdb->get_col("SHOW TABLES");
    }

    /**
     * Generate MySQL dump of the source database into plugin directory /dump.
     * 
     * @return void
     */
    public static function dump_src_database()
    {
        $dump_dir = ADL_PLUGIN_DIR . 'dump/';
        $now = date('Y-m-d_H-i-s');
        if (!file_exists($dump_dir)) {
            mkdir($dump_dir, 0755, true);
        }

        $dump_file = $dump_dir . get_option('adl_db_name') . '_' . $now . '.sql';
        $dump_command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s 2>&1',
            escapeshellarg(get_option('adl_db_host')),
            escapeshellarg(get_option('adl_db_user')),
            escapeshellarg(openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV)),
            escapeshellarg(get_option('adl_db_name')),
            escapeshellarg($dump_file)
        );

        exec($dump_command, $output, $return_var);
        if ($return_var !== 0) {
            Logger::log_message('mysqldump error: ' . implode("\n", $output));

            return false;
        }

        return true;
    }

    /**
     * Generate MySQL dump of the current WordPress database into plugin directory /dump.
     * 
     * @return void
     */
    public static function dump_current_database()
    {
        global $wpdb;
        $dump_dir = ADL_PLUGIN_DIR . 'dump/';
        $now = date('Y-m-d_H-i-s');
        if (!file_exists($dump_dir)) {
            mkdir($dump_dir, 0755, true);
        }

        $dump_file = $dump_dir . $wpdb->dbname . '_' . $now . '.sql';
        $dump_command = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s 2>&1',
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_PASSWORD),
            escapeshellarg(DB_NAME),
            escapeshellarg($dump_file)
        );

        exec($dump_command, $output, $return_var);
        if ($return_var !== 0) {
            Logger::log_message('mysqldump error: ' . implode("\n", $output));

            return false;
        }

        return true;
    }

    public static function delete_dump_file($file_name)
    {
        $log_dir = ADL_PLUGIN_DIR . 'dump/';
        $file_path = $log_dir . $file_name;

        if (file_exists($file_path) && is_writable($file_path)) {
            unlink($file_path);
            echo '<div class="updated"><p>' . __('Dump file deleted successfully.', 'archive-data-loader') . '</p></div>';
        } else {
            echo '<div class="error"><p>' . __('Failed to delete the dump file.', 'archive-data-loader') . '</p></div>';
        }
    }

    public static function clear_dump_files()
    {
        $log_dir = ADL_PLUGIN_DIR . 'dump/';
        $files = array_diff(scandir($log_dir), ['.', '..']);

        foreach ($files as $file) {
            $file_path = $log_dir . $file;
            if (is_file($file_path)) {
                unlink($file_path);
            }
        }
        echo '<div class="updated"><p>' . __('Dump files cleared successfully.', 'archive-data-loader') . '</p></div>';
    }

    public static function get_dump_files()
    {
        $log_dir = ADL_PLUGIN_DIR . 'dump/';
        if (!is_dir($log_dir)) {
            return [];
        }

        $files = array_diff(scandir($log_dir), ['.', '..']);
        $log_files = [];

        // Remove file beginning with a dot
        $files = array_filter($files, function ($file) {
            return strpos($file, '.') !== 0;
        });


        foreach ($files as $file) {
            $file_path = $log_dir . $file;
            if (is_file($file_path)) {
                $log_files[] = [
                    'name' => $file,
                    'size' => filesize($file_path),
                ];
            }
        }
        return $log_files;
    }

    public static function download_dump_file($file_name)
    {
        $log_dir = ADL_PLUGIN_DIR . 'dump/';
        $file_path = $log_dir . $file_name;

        if (file_exists($file_path) && is_readable($file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        } else {
            wp_die(__('Invalid dump file specified.', 'archive-data-loader'), 'Error', ['back_link' => true]);
        }
    }

    public static function get_dump_file_content($file_name)
    {
        $log_dir = ADL_PLUGIN_DIR . 'dump/';
        $file_path = $log_dir . $file_name;

        if (file_exists($file_path) && is_readable($file_path)) {
            return file_get_contents($file_path);
        } else {
            return __('Invalid dump file specified.', 'archive-data-loader');
        }
    }

    /**
     * Is source database WordPress database?
     * 
     * @return bool
     */
    public static function is_src_database_wp()
    {
        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        if ($archive_db->connect_error) {
            return false;
        }

        $result = $archive_db->query("SHOW TABLES");

        if ($result) {
            while ($row = $result->fetch_array()) {
                if ($row[0] === 'wp_options') {
                    return true;
                }
            }
        }

        $archive_db->close();

        return false;
    }

    /**
     * Is source database registered and valid?
     * 
     * @return bool
     */
    public static function is_src_database_registered()
    {
        $options = get_option('adl_db_host') && get_option('adl_db_user') && get_option('adl_db_password') && get_option('adl_db_name');

        return $options && self::test_connection_database();
    }

    /**
     * Restore the source database into the current WordPress database.
     * 
     * @return void
     */
    public static function restore_database_from_src_database()
    {
        global $wpdb;

        // Clear existing log
        update_option('adl_restore_log', []);

        // Initialize log
        $log = [];

        // Connect to the source database
        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        if ($archive_db->connect_error) {
            $log[] = __('Source DB Connection Error: ', 'archive-data-loader') . $archive_db->connect_error;
            update_option('adl_restore_log', $log);
            return;
        }

        $log[] = __('Connected to the source database.', 'archive-data-loader');

        // Get source database table prefix and URLs
        $src_prefix = '';
        $src_site_url = '';
        $src_home_url = '';

        $src_tables = $archive_db->query("SHOW TABLES");
        if (!$src_tables) {
            $log[] = __('Failed to retrieve tables from source database.', 'archive-data-loader');
            update_option('adl_restore_log', $log);
            return;
        } else {
            $log[] = __('Retrieved tables from source database.', 'archive-data-loader');
        }

        $wpdb->query('START TRANSACTION');
        try {
            while ($row = $src_tables->fetch_array()) {
                $table = $row[0];
                $table_name = str_replace($src_prefix, $wpdb->prefix, $table);

                // Check if the table exists
                if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                    // Create table in destination database
                    $create_table_sql = $archive_db->query("SHOW CREATE TABLE $table")->fetch_assoc()['Create Table'];
                    $create_table_sql = str_replace("`$table`", "`$table_name`", $create_table_sql);
                    if (!$wpdb->query($create_table_sql)) {
                        $log[] = __('Failed to create table ', 'archive-data-loader') . $table_name . ': ' . $wpdb->last_error;
                    } else {
                        $log[] = __('Created table ', 'archive-data-loader') . $table_name;
                    }
                }

                // Copy data from source to destination
                $result = $archive_db->query("SELECT * FROM $table");
                while ($data = $result->fetch_assoc()) {
                    $columns = array_keys($data);
                    $values = array_values($data);

                    // Prepare column names and values for insertion
                    $columns_list = implode(", ", array_map(function ($col) {
                        return "`$col`";
                    }, $columns));
                    $placeholders = implode(", ", array_fill(0, count($values), '%s'));

                    // Prepare update part for ON DUPLICATE KEY UPDATE
                    $update_list = implode(", ", array_map(function ($column) {
                        return "`$column` = VALUES(`$column`)";
                    }, $columns));

                    $query = $wpdb->prepare(
                        "INSERT INTO `$table_name` ($columns_list) VALUES ($placeholders)
                    ON DUPLICATE KEY UPDATE $update_list",
                        $values
                    );

                    if (!$wpdb->query($query)) {
                        $log[] = __('Failed to insert/update data into ', 'archive-data-loader') . $table_name . ': ' . $wpdb->last_error;
                    }
                }

                $log[] = __('Copied data into ', 'archive-data-loader') . $table_name;
                // Wait to avoid "MySQL server has gone away" error
                sleep(1);
            }

            // Update site URL and home URL in the options table
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}options SET option_value = %s WHERE option_name = 'siteurl'",
                get_site_url()
            ));
            $wpdb->query($wpdb->prepare(
                "UPDATE {$wpdb->prefix}options SET option_value = %s WHERE option_name = 'home'",
                get_home_url()
            ));

            $log[] = __('Updated siteurl and home options.', 'archive-data-loader');

            $archive_db->close();

            // Clear cache
            wp_cache_flush();

            $wpdb->query('COMMIT');
            $log[] = __('Database restoration completed successfully.', 'archive-data-loader');
        } catch (\Exception $e) {
            $wpdb->query('ROLLBACK');
            $log[] = __('Error during database restoration: ', 'archive-data-loader') . $e->getMessage();
        }

        // Add date and time to log entries
        $log = array_map(function ($entry) {
            return '[' . date('Y-m-d H:i:s') . '] ' . $entry;
        }, $log);

        // Remove duplicate entries
        $log = array_unique($log);

        // Update log option
        update_option('adl_restore_log', $log);

        // Create a log file
        Logger::log_message(implode("\n", $log));
    }

    public static function test_connection_database()
    {
        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        if ($archive_db->connect_error) {
            return false;
        }

        $archive_db->close();
        return true;
    }
}
