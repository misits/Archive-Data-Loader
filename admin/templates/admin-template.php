<?php

if (!defined('ABSPATH')) exit;

// Ensure we have the data
if (!isset($data) || !is_array($data)) {
    return;
}

// Extract data to make variables available to templates
extract($data);
?>

<div class="wrap" id="archive-data-loader">
    <?php
    // Show admin notices
    settings_errors('adl_messages');
    ?>

    <div id="adl-sections">
        <?php
        // Include each section with proper scope
        require dirname(__FILE__) . '/sections/database-settings.php';

        if ($data['is_src_database_registered']) {
            require dirname(__FILE__) . '/sections/load-data.php';
            require dirname(__FILE__) . '/sections/data-preview.php';
            require dirname(__FILE__) . '/sections/restore-commands.php';
        }

        require dirname(__FILE__) . '/sections/logs.php';
        ?>
    </div>
</div>