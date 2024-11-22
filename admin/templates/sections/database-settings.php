<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

?>

<div id="adl-settings" class="adl-section">
    <div class="adl-section-head">
        <div class="flex gap-10">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-transform">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M3 6a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                <path d="M21 11v-3a2 2 0 0 0 -2 -2h-6l3 3m0 -6l-3 3" />
                <path d="M3 13v3a2 2 0 0 0 2 2h6l-3 -3m0 6l3 -3" />
                <path d="M15 18a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
            </svg>
            <h1><?= __('Archive Data Loader', 'archive-data-loader') ?></h1>
        </div>
        <p><?= __('Use this tool to load data from a database into WordPress.', 'archive-data-loader') ?></p>
        <p><strong><?= __('Warning', 'archive-data-loader') ?>:</strong> <br /><?= __('This tool will load data from an external database into your WordPress site', 'archive-data-loader') ?>. <br /><?= __('Make sure you have a backup of your site before proceeding', 'archive-data-loader') ?>.</p>
    </div>
    <h2 class="adl-toggle">
        <span class="adl-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cloud-data-connection">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M5 9.897c0 -1.714 1.46 -3.104 3.26 -3.104c.275 -1.22 1.255 -2.215 2.572 -2.611c1.317 -.397 2.77 -.134 3.811 .69c1.042 .822 1.514 2.08 1.239 3.3h.693a2.42 2.42 0 0 1 2.425 2.414a2.42 2.42 0 0 1 -2.425 2.414h-8.315c-1.8 0 -3.26 -1.39 -3.26 -3.103z" />
                <path d="M12 13v3" />
                <path d="M12 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                <path d="M14 18h7" />
                <path d="M3 18h7" />
            </svg>
            <?= __('Database Settings', 'archive-data-loader') ?> <span class="desc"> - <?= __('Configure the connection to a database', 'archive-data-loader') ?>. </span>
        </span>
        <span class="adl-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M6 9l6 6l6 -6" />
            </svg></span>
    </h2>
    <div class="adl-content">
        <form method="post" class="flex flex-col gap-10">
            <!-- nonce field -->
            <?php wp_nonce_field('adl_save_settings', '_wpnonce_adl_save_settings'); ?>
            <div class="form-group">
                <label for="adl_db_host"><?= __('Database Host', 'archive-data-loader') ?>:</label>
                <input name="adl_db_host" type="text" id="adl_db_host" value="<?php echo esc_attr(get_option('adl_db_host')); ?>" class="regular-text">
            </div>

            <div class="form-group">
                <label for="adl_db_user"><?= __('Database User', 'archive-data-loader') ?>:</label>
                <input name="adl_db_user" type="text" id="adl_db_user" value="<?php echo esc_attr(get_option('adl_db_user')); ?>" class="regular-text">
            </div>

            <div class="form-group">
                <label for="adl_db_password"><?= __('Database Password', 'archive-data-loader') ?>:</label>
                <input name="adl_db_password" type="password" id="adl_db_password" value="<?php
                                                                                            $encrypted_password = get_option('adl_db_password');
                                                                                            if ($encrypted_password) {
                                                                                                echo esc_attr(openssl_decrypt($encrypted_password, 'AES-256-CBC', ADL_ENCRYPTION_KEY, 0, ADL_ENCRYPTION_IV));
                                                                                            }
                                                                                            ?>" class="regular-text">
            </div>

            <div class="form-group">
                <label for="adl_db_name"><?= __('Database Name', 'archive-data-loader') ?>:</label>
                <input name="adl_db_name" type="text" id="adl_db_name" value="<?php echo esc_attr(get_option('adl_db_name')); ?>" class="regular-text">
            </div>

            <div class="flex items-center gap-10 buttons">
            <button type="submit" name="adl_save_settings" class="button button-primary"><?= __('Save Settings', 'archive-data-loader') ?></button>

                <?php if (DataLoader::is_src_database_registered()) : ?>
                    <form method="post">
                        <!-- nonce field -->
                        <?php wp_nonce_field('adl_test_connection_database', '_wpnonce_adl_test_connection_database'); ?>
                        <button type="submit" name="adl_test_connection_database" class="button button-secondary flex item-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-server-bolt">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v2a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                                <path d="M15 20h-9a3 3 0 0 1 -3 -3v-2a3 3 0 0 1 3 -3h12" />
                                <path d="M7 8v.01" />
                                <path d="M7 16v.01" />
                                <path d="M20 15l-2 3h3l-2 3" />
                            </svg>
                            <?= __('Test Database Connection', 'archive-data-loader') ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </form>
    </div>
</div>