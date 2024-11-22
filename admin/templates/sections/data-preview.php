<?php

if (!defined('ABSPATH')) exit;

use ArchiveDataLoader\DataLoader;

?>

<?php if (DataLoader::is_src_database_registered()) : ?>
    <div id="adl-preview" class="adl-section">
        <h2 class="adl-toggle">
            <span class="adl-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-column">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                    <path d="M10 10h11" />
                    <path d="M10 3v18" />
                    <path d="M9 3l-6 6" />
                    <path d="M10 7l-7 7" />
                    <path d="M10 12l-7 7" />
                    <path d="M10 17l-4 4" />
                </svg>
                <?= __('Data Preview', 'archive-data-loader') ?> <span class="desc"> - <?= __('Preview the first 5 rows of the selected table', 'archive-data-loader') ?>.</span>
            </span>
            <span class="adl-arrow"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M6 9l6 6l6 -6" />
                </svg></span>
        </h2>
        <div class="adl-content">
            <div id="adl-data-preview" style="overflow-x: auto;"></div>
        </div>
    </div>
<?php endif; ?>