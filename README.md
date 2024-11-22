# Archive Data Loader for WordPress

## Description

Archive Data Loader is a WordPress plugin designed to seamlessly import and manage data from archive databases into WordPress. This tool helps administrators efficiently migrate historical data while maintaining data integrity and relationships.

## Table of Contents

- [Archive Data Loader for WordPress](#archive-data-loader-for-wordpress)
  - [Description](#description)
  - [Table of Contents](#table-of-contents)
  - [Features](#features)
  - [Requirements](#requirements)
  - [Installation](#installation)
    - [Manual Installation](#manual-installation)
  - [Usage](#usage)
    - [Basic Import](#basic-import)
  - [FAQs](#faqs)
  - [Changelog](#changelog)
    - [Version 1.0.0](#version-100)
  - [Support](#support)
  - [Contributing](#contributing)
  - [License](#license)

## Features

- Simple data import from archive databases
- Custom data mapping capabilities
- Data validation and error handling
- Detailed import logs
- User-friendly admin interface

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher
- Access to archive database
- Minimum Memory Limit: 256M
- Maximum Execution Time: 300 seconds

## Installation

1. Download the plugin ZIP file
2. Navigate to WordPress admin panel > Plugins > Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now"
5. Activate the plugin
6. Go to Settings > Archive Data Loader to configure

### Manual Installation

1. Unzip the plugin file
2. Upload the `archive-data-loader` folder to `/wp-content/plugins/`
3. Activate through the WordPress admin interface

## Usage

### Basic Import

1. Navigate to Tools > Archive Data Loader
2. Configure your database connection
3. Select tables to import
4. Map fields to WordPress data
5. Click "Load Data" to start the import

## FAQs

**Q: What happens if an import fails?**
A: The plugin includes rollback functionality and detailed error logging.

## Changelog

### Version 1.0.0

- Initial release
- Basic import functionality
- Admin interface
- Database connection management
- Field mapping system
- Error handling and logging

## Support

- Documentation: [Link to docs]
- Support Email: [dev@misits.ch](mailto:dev@misits.ch)
- Issue Tracker: [GitHub Issues]

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit changes
4. Push to the branch
5. Create Pull Request

## License

GPL v2 or later
