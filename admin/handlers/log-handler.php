<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\Logger;

if (isset($_GET['adl_download_log'])) {
    Logger::download_log_file(sanitize_text_field($_GET['adl_download_log']));
}

if (isset($_POST['action']) && $_POST['action'] === 'delete_log') {
    if (!isset($_POST['adl_delete_log_nonce']) || !wp_verify_nonce($_POST['adl_delete_log_nonce'], 'adl_delete_log')) {
        wp_die(__('Security check failed', 'archive-data-loader'), 'Error', ['back_link' => true]);
    }
    
    $file = sanitize_text_field($_POST['file']);
    Logger::delete_log_file($file);
    wp_redirect(admin_url('admin.php?page=archive-data-loader'));
    exit;
}