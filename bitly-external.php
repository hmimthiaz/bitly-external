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

    var $bitlyUsername;
    var $bitlyApikey;
    var $enabled;
    var $tableBitlyExternal;
    /**
     *
     * @var IRBitlyExternal 
     */
    protected static $_instance = null;
    /*
     * 
     * @var wpdb
     */
    var $db;

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
	global $wpdb;


	$this->bitlyUsername = get_option('bitly_external_plugin_username');
	$this->bitlyApikey = get_option('bitly_external_plugin_api_key');



	$this->blockedDomains = get_option('bitly_external_plugin_blocked_domains');
	if (!empty($this->blockedDomains)) {
	    $this->blockedDomains = explode(';', $this->blockedDomains);
	}else{
	    $this->blockedDomains = $this->getUrlHostname(get_option('siteurl'));
	    update_option('bitly_external_plugin_blocked_domains', $this->blockedDomains);
	}

	if (!empty($this->bitlyUsername) && !empty($this->bitlyApikey)) {
	    $this->enabled = TRUE;
	} else {
	    $this->enabled = FALSE;
	}

	$this->db = $wpdb;
	$this->tableBitlyExternal = $wpdb->prefix . 'bitly_external';

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
	if ($this->enabled) {
	    if (!function_exists('pq')) {
		include_once 'lib/phpQuery.php';
	    }
	    if (!class_exists('Bitly')) {
		include_once 'lib/bitly.php';
	    }
	    add_filter('the_content', array(&$this, 'theContent'));
	}
    }

    function getUrlHostname($linkurl) {
	return parse_url(strtolower($linkurl), PHP_URL_HOST);
    }

    function theContent($content) {
	$doc = phpQuery::newDocumentHTML($content);
	phpQuery::selectDocument($doc);
	foreach (pq('a') as $link) {
	    $linkurl = pq($link)->attr('href');
	    $linkHostname = $this->getUrlHostname($linkurl);
	    if ($linkHostname != FALSE) {
		if (!in_array($linkHostname, $this->blockedDomains)) {
		    $linkHash = md5($linkurl);
		    $query = "SELECT * FROM {$this->tableBitlyExternal} WHERE id='{$linkHash}'LIMIT 1;";
		    $linkData = $this->db->get_row($query, ARRAY_A);
		    if (empty($linkData)) {
			$bitly = new Bitly($this->bitlyUsername, $this->bitlyApikey);
			$shortURL = $bitly->shorten($linkurl);
			$shortURLData = get_object_vars($bitly->getData());
			if (!empty($shortURLData)) {
			    $linkData = array(
				'id' => $linkHash,
				'url' => $linkurl,
				'short_url' => $shortURLData['shortUrl'],
				'hash' => $shortURLData['userHash'],
				'created' => current_time('mysql'));
			    $this->db->insert($this->tableBitlyExternal, $linkData);
			}
		    }
		    if (!empty($linkData)) {
			pq($link)->attr('href', $linkData['short_url']);
		    }
		}
	    }
	}
	return $doc->htmlOuter();
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
	    update_option('bitly_external_plugin_blocked_domains', $_POST ['bitly_external_plugin_blocked_domains']);
	    ob_end_clean();
	    wp_redirect('plugins.php?page=bitlyexternal-options&msg=' . urlencode("Setting saved"));
	    exit();
	}
	include_once ('plugin-options.php');
    }

    function activatePlugin() {
	$query = "CREATE TABLE IF NOT EXISTS `{$this->tableBitlyExternal}` (
	      `id` varchar(32) NOT NULL,
	      `url` varchar(255) NOT NULL,
	      `short_url` varchar(100) NOT NULL,
	      `hash` varchar(15) NOT NULL,
	      `created` datetime NOT NULL,
	      UNIQUE KEY `id` (`id`));";
	$this->db->query($query);
    }

    function deactivatePlugin() {
	
    }

}

$bitlyExternal = IRBitlyExternal::getInstance();