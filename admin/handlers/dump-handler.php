<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

if (isset($_GET['adl_download_dump'])) {
    DataLoader::download_dump_file(sanitize_text_field($_GET['adl_download_dump']));
}

if (isset($_POST['action']) && $_POST['action'] === 'delete_dump') {
    if (!isset($_POST['adl_delete_dump_nonce']) || !wp_verify_nonce($_POST['adl_delete_dump_nonce'], 'adl_delete_dump')) {
        wp_die(__('Security check failed', 'archive-data-loader'), 'Error', ['back_link' => true]);
    }
    
    $file = sanitize_text_field($_POST['file']);
    DataLoader::delete_dump_file($file);
    wp_redirect(admin_url('admin.php?page=archive-data-loader'));
    exit;
}