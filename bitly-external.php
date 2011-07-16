<?php

/*
  Plugin Name: Wordpress Bit.ly External URLS
  Plugin URI: http://imthi.com/wp-s3/
  Description: This plugin helps the users to view your blog in a pda and iPhone browser.
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
	;
    }

}