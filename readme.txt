=== Archive Data Loader ===
Contributors: misits
Tags: data import, archive, database migration
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Archive Data Loader helps administrators import and manage data from archive databases into WordPress with ease and reliability.

== Description ==

Archive Data Loader is a powerful WordPress plugin designed to facilitate the seamless import of data from external archive databases into your WordPress site. With user-friendly options for data mapping, validation, and logging, this plugin is perfect for migrating historical data or integrating with external systems.

= Features =
- Import data from MySQL databases.
- Flexible field mapping to match archive fields with WordPress data.
- Detailed error logging for failed imports.
- User-friendly admin interface for managing imports.
- Support for importing posts, custom fields, and more.

= Use Case Scenarios: =
- Importing data from legacy systems into WordPress.
- Syncing archive data with WordPress for analysis or presentation.
- Migrating historical content into WordPress without losing structure.

== Installation ==

1. Download the plugin ZIP file.
2. Navigate to the **Plugins** section in your WordPress admin dashboard and click **Add New**.
3. Click the **Upload Plugin** button, then select the ZIP file and click **Install Now**.
4. After installation, click **Activate** to enable the plugin.
5. Go to **Tools > Archive Data Loader** to configure and start importing data.

= Manual Installation =

1. Unzip the plugin file.
2. Upload the entire `archive-data-loader` folder to the `/wp-content/plugins/` directory.
3. Activate the plugin through the **Plugins** menu in WordPress.

== Frequently Asked Questions ==

= How do I configure the plugin? =
Go to **Tools > Archive Data Loader** in your WordPress admin dashboard and enter your database connection details. Follow the on-screen instructions to map fields and start importing.

= What happens if an import fails? =
The plugin includes error logging, so you can review any issues and retry the import. Additionally, no existing WordPress data will be overwritten unless explicitly mapped.

= Does the plugin support large imports? =
Yes, the plugin supports large datasets, but you may need to increase your PHP memory limit and execution time.

== Changelog ==

= 1.0.0 =
* Initial release
* Support for MySQL connections
* Field mapping interface
* Import logs and error handling
* User-friendly admin settings

== Screenshots ==

1. Easily configure your external database connection.

2. Map archive fields to WordPress post fields.

3. Review logs for import status and error messages.

== Upgrade Notice ==

= 1.0.0 =
First release. Ensure you have backups of your WordPress database before using the plugin.
