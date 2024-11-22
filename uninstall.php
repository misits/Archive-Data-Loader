<?php

// Prevent direct access.
defined( 'ABSPATH' ) or exit;

// if uninstall.php is not called by WordPress, die
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    die;
}

// Delete options.
delete_option( 'adl_db_host' );
delete_option( 'adl_db_user' );
delete_option( 'adl_db_password' );
delete_option( 'adl_db_name' );