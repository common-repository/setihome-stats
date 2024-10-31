<?php

/*
Plugin Name: SETI@home Stats
Plugin URI: http://www.trassare.com/setihome-stats/
Description: This plugin displays a user's current SETI@home stats anywhere on Wordpress.  A widget is included to easily display stats in a sidebar.
Version: 1.2.0rc1
Author: Samuel T. Trassare
Author URI: http://www.trassare.com
Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
Text Domain: setihome-stats
Domain Path: /languages

Copyright 2017 Samuel T. Trassare (http://www.trassare.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('SETI_DEBUG', false);
define('SETI_FILE', ABSPATH . 'wp-content/plugins/setihome-stats/seti_cache.txt');
define('SETI_RSPNS', ABSPATH . 'wp-content/plugins/setihome-stats/seti_response.txt');
define('RQST_HDR', ABSPATH . 'wp-content/plugins/setihome-stats/rqst_header.txt');

function seti_init()
{
    load_plugin_textdomain('setihome-stats', 'wp-content/plugins/setihome-stats');
    
    /* 
    Do some first-run checks here.  If user hasn't been to the options page the 
    account number and cache expiration values won't be set.
    */
    $acctNo     = get_option('seti_acct');
    $notoptions = wp_cache_get('notoptions', 'options');
    if (isset($notoptions['seti_acct'])) {
        update_option('seti_acct', 0);
    } else {
        define('SETI_ACCT', $acctNo);
    }
    
    $cacheExpry = get_option('seti_expy');
    $notoptions = wp_cache_get('notoptions', 'options');
    if (isset($notoptions['seti_expy'])) {
        update_option('seti_expy', 4);
    } else {
        define('SETI_EXPY', $cacheExpry);
    }
}

add_action('init', 'seti_init');

function get_seti_stats()
{
    
    // Check for the existance of the cache file.  if it's not there, create it by reading the SETI site.
    if (!file_exists(SETI_FILE)) {
        read_seti_site();
    }
    
    // The cache file exist so open it and check its expiration value.
    $fh     = fopen(SETI_FILE, 'r');
    $expiry = fread($fh, 10);
    fclose($fh);
    
    // Get the write time from the cache file and do a date compare.
    $today = mktime(date("H"), 0, 0, date("m"), date("d"), date("y"));
    
    // During debug read the SETI site with each test cycle.
    if (SETI_DEBUG || ($expiry < $today)) {
        // The cach is expired so get fresh data.
        read_seti_site();
    }
    
    // Read data from cache.
    $fg  = fopen(SETI_FILE, 'r');
    $out = fread($fg, filesize(SETI_FILE));
    fclose($fg);
    
    // Output.
    $out = substr($out, 10, strlen($out));
    echo $out;
}

function read_seti_site()
{
    $host      = 'setiathome.berkeley.edu';
    $path      = '/show_user.php?userid=' . SETI_ACCT;
    if(SETI_DEBUG) {
	$host = 'trassare.com';
	// Instead of OK.html you can also try maintenance.html and no_such_user.html.
	$path = '/seti-test-pages/OK.html';
    }
    $seti_logo = get_settings('home') . '/wp-content/plugins/setihome-stats/seti_button.png';
    $seti_url  = 'http://setiathome.berkeley.edu';
    
    $res = "GET $path HTTP/1.0\r\n";
    $res .= "Host: $host\r\n";
    $res .= "User-Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
    $res .= "Accept-Language: en-US\r\n";
    $res .= "Content-Type: application/x-www-form-urlencoded\r\n";
    $res .= "Content-Length: 0\r\n";
    $res .= "Connection: close\r\n\r\n";
    
    $fp = @fsockopen($host, 80, $errno, $errstr);
    
    if (!$fp) {
        echo "<ul>\n" . "  <li> Error: $errno </li>\n" . "  <li> Descr: $errstr </li>\n" . "</ul>\n";
        return;
    } else {
        // Write the above HTML header to the socket.
        fputs($fp, $res, strlen($res));
        // Now get the response.
        while (!feof($fp)) {
            $line .= fgets($fp);
            // $line now contains the entire contents of the response from the SETI website.
        }
    }
    
    fclose($fp);
    
    // debug: What did we send?  What did we get back?
    if (SETI_DEBUG) {
        $fh = fopen(RQST_HDR, 'w+') or die("can't open file");
        fwrite($fh, $res);
        fclose($fh);
        
        $fh = fopen(SETI_RSPNS, 'w+') or die("can't open file");
        fwrite($fh, $line);
        fclose($fh);
    }
    
    
    
    
    // Parse $line and write the values we want to display to the cache file.
    // $line may report valid data, a maintenance page, or an invalid user number page.
    if (strpos($line, 'User ID') && !strpos($line, 'maintenance')) {
        // Set the expiration time for the cache file.
        $expire     = mktime(date("H") + SETI_EXPY, 0, 0, date("m"), date("d"), date("y"));
        $stringData = $expire;
        
        // Member name.
        $regexp = '<h2>(.*)<\/h2>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberName = $matches[1];
        }
        
        // Member's badge, if any.
        $regexp = 'Top (.*) in average credit';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberBadge = $matches[1];
        }
        
        // Member since.
        $regexp = 'member since<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberSince = $matches[1];
        }
        
        // Member Country.
        $regexp = 'Country<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberCountry = $matches[1];
        }
        
        // Member URL.
        $regexp = 'URL<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberUrl = $matches[1];
        }
        
        // Member total credit.
        $regexp = 'Total credit<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberTotalCredit = $matches[1];
        }
        
        // Member recent average credit.
        $regexp = 'Recent average credit<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberRecentAverageCredit = $matches[1];
        }
        
        // Member classic workunits.
        $regexp = 'home classic workunits<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberClassicWorkunits = $matches[1];
        }
        
        // Member classic cpu time.
        $regexp = 'home classic CPU time<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberClassicCpuTime = $matches[1];
        }
        
        // Member team.
        $regexp = 'Team<\/td>[ \r\n]+<td style="padding-left:12px" >(.*)<\/td>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $memberTeam = $matches[1];
        }
        
        $regexp = 'Generated (.*)<\/small>';
        if (preg_match("/$regexp/siU", $line, $matches)) {
            $genTime = $matches[1];
        }
        
        $stringData .= "<p><strong>$memberName</strong></p>\n<ul>\n";
        
        if ($memberBadge) {
            $stringData .= "  <li>";
            // The following string should be translated.
            $stringData .= sprintf(__('Top %s in average credit', setihome - stats), $memberBadge);
            $stringData .= "</li>\n";
        }
        
        $stringData .= "<li>";
        // The following string should be translated.
        $stringData .= sprintf(__('Member since %s', 'setihome-stats'), $memberSince);
        $stringData .= "</li>\n";
        
        $stringData .= "  <li>";
        // The following string should be translated.
        $stringData .= sprintf(__('Country: %s', 'setihome-stats'), $memberCountry);
        $stringData .= "</li>\n";
        
        if ($memberUrl) {
            $stringData .= "  <li>";
            // The following string should be translated. It is the definition of the acronym, "URL."
            $stringData .= sprintf(__('<abbr title="Uniform Resource Locator">URL</abbr>: %s', setihome - stats), $memberUrl);
            $stringData .= "</li>\n";
        }
        
        $stringData .= "  <li>";
        // The following string should be translated.
        $stringData .= sprintf(__('Total credit: %s', 'setihome-stats'), $memberTotalCredit);
        $stringData .= "</li>\n";
        
        $stringData .= "   <li>";
        // The following string should be translated. This is the definition of the acronym, "RAC."
        $stringData .= sprintf(__('<abbr title="Recent Average Credit">RAC</abbr>: %s', setihome - stats), $memberRecentAverageCredit);
        $stringData .= "</li>\n";
        
        if ($memberClassicWorkunits) {
            $stringData .= "   <li>";
            $stringData .= sprintf(__('Classic workunits: %s', 'setihome-stats'), $memberClassicWorkunits);
            $stringData .= "</li>\n";
        }
        
        if ($memberClassicCpuTime) {
            $stringData .= "   <li>";
            // The following string should be translated.
            $stringData .= sprintf(__('Classic <abbr title="Central Processing Unit">CPU</abbr> time: %s', 'setihome-stats'), $memberClassicCpuTime);
            $stringData .= "</li>\n";
        }
        
        if ($memberTeam != "None") {
            $stringData .= "  <li>";
            $stringData .= sprintf(__('Team: %s', 'setihome-stats'), $memberTeam);
            $stringData .= "</li>\n";
        }
        
        $stringData .= "<li>";
        $stringData .= sprintf(__('S@h Status: <p style="display: inline; color: green;">online</p>', setihome - stats));
        $stringData .= "</li>\n";
        
        $stringData .= "<li>";
        $stringData .= sprintf(__('As of %s', 'setihome-stats'), $genTime);
        $stringData .= "</li>\n";
        
        $stringData .= "</ul>\n";
        
    } else if (strpos($line, 'maintenance')) {
        // Edit the cache to reflect that the SETI@home site is down.
        if (!file_exists(SETI_FILE)) {
            $stringData .= default_message();
        } else {
            edit_cache();
        }
    } else if (strpos($line, 'No such user')) {
        
        $stringData .= default_message();
    }
    
    $stringData .= "<div style=\"border-top: 1px black solid; text-align: center; padding-top: 2px; vertical-align: top;\">";
    $stringData .= "<a href=\"$seti_url\" title=\"SETI@home\"><img style=\"border-style: none;\" src=\"$seti_logo\" alt=\"";
    /* translators: This is the alt text for the picture of the SETI@home logo. */
    $stringData .= sprintf(__('Visit SETI@home', setihome - stats));
    $stringData .= "\" /></a></div>\n";
    
    // Write to cache.
    $fh = fopen(SETI_FILE, 'w+') or die("can't open file");
    fwrite($fh, $stringData);
    fclose($fh);
}

/**
 * This function will not edit the cache timestamp.  
 * This means that the SETI@home website will be continously 
 * queried until it comes back online.
 */
function edit_cache()
{
    $fg  = fopen(SETI_FILE, 'r');
    $buf = fread($fg, filesize(SETI_FILE));
    fclose($fg);
    
    $buf = str_ireplace("color: green", "color: red", $buf);
    // Here the online/offline status of the S@h project must be updated in the plugin.
    $buf = str_ireplace(__('online', setihome - stats), __('offline', setihome - stats), $buf);
    
    $fh = fopen(SETI_FILE, 'w') or die("can't open file");
    fwrite($fh, $buf);
    fclose($fh);
}


/**
 * Default message displayed when a user hasn't configured their account number or if the plugin is run for the first time while the 
 * SETI site is in maintenance.
 */
function default_message()
{
    // Set a fake cache expiration.
    $defaultMessage = "0000000000";
    
    $defaultMessage .= "<div style=\"text-align: center; padding-bottom: 2px;\">";
    /* translators: This is the default message that is displayed if a user hasn't yet configured their user ID in the plugin settings. */
    $defaultMessage .= sprintf(__('I contribute CPU time to SETI@home.', 'setihome-stats'));
    $defaultMessage .= "</div>";
    
    return $defaultMessage;
}

/** filters**/
add_action('admin_menu', 'seti_add_options_page');

function seti_add_options_page()
{
    add_options_page('SETI@home Stats', 'SETI@home Stats', 'manage_options', 'setihome-stats/options-seti.php');
    
    $expire = mktime(date("H") - SETI_EXPY, 0, 0, date("m"), date("d"), date("y"));
}

function widget_init_SetiStatsDisplay()
{
    // Check for required functions
    if (!function_exists('register_sidebar_widget'))
        return;
    
    function widget_SetiStatsDisplay($args)
    {
        extract($args);
        if (function_exists('get_seti_stats')) {
            echo $before_widget;
            echo $before_title . __('SETI@home Stats', 'setihome-stats') . $after_title;
            get_seti_stats();
            echo $after_widget;
        }
    }
    register_sidebar_widget(__('SETI@home Stats'), 'widget_SetiStatsDisplay');
}

// Delay plugin execution until sidebar is loaded
add_action('widgets_init', 'widget_init_SetiStatsDisplay');

?>
