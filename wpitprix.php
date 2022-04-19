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
include_once plugin_dir_path( __FILE__ ) . 'inc/search_product_by_attribute.php';
include_once plugin_dir_path( __FILE__ ) . 'inc/sessioncheckout.php';
include_once plugin_dir_path( __FILE__ ) . 'inc/custominput.php';
include_once plugin_dir_path( __FILE__ ) . 'inc/registerfields.php';
/**
 * @WPitprix plaguin
 */
class WPitprix
{

	public function run()
	{
		new SearchProductByAttribute();
		new SessionCheckout();
        new CustomInput();
        new RegisterFields();
	}
}




// inetiate the class   

if (class_exists('WPitprix')) {
	$wpitprix = new WPitprix();
	$wpitprix->run();
}
