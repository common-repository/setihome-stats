<?php


/*
Author:  Samuel T. Trassare
Author URI: http://www.trassare.com
Description: Uninstall operations for SETI@home Stats.
Version: 1.2.0rc1
Text Domain: setihome-stats
Domain Path: /languages
*/

if(!defined('WP_UNINSTALL_PLUGIN')){
	die;
}

delete_option('seti_acct');
delete_option('seti_expy');

?>
