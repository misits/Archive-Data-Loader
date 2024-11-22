<?php

if (!defined('ABSPATH')) exit;

if (isset($_POST['adl_save_settings']) && wp_verify_nonce($_POST['_wpnonce_adl_save_settings'], 'adl_save_settings')) {
    // Update database settings
    update_option('adl_db_host', sanitize_text_field($_POST['adl_db_host']));
    update_option('adl_db_user', sanitize_text_field($_POST['adl_db_user']));

    $password = sanitize_text_field($_POST['adl_db_password']);
    $encrypted_password = openssl_encrypt($password, 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV);
    update_option('adl_db_password', $encrypted_password);

    update_option('adl_db_name', sanitize_text_field($_POST['adl_db_name']));
    
    // Add success message
   add_settings_error(
        'adl_messages',
        'adl_settings_updated',
        __('Settings saved successfully.', 'archive-data-loader'),
        'updated'
    );
}