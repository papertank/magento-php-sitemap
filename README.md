# Custom Magento Google Sitemap Generator

Using a custom class and Magento specific (collections) code, this simple script is designed to be used via the command line / cron job to generate a Google compatible XML sitemap.

## Installation

Upload the `sitemap.php` file to your Magento `shell` folder (within the root).

The sitemap generator can then be run via the command line with `php shell/sitemap.php` or set up as a regular task via crontab.

## Output

When run, the script will ouput a `sitemap.xml` file in the parent directory to the `sitemap.php` file (see below for changing path).

The XML file will contain:

  * All CMS pages which are have status *Active*
  * All Catalog Categories which have status *Enabled*
  * All Catalog Products which are *Enabled* and have visibility "*Catalog*" or "*Catalog, Search*"

## Configuration

You can change the **priority** field of the different url types and the path of the required Magento `Mage.php` and outputted `sitemap.xml` file by updating the lines below:

	require_once (dirname(__FILE__).'/../app/Mage.php');
	Mage::app();

	$sitemap_file = dirname(__FILE__).'/../sitemap.xml';

	$page_priority = '1';
	$category_priority = '0.5';
	$product_priority = '0.5';

  	
## Troubleshooting

The script is designed to work with single store Magento setups.

Feel free to submit an issue if you find any problems or bugs.

## Development

- Source hosted at [GitHub](https://github.com/papertank/magento-php-sitemap)
- Please fork and make suggested updates and improvements

## Authors

[Papertank](https://github.com/papertank)