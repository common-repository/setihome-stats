=== SETI@home Stats ===
Contributors:  tiogaplanet
Donate link: http://www.trassare.com/setihome-stats/
Requires at Least: 4.6 
Tested Up To:  WordPress 4.8.2
Tags:  SETI@home, SETI, Stats, widget
Stable tag: 1.1.9

== Description ==
This plugin shows the current SETI@home statistics for a given account.  It includes a widget to easily display stats in a sidebar.

== Installation ==
* Upload the contents of the zip file to your "wp-content/plugins" directory. 
* Ensure the "setihome-stats" plugin directory has write permissions (used for file cache).
* Go to the "Plugins" main menu and find "SETI@home Stats", then click "Activate".
* Go to the "Settings->SETI@home Stats" menu and enter your account ID and refresh interval.

== Usage ==
After activating the plugin you can then activate the included widget to display your stats on the sidebar.

The following snippet can be placed anywhere else you want your stats displayed.
<code>
get_seti_stats(); 
</code>

== Frequently Asked Questions ==
= I installed the plugin, but I get a "can't open file" message. What's going on? = 
 This is probably because the cache file cannot be created.  Ensure the "setihome-stats" plugin directory has write permissions (e.g. chmod 777).

== Screenshots ==
1. This screenshot shows SETI@home Stats running on the Get Some! theme.
2. This screenshot shows SETI@home Stats running on the Suffusion theme.

== Un-Installation ==
* Go to the "Plugins" main menu and find "SETI@home Stats Widget", then click "Deactivate".
* Repeat the above step for the "SETI@home Stats" plugin.
* Delete the "setihome-stats" directory from your "wp-content/plugins" directory.

== Changelog ==
* 0.1 - Initial Release (21 March 2007)
* 0.2 - Added Caching Support (22 March 2007)
* 1.0 - Changed the source of the data, added new fields and added a widget. (28 October 2008)
* 1.0.1 - Fixed some path issues and added i18n support. (03 November 2008)
* 1.0.2 - Fixed the bug associated with deleting the cache contents if the SETI site is down longer than the refresh interval.
* 1.0.3 - Changed all URL's to reflect the new port number 8080. 
* 1.0.4 - Changed all URL's back to default port (25 May 2009).
* 1.0.5 - Changed 'admin_head' to 'admin_menu' in the add_action() call in setistats.php. Verified compatibility with WordPress 3.0.1. (15 August 2010).
* 1.1.5 - Regular expression searches updated for the latest SETI@home statistics site. (27 June 2017)
* 1.1.6 Moved the screenshots out of the download package for a smaller zip.  Updated the README.txt to conform to Wordpress guidelines. (28 June 2017)
* 1.1.7 Removed hidden files from the repository and fixed some typos.
* 1.1.8 Added support for badges (25%, 5%, 1% in average credit).
* 1.1.9 Updates to support changes in Wordpress i18n introduced since 4.6.

== Upgrade Notice ==

= 0.2 =
Added caching support to speed up web page load times.

= 1.0 =
Added a widget.

= 1.0.1 =
Added i18n support.

= 1.0.2 =
Bug fixes.

= 1.0.3 =
Bug fixes.

= 1.0.4 =
Bug fixes.

= 1.0.5 =
Tested for WordPress 3.0.1.

= 1.1.5 =
Updates to read from the latest official SETI stats page and test for WordPress 4.8.

= 1.1.6 =
Moved the screenshots out of the download package for a smaller zip.  Updated the README.txt to conform to Wordpress guidelines.

= 1.1.7 =
Package cleanup to reduce file size.

= 1.1.8 =
Added much needed support for badges.

= 1.1.9 =
Wordpress introduced a new i18n system with version 4.6.  This update brings SETI@home Stats up-to-date with the new system.
