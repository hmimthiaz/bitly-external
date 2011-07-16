<?php

/*
  Plugin Name: Wordpress Bit.ly External URLS
  Plugin URI: http://imthi.com/wp-s3/
  Description: This plugin helps all the external URLS to bit.ly links
  Author: Imthiaz Rafiq
  Version: 1.1 Alpha
  Author URI: http://imthi.com/
 */

class IRBitlyExternal {

    /**
     *
     * @var IRBitlyExternal 
     */
    protected static $_instance = null;

    private function __clone() {
	
    }

    /**
     * Singleton instance
     *
     * @return IRBitlyExternal
     */
    public static function getInstance() {
	if (null === self::$_instance) {
	    self::$_instance = new self();
	}
	return self::$_instance;
    }

    function __construct() {
	register_activation_hook(plugin_basename(__FILE__), array(
	    &$this,
	    'activatePlugin'));
	register_deactivation_hook(plugin_basename(__FILE__), array(
	    &$this,
	    'deactivatePlugin'));
	add_action('admin_menu', array(&$this, 'adminMenu'));
	if (isset($_GET ['page']) && $_GET ['page'] == 'bitlyexternal-options') {
	    ob_start();
	}	
	
    }

    function adminMenu() {
	if (function_exists('add_submenu_page')) {
	    add_submenu_page('plugins.php', __('Bit.ly External'), __('Bit.ly External'), 'manage_options', 'bitlyexternal-options', array(
		&$this,
		'pluginOption'));
	}
    }
    
    function pluginOption() {
	if (isset($_POST ['Submit'])) {
	    if (function_exists('current_user_can') && !current_user_can('manage_options')) {
		die(__('Cheatin&#8217; uh?'));
	    }
	    update_option('bitly_external_plugin_username', $_POST ['bitly_external_plugin_username']);
	    update_option('bitly_external_plugin_api_key', $_POST ['bitly_external_plugin_api_key']);
	    ob_end_clean();
	    wp_redirect('plugins.php?page=bitlyexternal-options&msg=' . urlencode("Setting saved"));
	    exit();
	}
	include_once ('plugin-options.php');
    }    

    function activatePlugin() {
	$query = "CREATE TABLE IF NOT EXISTS `{$this->tabeImageQueue}` (
		  `id` varchar(32) NOT NULL,
		  `path` varchar(255) NOT NULL,
		  `status` enum('queue','done','error') NOT NULL,
		  `added` datetime NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=MyISAM;";
	$this->db->query($query);
    }

    function deactivatePlugin() {
	
    }

}

$bitlyExternal = IRBitlyExternal::getInstance();