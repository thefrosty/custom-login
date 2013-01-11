<?php
/**
 * Administration functions for loading and displaying the settings page and saving settings 
 * are handled in this file.
 *
 * @package CustomLogin
 */

/* Initialize the theme admin functionality. */
add_action( 'init', 'custom_login_admin_init' );

/**
 * Initializes the theme administration functions.
 *
 * @since 0.8
 */
function custom_login_admin_init() {	
	add_action( 'admin_init', 'custom_login_scripts' );
	
	add_action( 'admin_init', 'custom_login_styles' );
	
	add_action( 'admin_menu', 'custom_login_settings_page_init' );

	add_action( 'custom_login_update_settings_page', 'custom_login_save_settings' );
}

/**
 * Register the javascript.
 *
 * @since 0.8
 */
function custom_login_scripts() {
	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' );
	
	wp_register_script( 'autosize', CUSTOM_LOGIN_JS . 'jquery.autosize.js', array( 'jquery' ), '1.15.3', false );
	
	wp_register_script( 'custom-login', CUSTOM_LOGIN_JS . 'custom-login.js', array( 'jquery' ), $plugin_data['Version'], false );
	
	wp_register_script( 'jscolor', CUSTOM_LOGIN_JS . 'jscolor.js', false, '1.4.0', false );
	
	wp_register_script( 'gravatar', CUSTOM_LOGIN_JS . 'gravatar.js', array( 'jquery' ), '1.2', false );
}

/**
 * Register the stylesheets.
 *
 * @since 0.8
 */
function custom_login_styles() {	
	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' );
	
	wp_register_style( 'custom-login-tabs', CUSTOM_LOGIN_CSS . 'tabs.css', false, $plugin_data['Version'], 'screen' );
	
	wp_register_style( 'custom-login-admin', CUSTOM_LOGIN_CSS . 'admin.css', false, $plugin_data['Version'], 'screen' );
}

/**
 * Sets up the cleaner gallery settings page and loads the appropriate functions when needed.
 *
 * @since 0.8
 */
function custom_login_settings_page_init() {
	global $custom_login;
	
	$role = 'manage_options';
	$img  = '<div style="width: 16px; height: 16px; overflow: hidden; display: block; float: left;"><img src="' . plugin_dir_url( __FILE__ ) . 'Sprite.jpg" style="background-position: -31px 0 !important" /></div>';
	$img  = '';
	
	/* Create the theme settings page. */
	$custom_login->settings_page = add_options_page( __( 'Custom Login', 'custom-login' ), $img . __( 'Custom Login', 'custom-login' ), $role, 'custom-login', 'custom_login_settings_page' );

	/* Register the default theme settings meta boxes. */
	add_action( "load-{$custom_login->settings_page}", 'custom_login_create_settings_meta_boxes' );

	/* Make sure the settings are saved. */
	add_action( "load-{$custom_login->settings_page}", 'custom_login_load_settings_page' );

	/* Load the JavaScript and stylehsheets needed for the theme settings. */
	add_action( "load-{$custom_login->settings_page}", 'custom_login_settings_page_enqueue_script' );
	add_action( "load-{$custom_login->settings_page}", 'custom_login_settings_page_enqueue_style' );
	
	add_action( "admin_head-{$custom_login->settings_page}", 'custom_login_settings_page_load_scripts' );
}

/**
 * Returns an array with the default plugin settings.
 *
 * @since 0.8
 */
function custom_login_settings() {
	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' );
	
	$settings = array(
		'version' => $plugin_data['Version'],
		/* Activate */
		'custom' => false,
		/* Gravatar */
		'gravatar' => false,
		/* Core */		
		'hide_dashboard' => false,
		'disable_presstrends' => false,
		/* Upgrade */		
		'hide_upgrade' => false,
		'upgrade_complete' => false, //if the upgrade is good, hide it all forever.
		/* Custom css */	
		'custom_css' => '',		
		/* Custom html */	
		'custom_html' => '',	
		/* Custom jQUery */	
		'custom_jquery' => '',	
		/* html */
		'html_border_top_color' => '', //WP < 3.x
		'html_border_top_background' => '', //WP > 3.x
		'html_background_color' => '',
		'html_background_url' => '',
		'html_background_repeat' => 'repeat-x',	
		'html_background_size' => 'cover',	
		/* Login form */
		'login_form_logo' => '',
		'login_form_border_top_color' => '',
		'login_form_background_color' => '',
		'login_form_background' => '',
		'login_form_background_size' => 'cover',	
		'login_form_border_radius' => '11',
		'login_form_border' => '1',
		'login_form_border_color' => '',			
			/* Box Shadows */
			'login_form_box_shadow_1' => '5',
			'login_form_box_shadow_2' => '5',
			'login_form_box_shadow_3' => '18',
			'login_form_box_shadow_4' => '#464646',	
		/* Form Padding */
		'login_form_padding_top' => true,
		/* Label color */
		'label_color' => '#ffffff',
	);
	return apply_filters( 'custom_login_settings', $settings );
}

/**
 * Function run at load time of the settings page, which is useful for hooking save functions into.
 *
 * @since 0.8
 */
function custom_login_load_settings_page() {

	//delete_option( 'custom_login_settings' );
	/* Get theme settings from the database. */
	$settings = get_option( 'custom_login_settings' );
	
	///////////////////////////////////////////////////////////////////////////////////////////////
	// TO BE REMOVED IN VERSION 0.9 //
	/* If the old settings are available, delete the old settings. */
	//if ( !empty( $settings['use_custom'] ) ) {
		//delete_option( 'custom_login_settings' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		//wp_redirect( admin_url( 'options-general.php?page=custom-login' ) );
		//exit;
	//}
	///////////////////////////////////////////////////////////////////////////////////////////////

	/* If no settings are available, add the default settings to the database. */
	if ( empty( $settings ) ) {
		add_option( 'custom_login_settings', custom_login_settings(), '', 'yes' );

		/* Redirect the page so that the settings are reflected on the settings page. */
		wp_redirect( admin_url( 'options-general.php?page=custom-login' ) );
		exit;
	}

	/* If the form has been submitted, check the referer and execute available actions. */
	elseif ( isset( $_POST['custom-login-settings-submit'] ) ) {

		/* Make sure the form is valid. */
		check_admin_referer( 'custom-login-settings-page' );

		/* Available hook for saving settings. */
		do_action( 'custom_login_update_settings_page' );

		/* Redirect the page so that the new settings are reflected on the settings page. */
		wp_redirect( admin_url( 'options-general.php?page=custom-login&updated=true' ) );
		exit;
	}
}


/**
 * Validates the plugin settings.
 *
 * @since 0.8
 */
function custom_login_save_settings() {

	/* Get the current theme settings. */
	$settings = get_option( 'custom_login_settings' );
	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' );

	$settings['version'] = ( ( isset( $_POST['version'] ) ) ? esc_html( $_POST['version'] ) : $plugin_data['Version'] );
	$settings['custom'] = ( ( isset( $_POST['custom'] ) ) ? true : false );
	$settings['gravatar'] = ( ( isset( $_POST['gravatar'] ) ) ? true : false );
	$settings['hide_dashboard'] = ( ( isset( $_POST['hide_dashboard'] ) ) ? true : false );
	$settings['disable_presstrends'] = ( ( isset( $_POST['disable_presstrends'] ) ) ? true : false );
	$settings['hide_upgrade'] = ( ( isset( $_POST['hide_upgrade'] ) ) ? true : false );
	
	$settings['custom_css'] = esc_html( $_POST['custom_css'] );
	$settings['custom_html'] = esc_html( $_POST['custom_html'] );
	$settings['custom_jquery'] = esc_html( $_POST['custom_jquery'] );
	
	$settings['html_border_top_color'] = ( ( isset( $_POST['html_border_top_color'] ) ) ? esc_html( $_POST['html_border_top_color'] ) : '' ); // > 3.0.x
	$settings['html_border_top_background'] = isset( $_POST['html_border_top_background'] ) ? esc_html( $_POST['html_border_top_background'] ) : '';
	$settings['html_background_color'] = esc_html( $_POST['html_background_color'] );
	$settings['html_background_url'] = esc_html( $_POST['html_background_url'] );
	$settings['html_background_repeat'] = esc_attr( $_POST['html_background_repeat'] );
	$settings['html_background_size'] = esc_attr( $_POST['html_background_size'] );
	
	$settings['login_form_logo'] = esc_html( $_POST['login_form_logo'] );
	$settings['login_form_border_top_color'] = ( ( isset( $_POST['login_form_border_top_color'] ) ) ? esc_html( $_POST['login_form_border_top_color'] ) : '' );
	$settings['login_form_background_color'] = esc_html( $_POST['login_form_background_color'] );
	$settings['login_form_background'] = esc_html( $_POST['login_form_background'] );
	$settings['login_form_background_size'] = esc_attr( $_POST['login_form_background_size'] );
	$settings['login_form_border_radius'] = esc_html( $_POST['login_form_border_radius'] );
	$settings['login_form_border'] = esc_html( $_POST['login_form_border'] );
	$settings['login_form_border_color'] = esc_html( $_POST['login_form_border_color'] );
	$settings['login_form_box_shadow_1'] = esc_html( $_POST['login_form_box_shadow_1'] );
	$settings['login_form_box_shadow_2'] = esc_html( $_POST['login_form_box_shadow_2'] );
	$settings['login_form_box_shadow_3'] = esc_html( $_POST['login_form_box_shadow_3'] );
	$settings['login_form_box_shadow_4'] = esc_html( $_POST['login_form_box_shadow_4'] );
	$settings['login_form_padding_top'] = ( ( isset( $_POST['login_form_padding_top'] ) ) ? true : false );
	$settings['label_color'] = esc_html( $_POST['label_color'] );

	/* Update the theme settings. */
	$updated = update_option( 'custom_login_settings', $settings );
}

/**
 * Registers the plugin meta boxes for use on the settings page.
 *
 * @since 0.8
 */
function custom_login_create_settings_meta_boxes() {
	global $custom_login;


	add_meta_box( 'custom-login-activate-meta-box', __( 'Avtivation &mdash; <em>to infinity and beyond</em>', 'custom-login' ), 'custom_login_activate_meta_box', $custom_login->settings_page, 'normal', 'high' );

	add_meta_box( 'custom-login-announcement-meta-box', __( 'Announcements', 'custom-login' ), 'custom_login_announcement_meta_box', $custom_login->settings_page, 'normal', 'high' );

	add_meta_box( 'custom-login-about-meta-box', __( 'About Custom Login', 'custom-login' ), 'custom_login_about_meta_box', $custom_login->settings_page, 'advanced', 'high' );
	
	add_meta_box( 'custom-login-support-meta-box', __( 'Support Custom Login', 'custom-login' ), 'custom_login_support_meta_box', $custom_login->settings_page, 'advanced', 'high' );
	
	add_meta_box( 'custom-login-dasboard-meta-box', __( 'Core Settings', 'custom-login' ), 'custom_login_dashboard_meta_box', $custom_login->settings_page, 'advanced', 'high' );
	
	add_meta_box( 'custom-login-preview-meta-box', __( 'Preview your work, <em>Master</em>', 'custom-login' ), 'custom_login_preview_meta_box', $custom_login->settings_page, 'advanced', 'high' );
	
	/* Remove the upgrade meta box when upgrade is good */
	add_meta_box( 'custom-login-upgrade-link-meta-box', __( 'Upgrade Custom Login', 'custom-login' ), 'custom_login_upgrade_link_meta_box', $custom_login->settings_page, 'advanced', 'high' );

	add_meta_box( 'custom-login-general-meta-box', __( 'General Settings', 'custom-login' ), 'custom_login_general_meta_box', $custom_login->settings_page, 'normal', 'high' );
	
	add_meta_box( 'custom-login-advanced-meta-box', __( 'Advanced Settings', 'custom-login' ), 'custom_login_advanced_meta_box', $custom_login->settings_page, 'normal', 'high' );
	
	add_meta_box( 'custom-login-tabs-meta-box', __( 'TheFrosty Network', 'custom-login' ), 'custom_login_tabs_meta_box', $custom_login->settings_page, 'side', 'low' );
}

/**
 * Displays activation meta box.
 *
 * @since 0.8
 */
function custom_login_activate_meta_box() { ?>

	<table class="form-table side">
		<tr>
			<th>
            	<label for="custom"><?php _e( 'Activate:', 'custom-login' ); ?></label> 
            </th>
            <td>
				<input id="custom" name="custom" type="checkbox" <?php checked( custom_login_get_setting( 'custom' ), true ); ?> value="true" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Check this box to use your own CSS, leave unchecked to use the default style.', 'custom-login' ); ?></span>
            </td>
		</tr>
		<tr>
			<th>
            	<label for="gravatar"><?php _e( 'Gravatar:', 'custom-login' ); ?></label> 
            </th>
            <td>
				<input id="gravatar" name="gravatar" type="checkbox" <?php checked( custom_login_get_setting( 'gravatar' ), true ); ?> value="true" <?php if ( !get_option( 'users_can_register' ) ) echo 'disabled="disabled" readonly="readonly"'; ?> />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php if ( !get_option( 'users_can_register' ) ) { _e( 'This feature only works for sites with registration enabled.', 'custom-login' ); } else { _e( 'Check this box to activate a AJAX Gravatar image for registration.', 'custom-login' ); } ?></span>
            </td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Display an announcement meta box.
 *
 * @since 0.8
 */
function custom_login_announcement_meta_box() { ?>

	<iframe allowtransparency="true" src="http://austinpassy.com/custom-login.php" scrolling="no" style="height:50px;width:100%;">
	</iframe><!-- .form-table --><?php
}

/**
 * Displays the about meta box.
 *
 * @since 0.8
 */
function custom_login_about_meta_box() {
	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' ); ?>

	<table class="form-table side">
		<tr>
			<th><?php _e( 'Plugin:', 'custom-login' ); ?></th>
			<td><?php echo $plugin_data['Title']; ?> <?php echo $plugin_data['Version']; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Author:', 'custom-login' ); ?></th>
			<td><?php echo $plugin_data['Author']; ?> &ndash; @<a href="http://twitter.com/TheFrosty" title="Follow me on Twitter">TheFrosty</a></td>
		</tr>
		<tr style="display: none;">
			<th><?php _e( 'Description:', 'custom-login' ); ?></th>
			<td><?php echo $plugin_data['Description']; ?></td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * link to upgrade page
 * http://codex.wordpress.org/Function_Reference/remove_submenu_page
 *
 * @since 1.0.1
 */
function custom_login_upgrade_link_meta_box() {
	global $custom_login; ?>
    
        <div style="height: 25px; padding: 25px; text-align: center"><a href="http://extendd.com/plugin/custom-login-pro/" class="button-secondary"/><?php esc_attr_e('Upgrade to Custom Login PRO', 'custom-login'); ?></a></div><?php
}

/**
 * Displays the support meta box.
 *
 * @since 0.8
 */
function custom_login_support_meta_box() { ?>

	<table class="form-table side">
        <tr>
            <th><?php _e( 'Donate:', 'custom-login' ); ?></th>
            <td><?php _e( '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X4JPT57AWMTYW">PayPal</a>.', 'custom-login' ); ?></td>
        </tr>
        <tr>
            <th><?php _e( 'Flattr:', 'custom-login' ); ?></th>
            <td><a href="http://flattr.com/thing/846561/Custom-Login" target="_blank"><img src="http://api.flattr.com/button/flattr-badge-large.png" alt="<?php _e( 'Flattr:', 'custom-login' ); ?>" title="<?php _e( 'Flattr:', 'custom-login' ); ?>" /></a></td>
        </tr>
        <tr>
            <th><?php _e( 'Rate:', 'custom-login' ); ?></th>
            <td><?php _e( '<a href="http://wordpress.org/extend/plugins/custom-login/">This plugin on WordPress.org</a>.', 'custom-login' ); ?></td>
        </tr>
        <tr>
            <th><?php _e( 'Share:', 'custom-login' ); ?></th>
            <td><?php _e( 'Your design on <a href="http://www.flickr.com/groups/custom-login/"><span style="color:#0066DC;font-weight:bold;">Flick</span><span style="color:#ff0084;font-weight:bold;">r</span></a>.', 'custom-login' ); ?></td>
        </tr>
		<tr>
			<th><?php _e( 'Support:', 'custom-login' ); ?></th>
			<td><?php _e( '<a href="http://wordpress.org/support/plugin/custom-login">WordPress support forums</a>.', 'custom-login' ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Contribute:', 'custom-login' ); ?></th>
			<td><?php _e( '<a href="https://github.com/thefrosty/custom-login">GitHub</a>.', 'custom-login' ); ?></td>
		</tr>
		<tr class="alt">
			<th><?php _e( 'Go PRO:', 'custom-login' ); ?></th>
			<td><?php _e( '<a href="http://extendd.com/plugin/custom-login-pro/?ref=custom-login&url='.get_home_url().'">Custom Login PRO</a>.', 'custom-login' ); ?></td>
		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the preview meta box.
 *
 * @since 0.8
 */
function custom_login_dashboard_meta_box() { ?>

    <table class="form-table side">
        <tr>
            <td>
				<input id="hide_dashboard" name="hide_dashboard" type="checkbox" <?php checked( custom_login_get_setting( 'hide_dashboard' ), true ); ?> value="true" />
                <span class="hide"><?php _e( 'Hide the dashboard widget?', 'custom-login' ); ?></span>
            </td>
		</tr>
        <tr>
            <td>
				<input id="disable_presstrends" name="disable_presstrends" type="checkbox" <?php checked( custom_login_get_setting( 'disable_presstrends' ), true ); ?> value="true" />
                <span class="hide"><?php _e( 'Disable <a href="http://presstrends.io">presstrends.io</a>', 'custom-login' ); ?></span>
            </td>
		</tr>
	</table><!-- .form-table --><?php
    
	if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) echo '<code>' . print_r( CUSTOM_LOGIN_FILE, true ) . '</code>';
}

/**
 * Displays the preview meta box.
 *
 * @since 0.8
 */
function custom_login_preview_meta_box() { ?>

    <p style="font-weight: bold; text-align: center;"><a class="thickbox thickbox-preview" href="<?php echo wp_login_url(); ?>?TB_iframe=true" title=""><?php _e( 'Click here to see a live preview!', 'custom-login' ); ?></a></p>
	<p style="text-align: center;"><small><?php //_e( '(May not work as of WordPress 3.1.1)', 'custom-login' ); ?></small></p><?php
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////
//		login_form_border_top_color
///////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Displays the general meta box.
 *
 * @since 0.8
 */
function custom_login_general_meta_box() {
	
	$background_size = array( 'none', 'cover', 'contain', 'flex' );
	
	?>
	<table class="form-table">        
            <th>
            	<label for="html_background_color"><?php _e( 'html background color:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="color {hash:true,required:false,adjust:false}" id="html_background_color" name="html_background_color" value="<?php echo custom_login_get_setting( 'html_background_color' ); ?>" size="10" maxlength="21" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use HEX color <strong>with</strong> &ldquo;#&rdquo; <strong>or</strong> RGB/A format.<br />
				Example: &sup1;<code>#121212</code> &sup2;<code>rgba(255,255,255,0.4)</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="html_background_url"><?php _e( 'html background url:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="upload_image" id="html_background_url" name="html_background_url" value="<?php echo custom_login_get_setting( 'html_background_url' ); ?>" size="40" />
				<input class="upload_image_button" type="button" value="Upload" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Upload an image and put the full path here.<br />
                Suggested size: <code>10px X 500px</code> (for a repeating background) or<br />
                Full size image with a 100% stretched to fit window image.', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="html_background_repeat"><?php _e( 'html background repeat:', 'custom-login' ); ?></label> 
            </th>
            <td>
            	<?php $background_repeat = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ); ?>
                <select name="html_background_repeat" id="html_background_repeat" style="width:88px;">
					<?php foreach ( $background_repeat as $option ) { ?>
                        <option value="<?php echo $option; ?>" <?php selected( $option, custom_login_get_setting( 'html_background_repeat' ) ); ?>><?php echo $option; ?></option>
                    <?php } ?>
                </select>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use <code>no-repeat</code>, <code>repeat</code>, <code>repeat-x</code> or <code>repeat-y.</code>', 'custom-login' ); ?></span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="html_background_size"><?php _e( 'html background size:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <select name="html_background_size" id="html_background_size" style="width:88px;">
					<?php foreach ( $background_size as $option ) { ?>
                        <option value="<?php echo $option; ?>" <?php selected( $option, custom_login_get_setting( 'html_background_size' ) ); ?>><?php echo $option; ?></option>
                    <?php } ?>
                </select>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'See <a href="http://css-tricks.com/perfect-full-page-background-image/">CSS-Tricks</a> and <a href="http://davidwalsh.name/background-size">David Walsh</a> for examples.', 'custom-login' ); ?></span>
            </td>
   		</tr>
        <!-- Break -->
        
        <tr style="border-top: 1px solid #eee;">
            <th>
            	<label for="login_form_logo"><?php _e( 'Logo:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="upload_image" id="login_form_logo" name="login_form_logo" value="<?php echo custom_login_get_setting( 'login_form_logo' ); ?>" size="40" />
				<input class="upload_image_button" type="button" value="Upload" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Upload an image and put the full path here.<br />
                Suggested size: <code>310px X 70px</code>, which will replace WordPress logo. Be sure to leave blank if not in use. NOTE: Will go <strong>above</strong> the form and it&prime;s border.', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>        
        <!-- Break -->
        
        <tr style="border-top: 1px solid #eee;">
            <th>
            	<label for="login_form_background_color"><?php _e( 'login form background color:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="color {hash:true,required:false,adjust:false}" id="login_form_background_color" name="login_form_background_color" value="<?php echo custom_login_get_setting( 'login_form_background_color' ); ?>" size="10" maxlength="21" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use HEX color <strong>with</strong> &ldquo;#&rdquo; or RGB/A format.<br />
				Example: &sup1;<code>#121212</code> &sup2;<code>rgba(255,255,255,0.4)</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_background"><?php _e( 'login form background url:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="upload_image" id="login_form_background" name="login_form_background" value="<?php echo custom_login_get_setting( 'login_form_background' ); ?>" size="40" />
				<input class="upload_image_button" type="button" value="Upload" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Upload an image and put the full path here. Suggested size: <code>308px X 108px</code><br />
                My suggestion: use a transparent .png or .gif. <a href="' . CUSTOM_LOGIN_URL . 'library/psd/custom-login.psd">Download included .psd file</a>.', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_background_size"><?php _e( 'form background size:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <select name="login_form_background_size" id="login_form_background_size" style="width:88px;">
					<?php foreach ( $background_size as $option ) { ?>
                        <option value="<?php echo $option; ?>" <?php selected( $option, custom_login_get_setting( 'login_form_background_size' ) ); ?>><?php echo $option; ?></option>
                    <?php } ?>
                </select>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'See <a href="http://css-tricks.com/perfect-full-page-background-image/">CSS-Tricks</a> and <a href="http://davidwalsh.name/background-size">David Walsh</a> for examples.', 'custom-login' ); ?></span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_border_radius"><?php _e( 'login form border radius:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input id="login_form_border_radius" name="login_form_border_radius" value="<?php echo custom_login_get_setting( 'login_form_border_radius' ); ?>" size="3" maxlength="2" />px
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Choose your border radius, ie <code>8</code> or <code>12</code>. Do not put &ldquo;<strong>px</strong>&rdquo;!', 'custom-login' ); ?></span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_border"><?php _e( 'login form border thickness:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input id="login_form_border" name="login_form_border" value="<?php echo custom_login_get_setting( 'login_form_border' ); ?>" size="2" maxlength="2" />px
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Choose your border thickness, i.e. <code>1</code> or <code>2</code>. Do not put &ldquo;<strong>px</strong>&rdquo;!', 'custom-login' ); ?></span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_border_color"><?php _e( 'login form border color:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="color {hash:true,required:false,adjust:false}" id="login_form_border_color" name="login_form_border_color" value="<?php echo custom_login_get_setting( 'login_form_border_color' ); ?>" size="10" maxlength="21" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use HEX color <strong>with</strong> &ldquo;#&rdquo; or RGB/A format.<br />
				Example: &sup1;<code>#121212</code> &sup2;<code>rgba(255,255,255,0.4)</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_box_shadow_1"><?php _e( 'login form box shadow:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input id="login_form_box_shadow_1" name="login_form_box_shadow_1" value="<?php echo custom_login_get_setting( 'login_form_box_shadow_1' ); ?>" size="2" maxlength="2" />px
                <input id="login_form_box_shadow_2" name="login_form_box_shadow_2" value="<?php echo custom_login_get_setting( 'login_form_box_shadow_2' ); ?>" size="2" maxlength="2" />px
                <input id="login_form_box_shadow_3" name="login_form_box_shadow_3" value="<?php echo custom_login_get_setting( 'login_form_box_shadow_3' ); ?>" size="2" maxlength="2" />px
                <input class="color {hash:true,required:false,adjust:false}" id="login_form_box_shadow_4" name="login_form_box_shadow_4" value="<?php echo custom_login_get_setting( 'login_form_box_shadow_4' ); ?>" size="10" maxlength="21" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Choose your box shadow settings, i.e. <code>5px 5px 18px #464646</code> <em>example code - <code>offset, offset, blur, color</code></em>.', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <tr>
            <th>
            	<label for="login_form_padding_top"><?php _e( 'login form padding fix:', 'custom-login' ); ?></label> 
            </th>
            <td>
				<input id="login_form_padding_top" name="login_form_padding_top" type="checkbox" <?php checked( custom_login_get_setting( 'login_form_padding_top' ), true ); ?> value="true" />
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Select the box if you would like the padding of the form to fit better.', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
        <!-- Break -->
        
		<tr style="border-top: 1px solid #eee;">
            <th>
            	<label for="label_color"><?php _e( 'label font color:', 'custom-login' ); ?></label> 
            </th>
            <td>
                <input class="color {hash:true,required:false,adjust:false}" id="label_color" name="label_color" value="<?php echo custom_login_get_setting( 'label_color' ); ?>" size="10" maxlength="21" /> <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use HEX color <strong>with</strong> &ldquo;#&rdquo; or RGB/A format.<br />
				Example: &sup1;<code>#121212</code> &sup2;<code>rgba(255,255,255,0.4)</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the gallery settings meta box.
 *
 * @since 0.8
 */
function custom_login_advanced_meta_box() { ?>

	<table class="form-table">
		<tr>
			<th>
            	<label for="custom_css"><?php _e( 'Custom CSS:', 'custom-login' ); ?></label> 
            </th>
            <td>             
                <textarea id="custom_css" name="custom_css" cols="50" rows="3" class="large-text code"><?php echo wp_specialchars_decode( stripslashes( custom_login_get_setting( 'custom_css' ) ), 1, 0, 1 ); ?></textarea>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use this box to enter any custom CSS code that may not be shown below.<br />
                <strong>Example code for resgister, forgot password, back to blog:</strong><br>
				<code>.login #nav, .login #backtoblog { text-shadow: none;}</code><br />
				<code>.login #nav a{color:#FFFFFF!important;}
				.login #nav a:hover{color:#FFFFFF!important;}</code><br />
				<code>.login #nav a{text-decoration:none!important;}
				.login #nav a:hover{text-decoration:underline!important;}</code><br />
				<code>.login #backtoblog a{text-decoration:none!important;}
				.login #backtoblog a:hover{text-decoration:underline!important;}</code><br />
				<code>.login #backtoblog  a{color:#FFFFFF!important;}
				.login #backtoblog  a:hover{color:#FFFFFF!important;}</code><br />
				<hr>
                <strong>Example:</strong> <code>.login #backtoblog a { color:#990000; }</code><br />
                &sect; <strong>Example:</strong> <code>#snow { display:block; position:absolute; } #snow img { height:auto; width:100%; }</code><br />
                &sect; example CSS code for custom html code example..', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
		<tr>
			<th>
            	<label for="custom_html"><?php _e( 'Custom HTML:', 'custom-login' ); ?></label> 
            </th>
            <td>             
                <textarea id="custom_html" name="custom_html" cols="50" rows="3" class="large-text code"><?php echo wp_specialchars_decode( stripslashes( custom_login_get_setting( 'custom_html' ) ), 1, 0, 1 ); ?></textarea>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use this box to enter any custom HTML coded that you can add custom style to in the custom CSS box.<br />
                <strong>Example:</strong> <code>&lt;div id="snow"&gt;&lt;img src="../image.jpg" alt="" /&gt;&lt;/div&gt;<br />&lt;div id="snow-bird"&gt; &lt;/div&gt;</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
        
		<tr>
			<th>
            	<label for="custom_jquery"><?php _e( 'Custom jQuery:', 'custom-login' ); ?></label> 
            </th>
            <td>             
                <textarea id="custom_jquery" name="custom_jquery" cols="50" rows="3" class="large-text code"><?php echo wp_specialchars_decode( stripslashes( custom_login_get_setting( 'custom_jquery' ) ), 1, 0, 1 ); ?></textarea>
                <a class="question" title="Help &amp; Examples">[?]</a><br />
                <span class="hide"><?php _e( 'Use this box to enter any custom jQuery.<br />
                <strong>Example:</strong> <code>$(\'#login\').delay(300).fadeTo(800,1);</code>', 'custom-login' ); ?>
                </span>
            </td>
   		</tr>
	</table><!-- .form-table --><?php
}

/**
 * Displays the support meta box.
 *
 * @since 0.8
 */
function custom_login_tabs_meta_box() { ?>
	<table class="form-table">
        <div id="tab" class="tabbed inside">
    	
        <ul class="tabs">        
            <li class="t1 t"><a class="t1 tab">Austin Passy</a></li>
            <li class="t2 t"><a class="t2 tab">WordCamp<strong>LA</strong></a></li>
            <li class="t4 t"><a class="t3 tab">Extendd</a></li>  
            <li class="t4 t"><a class="t4 tab">Premium WP Plugins</a></li>  
            <li class="t4 t"><a class="t5 tab">Infield Box</a></li>  
            <li class="t5 t"><a class="t6 tab">Float-O-holics</a></li>  
            <li class="t6 t"><a class="t7 tab">Great Escape</a></li>   
            <li class="t7 t"><a class="t8 tab">PDXbyPix</a></li>      
            <li class="t8 t"><a class="t9 tab">Jeana Arter</a></li>             
        </ul>
        
		<?php 
		if ( function_exists( 'thefrosty_network_feed' ) ) {
        	thefrosty_network_feed( 'http://feeds.feedburner.com/AustinPassy', '1' );
			thefrosty_network_feed( 'http://feeds.feedburner.com/WordCampLA', '2' );
       		thefrosty_network_feed( 'http://extendd.com/feed', '3' );
       		thefrosty_network_feed( 'http://extendd.com/feed?post_type=plugin', '4' );
       		thefrosty_network_feed( 'http://infieldbox.com/feed', '5' );
        	thefrosty_network_feed( 'http://floatoholics.com/feed', '6' );
        	thefrosty_network_feed( 'http://greatescapecabofishing.com/feed', '7' ); 
        	thefrosty_network_feed( 'http://pdxbypix.com/feed', '8' );  
        	thefrosty_network_feed( 'http://feeds.feedburner.com/JeanaArter', '9' );  
		} ?>
        
    	</div>
	</table><!-- .form-table --><?php
}

/**
 * Displays a settings saved message.
 *
 * @since 0.8
 */
function custom_login_settings_update_message() { ?>
	<div class="updated fade">
		<p><strong><?php _e( 'Don&prime;t you feel good. You just saved me.', 'custom-login' ); ?></strong></p>
	</div><?php
}

/**
 * Outputs the HTML and calls the meta boxes for the settings page.
 *
 * @since 0.8
 */
function custom_login_settings_page() {
	global $custom_login;

	$plugin_data = get_plugin_data( CUSTOM_LOGIN_DIR . 'custom-login.php' ); ?>

	<div class="wrap">
		
        <?php if ( function_exists( 'screen_icon' ) ) screen_icon(); ?>
        
		<h2><?php _e( 'Custom Login Settings', 'custom-login' ); ?></h2>

		<?php //if ( isset( $_GET['updated'] ) && 'true' == esc_attr( $_GET['updated'] ) ) custom_login_settings_update_message(); ?>

		<div id="poststuff">

			<form method="post" action="<?php echo esc_url( admin_url( 'options-general.php?page=custom-login' ) ); ?>">

				<?php wp_nonce_field( 'custom-login-settings-page' ); ?>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>

				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>

				<div class="metabox-holder">
					<div class="post-box-container column-1 normal"><?php do_meta_boxes( $custom_login->settings_page, 'normal', $plugin_data ); ?></div>
					<div class="post-box-container column-2 advanced"><?php do_meta_boxes( $custom_login->settings_page, 'advanced', $plugin_data ); ?></div>
					<div class="post-box-container column-3 side" style="clear:both;"><?php do_meta_boxes( $custom_login->settings_page, 'side', $plugin_data ); ?></div>
				</div>

				<p class="submit" style="clear: both;">
					<input type="submit" name="Submit" class="button-primary" value="<?php _e( 'Update Settings', 'custom-login' ); ?>" />
					<input type="hidden" name="custom-login-settings-submit" value="true" />
				</p><!-- .submit -->

			</form>

		</div><!-- #poststuff -->

	</div><!-- .wrap --><?php
}

/**
 * Loads the scripts needed for the settings page.
 *
 * @since 0.8
 */
function custom_login_settings_page_enqueue_script() {	
	wp_enqueue_script( 'media-upload' );
	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );
	wp_enqueue_script( 'thickbox' );
	wp_enqueue_script( 'theme-preview' );
	wp_enqueue_script( 'autosize' );
	wp_enqueue_script( 'custom-login' );
	wp_enqueue_script( 'jscolor' );
}

/**
 * Loads the stylesheets needed for the settings page.
 *
 * @since 0.8
 */
function custom_login_settings_page_enqueue_style() {
	wp_enqueue_style( 'thickbox' );
	wp_enqueue_style( 'custom-login-tabs' );
	wp_enqueue_style( 'custom-login-admin' );
}

/**
 * Loads the metabox toggle JavaScript in the settings page head.
 *
 * @since 0.8
 */
function custom_login_settings_page_load_scripts() {
	global $custom_login; ?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			postboxes.add_postbox_toggles( '<?php echo $custom_login->settings_page; ?>' );
		});
		//]]>
	</script><?php
}

/**
 * Plugin Action /Settings on plugins page
 * @since 0.4.2
 * @package plugin
 */
function custom_login_plugin_actions( $links, $file ) {
 	if( $file == 'custom-login/custom-login.php' && function_exists( "admin_url" ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=custom-login' ) . '">' . __('Settings', 'custom-login' ) . '</a>';
		array_unshift( $links, $settings_link ); // before other links
		$links[] = '<a href="http://extendd.com/plugin/custom-login-pro/?ref=plugin-upgrade&refer=' . urlencode( home_url() ) . '" target="_blank">' . __('Go Pro', 'custom-login' ) . '</a>';
	}
	return $links;
}

/**
 * Warnings
 * @since 0.5
 * @package admin
 */
function custom_login_admin_warnings() {
	global $custom_login;
		
		function custom_login_warning() {
			global $custom_login;

			if ( custom_login_get_setting( 'use_custom' ) != true )?>
                <p id="custom-login-warning" class="updated fade below-h2" style="padding: 5px 10px;">
                    <strong><?php sprintf( _e( 'Custom Login plugin is not configured yet. It will use the defualt theme unless you configure the %1$s.', 'custom-login' ), '<a href="' . admin_url( 'options-general.php?page=custom-login' ) . '">options</a>' ); ?></strong>
                </p><?php
		}

		add_action( 'admin_notices', 'custom_login_warning' );

	return;
}

/**
 * RSS Feed
 * @since 		0.3
 * @updated		1.1
 * @package 	Admin
 */
if ( !function_exists( 'thefrosty_network_feed' ) ) {
	function thefrosty_network_feed( $url, $count ) {
		
		$items = custom_login_fetch_rss_items( 3, $url );
		
		echo '<div class="t' . esc_attr( $count ) . ' tab-content postbox open feed">';		
		echo '<ul>';		
		if ( empty( $items ) ) { 
			echo '<li>' . __( 'Error fetching feed' ) . '</li>';
		} else {
			foreach( $items as $item ) : ?>		
				<li>		
					<a href='<?php echo esc_url( $item->get_permalink() ); ?>' title='<?php esc_attr_e( $item->get_description() ); ?>'><?php esc_attr_e( $item->get_title() ); ?></a><br /> 		
					<span style="font-size:10px; color:#aaa;"><?php esc_attr_e( $item->get_date('F, jS Y | g:i a') ); ?></span>		
				</li>		
			<?php endforeach;
		}
		echo '</ul>';		
		echo '</div>';
	}
}

/**
 * Fetch RSS items from the feed.
 *
 * @param 	int    $num  Number of items to fetch.
 * @param 	string $feed The feed to fetch.
 * @return 	array|bool False on error, array of RSS items on success.
 */
function custom_login_fetch_rss_items( $num, $feed ) {
	include_once( ABSPATH . WPINC . '/feed.php' );
	$rss = fetch_feed( $feed );

	// Bail if feed doesn't work
	if ( !$rss || is_wp_error( $rss ) )
		return false;

	$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );

	// If the feed was erroneous 
	if ( !$rss_items ) {
		$md5 = md5( $feed );
		delete_transient( 'feed_' . $md5 );
		delete_transient( 'feed_mod_' . $md5 );
		$rss       = fetch_feed( $feed );
		$rss_items = $rss->get_items( 0, $rss->get_item_quantity( $num ) );
	}

	return $rss_items;
}

?>