<?php


/*
Author:  Samuel T. Trassare
Author URI: http://www.trassare.com
Description: Manages the options for the SETI@home Stats plugin.
Version: 1.2.0rc1
Text Domain: setihome-stats
Domain Path: /languages
*/

/* Create Default Account Number if it Doesn't Exist */
add_option('seti_acct', '0');
add_option('seti_expy', '4');

$location = get_option('siteurl') . '/wp-admin/admin.php?page=setihome-stats/options-seti.php'; // Form Action URI

/* check form submission and update options */
if ('process' == $_POST['stage']) {
	update_option('seti_acct', $_POST['seti_acct']);
	update_option('seti_expy', $_POST['seti_expy']);
}

/* Get options for form fields */
$seti_acct = get_option('seti_acct');
$seti_expy = get_option('seti_expy');

function seti_plugin_menu() {

  add_options_page(__('SETI@home Stats', 'setihome-stats'),
	__('SETI@home Stats', 'setihome-stats'),
	'manage_options',
	'my-unique-identifier',
	'seti_plugin_options');

}
?>

<form name="seti"></form>
<div class="wrap">
  <h2><?php _e('SETI@home Stats', 'setihome-stats') ?></h2>
  <form id="seti_form" name="form1" method="post" action="<?php echo $location ?>&amp;updated=true">
  	<input type="hidden" name="stage" value="process" />

  	<fieldset class="options">
  		<legend><?php _e('Personal Settings', 'setihome-stats') ?></legend>
  		<table width="100%" cellpadding="5" class="editform">
  			<tr>
					<td><label for="seti_acct"><?php _e('Account Number:', 'setihome-stats'); ?></label></td>
					<td><input style="width: 200px;" id="seti_acct" name="seti_acct" type="text" value="<?php echo $seti_acct; ?>" /></td>
				</tr>
  			<tr>
					<td><label for="seti_expy"><?php _e('Refresh (in hours): ', 'setihome-stats'); ?></label></td>
					<td><input style="width: 50px;" id="seti_expy" name="seti_expy" type="text" value="<?php echo $seti_expy; ?>" /></td>
				</tr>
			</table>
    </fieldset>
    
    <p class="submit">
      <input type="submit" name="Submit" value="<?php _e('Update', 'setihome-stats') ?> &raquo;" />
    </p>
  </form>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<p><?php _e('I hope you have found this plugin useful and entertaining.  If so, please consider a donation to encourage the ongoing development of the SETI@home Stats plugin for Wordpress.  Even a mere US$1 would help.', 'setihome-stats'); ?></p>
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="839001">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e('The PayPal donation button.', 'setihome-stats'); ?>">
<img alt="<?php _e('The PayPal tracking pixel.', 'setihome-stats'); ?>" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

</div>
