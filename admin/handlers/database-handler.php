<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

function add_adl_notice($message, $type = 'success', $id = '') {
    $id = !empty($id) ? $id : 'adl_' . uniqid();
    $class = 'notice notice-' . $type . ' settings-error is-dismissible';
    add_settings_error(
        'adl_messages',
        $id,
        $message,
        $class
    );
}

if (isset($_POST['adl_dump_src_database']) && wp_verify_nonce($_POST['_wpnonce_adl_dump_src_database'], 'adl_dump_src_database')) {
    $status = DataLoader::dump_src_database();
    if ($status) {
        add_adl_notice(__('Source database dumped successfully.', 'archive-data-loader'), 'success', 'adl_dump_src_success');
    } else {
        add_adl_notice(__('Failed to dump source database.', 'archive-data-loader'), 'error', 'adl_dump_src_error');
    }
}

if (isset($_POST['adl_dump_current_database']) && wp_verify_nonce($_POST['_wpnonce_adl_dump_current_database'], 'adl_dump_current_database')) {
    $status = DataLoader::dump_current_database();
    if ($status) {
        add_adl_notice(__('Current database dumped successfully.', 'archive-data-loader'), 'success', 'adl_dump_current_success');
    } else {
        add_adl_notice(__('Failed to dump current database.', 'archive-data-loader'), 'error', 'adl_dump_current_error');
    }
}

if (isset($_POST['adl_restore_src_database']) && wp_verify_nonce($_POST['_wpnonce_adl_restore_src_database'], 'adl_restore_src_database')) {
    $status = DataLoader::restore_database_from_src_database();
    if ($status) {
        add_adl_notice(__('Source database restored successfully.', 'archive-data-loader'), 'success', 'adl_restore_success');
    } else {
        add_adl_notice(__('Failed to restore source database.', 'archive-data-loader'), 'error', 'adl_restore_error');
    }
}

if (isset($_POST['adl_test_connection_database']) && wp_verify_nonce($_POST['_wpnonce_adl_test_connection_database'], 'adl_test_connection_database')) {
    $status = DataLoader::test_connection_database();
    if ($status) {
        add_adl_notice(__('Connection to database successful.', 'archive-data-loader'), 'success', 'adl_connection_success');
    } else {
        add_adl_notice(__('Connection to database failed.', 'archive-data-loader'), 'error', 'adl_connection_error');
    }
}