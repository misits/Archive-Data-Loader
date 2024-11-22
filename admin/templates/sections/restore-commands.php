<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

?>

<?php if (DataLoader::is_src_database_registered()) : ?>
    <div id="adl-commands" class="adl-section">
        <h2 class="adl-toggle">
            <span class="adl-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-terminal-2">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 9l3 3l-3 3" />
                    <path d="M13 15l3 0" />
                    <path d="M3 4m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                </svg>
                <?= __('Restore Command', 'archive-data-loader') ?> <span class="desc"> - <?= __('Execute commands to manage database', 'archive-data-loader') ?>.</span>
            </span>
            <span class="adl-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg></span>
        </h2>
        <div class="adl-content">
            <table class="adl_dump_list wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?= __('File Name', 'archive-data-loader') ?></th>
                        <th><?= __('Size', 'archive-data-loader') ?></th>
                        <th><?= __('Actions', 'archive-data-loader') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($dump_files)) : ?>
                        <?php foreach ($dump_files as $file) : ?>
                            <tr>
                                <td><?php echo esc_html($file['name']); ?></td>
                                <td><?php echo esc_html(size_format($file['size'])); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=adl_plugin&adl_download_dump=' . urlencode($file['name']))); ?>" class="button button-secondary"><?= __('Download', 'archive-data-loader') ?></a>
                                    <form method="post" style="display:inline;">
                                        <?php wp_nonce_field('adl_delete_dump', 'adl_delete_dump_nonce'); ?>
                                        <input type="hidden" name="action" value="delete_dump">
                                        <input type="hidden" name="file" value="<?php echo esc_attr($file['name']); ?>">
                                        <button type="submit" class="button button-danger"><?= __('Delete', 'archive-data-loader') ?></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="3"><?= __('No dump files available', 'archive-data-loader') ?>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="flex gap-10">
                <form method="post">
                    <!-- nonce field -->
                    <?php wp_nonce_field('adl_dump_src_database', '_wpnonce_adl_dump_src_database'); ?>
                    <button type="submit" name="adl_dump_src_database" class="button button-primary flex item-center gap-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-database">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12.75m-4 0a4 1.75 0 1 0 8 0a4 1.75 0 1 0 -8 0" />
                            <path d="M8 12.5v3.75c0 .966 1.79 1.75 4 1.75s4 -.784 4 -1.75v-3.75" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        </svg>
                        <?= __('Dump Source Database', 'archive-data-loader') ?>
                    </button>
                </form>


                <form method="post">
                    <!-- nonce field -->
                    <?php wp_nonce_field('adl_dump_current_database', '_wpnonce_adl_dump_current_database'); ?>
                    <button type="submit" name="adl_dump_current_database" class="button button-primary flex item-center gap-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-database">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12.75m-4 0a4 1.75 0 1 0 8 0a4 1.75 0 1 0 -8 0" />
                            <path d="M8 12.5v3.75c0 .966 1.79 1.75 4 1.75s4 -.784 4 -1.75v-3.75" />
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                        </svg>
                        <?= __('Dump Current Database', 'archive-data-loader') ?>
                    </button>
                </form>

                <?php if (DataLoader::is_src_database_wp()) : ?>
                    <form method="post" class="adl-restore-src-database">
                        <!-- nonce field -->
                        <?php wp_nonce_field('adl_restore_src_database', '_wpnonce_adl_restore_src_database'); ?>
                        <button type="submit" name="adl_restore_src_database" class="button button-secondary flex item-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-database-import">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3" />
                                <path d="M4 6v6c0 1.657 3.582 3 8 3c.856 0 1.68 -.05 2.454 -.144m5.546 -2.856v-6" />
                                <path d="M4 12v6c0 1.657 3.582 3 8 3c.171 0 .341 -.002 .51 -.006" />
                                <path d="M19 22v-6" />
                                <path d="M22 19l-3 -3l-3 3" />
                            </svg>
                            <?= __('Restore from Source Database') ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>