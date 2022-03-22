<?php 
/**
 * @package WpItprix
 */


/**
 * Plugin Name: WpItprix
 * Description: itprix official plaguin for headless wordpress
 * Plugin URI: http://itprix.com
 * Author: Subbir Ahamed
 * Author URI: http://itprix.com
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: wpitprix
 * Domain Path: http://itprix.com
 */


// sequre the plaguin from hacker
defined('ABSPATH') or die("error...");
require_once 'includes/search_product_by_attribute.php';
require_once 'includes/sessioncheckout.php';

/**
 * @WPitprix plaguin
 */
class WPitprix
{

	public function run()
	{
		new SearchProductByAttribute();
		new SessionCheckout();
	}
}




// inetiate the class   

if (class_exists('WPitprix')) {
	$wpitprix = new WPitprix();
	$wpitprix->run();
}
