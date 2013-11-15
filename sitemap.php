<?php

class PT_Magento_Sitemap {

	protected $file;
	protected $filename;

	protected $urls;
	
	public function __construct($filename)
	{	
		$this->urls = array();
		$this->filename = $filename;
	}
	
	public function formatDate($datetime)
	{
		$timestamp = strtotime($datetime);
		return date('Y-m-d', $timestamp);
	}
	
	public function addUrl($loc, $priority = '1', $lastmod = NULL)
	{
		$this->urls[] = array(
			'loc' => $loc,
			'priority' => $priority,
			'lastmod' => ( $lastmod ? $this->formatDate($lastmod) : NULL ),
		);
		
		return true;
	}
	
	public function generate()
	{
		if ( ! $this->file ) {
			$this->openFile();
		}
	
		if ( ! $this->urls ) {
			return false;
		}
	
		foreach ( $this->urls as $url )  {
			$this->writeUrl($url);
		}
		
		$this->closeFile();
		
		return true;
	}
	
	private function openFile()
	{
		$this->file = fopen($this->filename, 'w');
		
		if ( ! $this->file ) {
			throw new Exception('Sitemap file '.$file.' is not writable');
			return false;
		}
		
		fwrite($this->file, '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL);
		fwrite($this->file, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL);
		
		return true;
	}
	
	private function closeFile()
	{
		if ( $this->file ) {
			fwrite($this->file, "</urlset>");
			fclose($this->file);
		}
		 
		return true;
	}
	
	private function writeUrl($url)
	{
		fwrite($this->file,  "\t".'<url>'."\n".
			   "\t\t".'<loc>'.$url['loc'].'</loc>'."\n".
			   "\t\t".'<priority>'.$url['priority'].'</priority>'."\n".
			   ( $url['lastmod'] ? "\t\t".'<lastmod>'.$url['lastmod'].'</lastmod>'."\n" : '' ).
			   "\t".'</url>'."\n");
	}


}

	
// make sure we don't time out
error_reporting(E_ALL);
set_time_limit(0);	

require_once (dirname(__FILE__).'/../app/Mage.php');
Mage::app();

$sitemap_file = dirname(__FILE__).'/../sitemap.xml';

$page_priority = '1';
$category_priority = '0.5';
$product_priority = '0.5';
   	
try {

	$sitemap = new PT_Magento_Sitemap($sitemap_file);
	
	$collection = Mage::getModel('cms/page')
						->getCollection()
						->addStoreFilter(Mage::app()->getStore()->getId())
						->addFieldToFilter('is_active',1);
						
	foreach ( $collection as $page ) {
		$sitemap->addUrl(Mage::getBaseUrl().$page->getIdentifier(), $page_priority, $page->getUpdateTime());
	}
	
	unset($collection);

	
	$collection = Mage::getModel('catalog/category')
				        ->getCollection()
				        ->addAttributeToSelect('*')
				        ->addIsActiveFilter();
				        
	foreach ( $collection as $category ) {
		$sitemap->addUrl($category->getUrl(), $category_priority, $category->getUpdatedAt());
	}
	
	unset($collection);

	$collection = Mage::getModel('catalog/product')
					->getCollection()
					->addAttributeToSelect('*')
					->addAttributeToFilter('status', Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
					->addAttributeToFilter('visibility', array(
						Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,															Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
					));
					
	foreach ( $collection as $product ) {
		$sitemap->addUrl($product->getProductUrl(), $product_priority, $product->getUpdatedAt());
	}
	
	unset($collection);
		
	// Generate and write the sitemap.
	$sitemap->generate();


} catch( Exception $e ) {

	die($e->getMessage());
	
}
