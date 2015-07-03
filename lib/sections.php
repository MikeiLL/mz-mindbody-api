<?php
add_action ('admin_menu', 'mz_mindbody_settings_menu');

	function mz_mindbody_settings_menu() {
		//create submenu under Settings
		add_options_page ('MZ Mindbody Settings', esc_attr__('MZ Mindbody', 'mz-mindbody-api'),
		'manage_options', __FILE__, 'mz_mindbody_settings_page');
	}

	function mz_mindbody_settings_page() {
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<form action="options.php" method="post">
				<?php settings_fields('mz_mindbody_options'); ?>
				<?php do_settings_sections('mz_mindbody'); ?>
				<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
		<?php
	}

	// Register and define the settings
	add_action('admin_init', 'mz_mindbody_admin_init');

	function mz_mindbody_admin_init(){
		register_setting(
			'mz_mindbody_options',
			'mz_mindbody_options',
			'mz_mindbody_validate_options'
		);

		add_settings_section(
			'mz_mindbody_server',
			'MZ Mindbody Server',
			'mz_mindbody_server_check',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_section2_text',
			'',
			'mz_mindbody_section2_text',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_section4_text',
			'',
			'mz_mindbody_section4_text',
			'mz_mindbody'
		);
		
		add_settings_section(
			'mz_mindbody_main',
			__('MZ Mindbody Credentials', 'mz-mindbody-api'),
			'mz_mindbody_section_text',
			'mz_mindbody'
		);

		
		add_settings_field(
			'mz_mindbody_source_name',
			__('Source Name: ', 'mz-mindbody-api'),
			'mz_mindbody_source_name',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_password',
			__('Key: ', 'mz-mindbody-api'),
			'mz_mindbody_password',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_siteID',
			__('Site ID: ', 'mz-mindbody-api'),
			'mz_mindbody_siteID',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_field(
			'mz_mindbody_eventID',
			__('Event IDs: ', 'mz-mindbody-api'),
			'mz_mindbody_eventID',
			'mz_mindbody',
			'mz_mindbody_main'
		);
		
		add_settings_field(
			'mz_mindbody_eventsDuration',
			__('Event Schedule Duration', 'mz-mindbody-api'),
			'mz_mindbody_eventsDuration',
			'mz_mindbody',
			'mz_mindbody_main'
		);
		
		add_settings_section(
			'mz_mindbody_section3_text',
			'',
			'mz_mindbody_section3_text',
			'mz_mindbody'
		);

		add_settings_field(
			'mz_mindbody_clear_cache',
			__('Force Cache Reset ', 'mz-mindbody-api'),
			'mz_mindbody_clear_cache',
			'mz_mindbody',
			'mz_mindbody_main'
		);

		add_settings_section(
			'mz_mindbody_secondary',
			__('Debug', 'mz-mindbody-api'),
			'mz_mindbody_debug_text',
			'mz_mindbody'
		);
	}

	// Draw the section header
	function mz_mindbody_server_check() {
		$mz_requirements = 0;
		if (version_compare(phpversion(), '5.3.10', '<')) {
			echo '<h2>';
    		_e('Sorry but this plugin requires php version 5.3.10 or greater.'); // php version isn't high enough
    		echo '</h2>';
    		die();
		}
		require_once 'System.php';

		if (extension_loaded('soap'))
		{
			_e( 'SOAP installed! ', 'mz-mindbody-api');
		}
		else
		{
		   _e('SOAP is not installed. ', 'mz-mindbody-api');
		   $mz_requirements = 1;
		}
		echo '&nbsp;';
		if (class_exists('System')===true)
		{
		   _e('PEAR installed! ', 'mz-mindbody-api');
		}
		else
		{
		   _e('PEAR is not installed. ', 'mz-mindbody-api');
		   $mz_requirements = 1;
		}

		if ($mz_requirements == 1)
		{
			echo '<div class="settings-error" style="max-width:60%"><p>';
			_e('MZ Mindbody API requires SOAP and PEAR. Please contact your hosting provider or enable via your CPANEL of php.ini file.', 'mz-mindbody-api');
			echo '</p></div>';
		}
		else
		{
			
			echo '<div class="updated" style="max-width:60%"><p>';
			_e('Congratulations. Your server appears to be configured to integrate with mindbodyonline.', 'mz-mindbody-api');
			echo '</p></div>';
		}
	}

	function mz_mindbody_section_text() { 
		$globals = new Global_Strings();
		$global_strings = $globals->translate_them();
		$password = $global_strings['password'];
		$login_url = $global_strings['login_url'];
		$logout_url = $global_strings['logout_url'];
		$create_account_url = $global_strings['create_account_url'];

		?>
		<div style="max-width:60%">
		<p><?php _e('Enter your mindbody credentials below.', 'mz-mindbody-api') ?></p>
		<p><?php printf(__('If you do not have them yet, visit the %1$s MindBodyOnline developers website %2$s 
		and register for developer credentials.', 'mz-mindbody-api'),
		 '<a href="https://api.mindbodyonline.com/Home/LogIn">', '</a>')?>
		(<a href="http://www.mzoo.org/creating-your-mindbody-credentials/"><?php _e('Detailed instructions here', 'mz-mindbody-api') ?></a>.)</p>
		<h3>Shortcodes</h3>
		<p>
		<?php _e('Add to page or post with shortcode:', 'mz-mindbody-api'); 
		echo '&nbsp;';
		printf('[%1$s], [%2$s], [%3$s], [%4$s %5$s=%6$s %7$s=1 %8$s=-99]',
		'mz_mindbody_show_schedule', 'mz_mindbody_show_events', 'mz_mindbody_staff_list',
		'mz_mindbody_show_schedule', 'type', 'day', 'location', 'account'); 
		echo '<br/>('.__('-99 is the MBO sandbox/testing account', 'mz-mindbody-api').')</font></p>';
		echo '<p>';
		echo __('Grid and Filter can be added like this:', 'mz-mindbody-api').'<br/>';
		printf('[%1$s %2$s=1 %3$s=1]<br/>',
		'mz-mindbody-show-schedule', 'grid', 'filter');
		
		echo '</p>';
		echo '<p>' . __('To remove hide any of the following elements from grid calendar:', 'mz-mindbody-api') . 
		'&nbsp; hide="teacher, signup, duration"'
		
		?>
		
		<p><?php _e('Additional shortcodes:', 'mz-mindbody-api'); 
		echo '&nbsp;[mz-mindbody-signup], [mz-mindbody-login], [mz-mindbody-logout]';
		?></p>

		<p><?php _e('In order for these to work correctly, the permalinks for those pages need to be:', 'mz-mindbody-api');
		echo '&nbsp;<em>create-account</em>, <em>login</em> and <em>logout</em>.'; ?>

		</div>
	<?php
	
	}

	function mz_mindbody_section2_text() {
	?><div style="float:right;width:150px;background:#CCCCFF;padding:5px 20px 20px 20px;margin-left:20px;margin-bottom:8px;">
	<h4><?php _e('Contact', 'mz-mindbody-api')?></h4>
	<p><a href="http://www.mzoo.org">www.mzoo.org</a></p>
	<p><div class="dashicons dashicons-email-alt" alt="f466"></div> 
	<?php printf(__('Email welcome, but please also post in the %1$s support forum %2$s for the benefit of others.', 'mz-mindbody-api'),
	'<a href="https://wordpress.org/support/plugin/mz-mindbody-api">', '</a>')?></p>
	<p><div class="dashicons dashicons-heart" alt="f487" style="color:red;"></div>
	
	<?php printf(__('%1$s Small donations %2$s and %3$s reviews %4$s welcome.', 'mz-mindbody-api'),
	'<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=A95ZEELLHGECE" target="_blank">','</a>', '<a href="https://wordpress.org/support/view/plugin-reviews/mz-mindbody-api">','</a>'); ?> </p>

	</div>
	<br style='clear:right;'/>
	<?php
	}
	
	function mz_mindbody_section4_text() {
	?><div style="float:right;width:150px;background:#CCCCFF;padding:5px 20px 20px 20px;margin-left:20px;">
	<h4><i class="dashicons dashicons-megaphone" alt="f488" style="max-width:90%"></i> <?php _e('News', 'mz-mindbody-api')?></h4>
	<p><?php _e('Now supports multiple locations and MBO accounts.', 'mz-mindbody-api')?><p>
	<hr/>
	<h4><?php _e("Customization requests invited, and there's an ADVANCED VERSION of the plugin which integrates MBO class registration without leaving the WP site.", 'mz-mindbody-api')?>
	</h4>
	</div>
	<?php
	}
	
		add_action( 'wp_footer', 'mz_mindbody_debug_text' );
	function mz_mindbody_debug_text() {
	  require_once MZ_MINDBODY_SCHEDULE_DIR .'mindbody-php-api/MB_API.php';
	  require_once MZ_MINDBODY_SCHEDULE_DIR .'inc/mz_mbo_init.inc';
	  echo "<p>";
	  printf(__('Once credentials have been set and activated, look for %1$s in the 
	  GetClassesResponse box below to confirm settings are correct.',  'mz-mindbody-api'),
	  '<code>&lt;ErrorCode&gt;200&lt;/ErrorCode&gt;</code>');
	  echo "</p>";
	  $mz_timeframe = array_slice(mz_getDateRange(date_i18n('Y-m-d'), 1), 0, 1);
	  $test = $mb->GetClasses($mz_timeframe);
	  $mb->debug();
	  echo "<br/>";
	}

	// Display and fill the form field
	function mz_mindbody_source_name() {
		// get option 'mz_source_name' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
		$mz_source_name = (isset($options['mz_source_name'])) ? $options['mz_source_name'] : __('YOUR SOURCE NAME', 'mz-mindbody-api');
		// echo the field
		echo "<input id='mz_source_name' name='mz_mindbody_options[mz_source_name]' type='text' value='$mz_source_name' />";
	}

	// Display and fill the form field
	function mz_mindbody_password() {
		$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
		$mz_mindbody_password = (isset($options['mz_mindbody_password'])) ? $options['mz_mindbody_password'] : __('YOUR MINDBODY PASSWORD', 'mz-mindbody-api');
		// echo the field
		echo "<input id='mz_mindbody_password' name='mz_mindbody_options[mz_mindbody_password]' type='text' value='$mz_mindbody_password' />";
	}

	// Display and fill the form field
	function mz_mindbody_siteID() {
		// get option 'text_string' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
		$mz_mindbody_siteID = (isset($options['mz_mindbody_siteID'])) ? $options['mz_mindbody_siteID'] : __('YOUR SITE ID', 'mz-mindbody-api');
		// echo the field
		echo "<input id='mz_mindbody_siteID' name='mz_mindbody_options[mz_mindbody_siteID]' type='text' value='$mz_mindbody_siteID' />";
	}

	// Display and fill the form field
	function mz_mindbody_eventID() {
		// get option 'text_string' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
		$mz_mindbody_eventID = (isset($options['mz_mindbody_eventID'])) ? $options['mz_mindbody_eventID'] : __('Event Category IDs');
		// echo the field
		echo "<input id='mz_mindbody_eventID' name='mz_mindbody_options[mz_mindbody_eventID]' type='text' value='$mz_mindbody_eventID' />  eg: 25,17";
	}
	
	function mz_mindbody_eventsDuration() {
		// get option 'text_string' value from the database
		$options = get_option( 'mz_mindbody_options',__('Option Not Set', 'mz-mindbody-api') );
		$mz_mindbody_eventsDuration = (isset($options['mz_mindbody_eventsDuration'])) ? $options['mz_mindbody_eventsDuration'] : '60';
		// echo the field
		echo "<input id='mz_mindbody_eventsDuration' name='mz_mindbody_options[mz_mindbody_eventsDuration]' type='text' value='$mz_mindbody_eventsDuration' />";
	}

	function mz_mindbody_section3_text() {
		_e('Having this checked will allow you to see immediate changes in MBO', 'mz-mindbody-api');
		echo "<br/>";
		_e('but may end up costing more in API transfer fees.', 'mz-mindbody-api');
		echo "<br/>";
		_e('Class calendar cache is held for 1 day. Event calendar for 1 hour.', 'mz-mindbody-api');
		}
		
	// Display and fill the cache reset form field
	function mz_mindbody_clear_cache() {
		$options = get_option( 'mz_mindbody_options','Option Not Set' );
		printf(
	    '<input id="%1$s" name="mz_mindbody_options[%1$s]" type="checkbox" %2$s />',
	    'mz_mindbody_clear_cache',
	    checked( isset($options['mz_mindbody_clear_cache']) , true, false )
		);
	}

	// Validate user input (we want text only)
	function mz_mindbody_validate_options( $input ) {
	    foreach ($input as $key => $value)
	    {
				$valid[$key] = wp_strip_all_tags(preg_replace( '/\s/', '', $input[$key] ));
				if( $valid[$key] != $input[$key] )
				{
					add_settings_error(
						'mz_mindbody_text_string',
						'mz_mindbody_texterror',
						'Does not appear to be valid ',
						'error'
					);
				}
			}

		return $valid;
	}
?>