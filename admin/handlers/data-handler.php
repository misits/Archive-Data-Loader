<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

if (isset($_POST['adl_load_data'])) {
    $selected_table = sanitize_text_field($_POST['adl_src_table']);
    $destination_table = sanitize_text_field($_POST['adl_dest_table']);
    $post_type = sanitize_text_field($_POST['adl_post_type']);
    $field_mappings = array(
        'post_title' => sanitize_text_field($_POST['adl_post_title']),
        'post_content' => sanitize_text_field($_POST['adl_post_content']),
        'post_date' => sanitize_text_field($_POST['adl_post_date']),
        'post_meta' => $_POST['adl_post_meta'],
    );
    DataLoader::load_data_from_table($selected_table, $destination_table, $post_type, $field_mappings);
    echo '<div class="updated"><p>Data loaded from ' . esc_html($selected_table) . '.</p></div>';
}