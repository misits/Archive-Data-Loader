<?php

namespace ArchiveDataLoader;

class PreviewHandler
{
    public static function preview_data()
    {
        // Check the nonce for security
        check_ajax_referer('adl_preview_data');

        // Ensure 'table' key exists in the request and is not empty
        if (!isset($_POST['table']) || empty($_POST['table'])) {
            wp_send_json_error(__('Missing or empty table parameter.', 'archive-data-loader'));
        }

        // Sanitize the table name
        $table = sanitize_text_field($_POST['table']);

        // Connect to the archive database
        $archive_db = new \mysqli(
            get_option('adl_db_host'),
            get_option('adl_db_user'),
            openssl_decrypt(get_option('adl_db_password'), 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV),
            get_option('adl_db_name')
        );

        // Check for connection errors
        if ($archive_db->connect_error) {
            wp_send_json_error('Connection error: ' . $archive_db->connect_error);
        }

        // Validate the table name to prevent SQL injection
        $escaped_table = $archive_db->real_escape_string($table);

        // Query the table with a limit of 5 rows
        $query = "SELECT * FROM `$escaped_table` LIMIT 5";
        $result = $archive_db->query($query);

        // Check for query errors
        if (!$result) {
            $archive_db->close();
            wp_send_json_error(__('Query error: ', 'archive-data-loader') . $archive_db->error);
        }

        // Fetch the data
        $columns = [];
        $rows = [];

        while ($row = $result->fetch_assoc()) {
            // Capture the column names only once
            if (empty($columns)) {
                $columns = array_keys($row);
            }
            $rows[] = $row;
        }

        // Close the database connection
        $archive_db->close();

        // Respond with the data
        wp_send_json_success([
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }
}
