<div class="wrap">
    <?php screen_icon(); ?>
    <h2>Bitly External URL Plugin Configuration</h2>
    <?php if (isset($_GET['msg']) && !empty($_GET['msg'])): ?>
        <div id="s3plugin-settings_updated" class="updated settings-error"> 
    	<p><strong><?php echo $_GET['msg']; ?></strong></p>
        </div>
    <?php endif; ?>
    <form method="post" action="">
	<table class="form-table">
	    <tr valign="top">
		<th scope="row"><label for="bitly_external_plugin_username"><?php _e('Bitly Username') ?></label></th>
		<td>
		    <input name="bitly_external_plugin_username" type="text" id="bitly_external_plugin_username" value="<?php form_option('bitly_external_plugin_username'); ?>" class="regular-text" />
		</td>
	    </tr>
	    <tr valign="top">
		<th scope="row"><label for="bitly_external_plugin_api_key"><?php _e('API Key') ?></label></th>
		<td><input name="bitly_external_plugin_api_key" type="text" id="bitly_external_plugin_api_key" value="<?php form_option('bitly_external_plugin_api_key'); ?>" class="regular-text" /></td>
	    </tr>
	</table>
	<p class="submit">
	    <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
	</p>
    </form>
    <p>If you find this plugin usefull please donate few dollars ;-)</p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="7TQZ679T4NDG6">
	<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
</div>
