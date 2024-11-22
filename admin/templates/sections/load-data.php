<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

// Verify we have the required data
if (!isset($src_tables) || !isset($dest_tables) || !isset($post_types)) {
    return;
}
?>

<?php if (DataLoader::is_src_database_registered()) : ?>
    <div id="adl-loader" class="adl-section">
        <h2 class="adl-toggle">
            <span class="adl-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-database-smile">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 14h.01" />
                    <path d="M14 14h.01" />
                    <path d="M10 17a3.5 3.5 0 0 0 4 0" />
                    <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3" />
                    <path d="M4 6v12c0 1.657 3.582 3 8 3s8 -1.343 8 -3v-12" />
                </svg>
                <?= __('Load Data', 'archive-data-loader') ?> <span class="desc"> - <?= __('Map the fields from the selected table in source database to the current database', 'archive-data-loader') ?>.</span>
            </span>
            <span class="adl-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg></span>
        </h2>
        <div class="adl-content">
            <form method="post">
                <!-- nonce field -->
                <?php wp_nonce_field('adl_load_data', '_wpnonce_adl_load_data'); ?>
                <div class="form-group">
                    <label for="adl_src_table"><?= __('Source Table', 'archive-data-loader') ?>:</label>
                    <select name="adl_src_table" id="adl_src_table" required>
                        <option value=""><?= __('Select a table', 'archive-data-loader') ?></option>
                        <?php foreach ($src_tables as $table) : ?>
                            <option value="<?php echo esc_attr($table); ?>"><?php echo esc_html($table); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="adl_dest_data" class="mt-10">
                    <hr class="adl_hr" />
                    <div class="flex flex-col gap-10">
                        <div class="form-group">
                            <label for="adl_dest_table"><?= __('Destination Table', 'archive-data-loader') ?>:</label>
                            <select name="adl_dest_table" id="adl_dest_table">
                                <option value=""><?= __('Select a table', 'archive-data-loader') ?></option>
                                <?php foreach ($dest_tables as $table) : ?>
                                    <option value="<?php echo esc_attr($table); ?>"><?php echo esc_html($table); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div id="adl_load_data" class="mt-10">
                    <div id="adl_load_post_type">
                        <div class="flex flex-col gap-10">
                            <div class="form-group">
                                <label for="adl_post_type"><?= __('Post Type', 'archive-data-loader') ?>:</label>
                                <select name="adl_post_type" id="adl_post_type">
                                    <?php foreach ($post_types as $post_type) : ?>
                                        <option value="<?php echo esc_attr($post_type->name); ?>"><?php echo esc_html($post_type->label); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adl_post_title"><?= __('Title Field', 'archive-data-loader') ?>:</label>
                                <select name="adl_post_title" id="adl_post_title">
                                    <!-- Options populated by JS -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adl_post_content"><?= __('Content Field', 'archive-data-loader') ?>:</label>
                                <select name="adl_post_content" id="adl_post_content">
                                    <!-- Options populated by JS -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="adl_post_date"><?= __('Date Field', 'archive-data-loader') ?>:</label>
                                <select name="adl_post_date" id="adl_post_date">
                                    <!-- Options populated by JS -->
                                </select>
                            </div>
                        </div>

                        <div id="adl_post_meta_container" class="mt-10 flex flex-col gap-10">
                            <div class="adl_post_meta grid-meta mt-10">
                                <div class="flex flex-col gap-10">
                                    <label><?= __('Archive Column', 'archive-data-loader') ?>:</label>
                                    <select name="adl_post_meta[0][value]" class="adl_table_columns">
                                        <!-- Options populated by JS -->
                                    </select>
                                </div>
                                <div class="flex flex-col gap-10">
                                    <label><?= __('Post Meta Fields', 'archive-data-loader') ?>:</label>
                                    <input type="text" name="adl_post_meta[0][name]" placeholder="Meta Key" class="regular-text">
                                </div>
                                <div class="flex flex-col gap-10">
                                    <label><?= __('Action', 'archive-data-loader') ?>:</label>
                                    <button type="button" class="button button-secondary adl_add_meta"><?= __('Add Meta', 'archive-data-loader') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="submit">
                        <button type="submit" name="adl_load_data" class="button button-primary"><?= __('Load Data', 'archive-data-loader') ?></button>
                    </p>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>