<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
				
$sections	= array(
	array(
		'id'		=> CUSTOM_LOGIN_OPTION . '_design',
		'title'		=> __( 'Design Settings', CUSTOM_LOGIN_DIRNAME ),
		'submit'	=> true,
	),
	array(
		'id'		=> CUSTOM_LOGIN_OPTION . '_general',
		'title'		=> __( 'General Settings', CUSTOM_LOGIN_DIRNAME ),
		'submit'	=> true,
	),
);

/**
 * Design Settings Section
 *
 */
$fields	[CUSTOM_LOGIN_OPTION . '_design'] = array(
	/** BREAK **/
	array(
		'name' 		=> 'break_1',
		'label'		=> sprintf( '<h4>%s</h4>', __( '<abbr title="Hyper Text Markup Language">HTML</abbr>', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'html_background_color',
		'label' 	=> __( 'Background color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> '',
	),
	array(
		'name' 		=> 'html_background_url',
		'label' 	=> __( 'Background image', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'file',
		'default' 	=> '',
		'size'		=> 'large',
		'sanitize' => 'esc_url',
	),
	array(
		'name' 		=> 'html_background_position',
		'label' 	=> __( 'Background position', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> sprintf( '<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>.', __( 'html background position', CUSTOM_LOGIN_DIRNAME ) ),
		'type' 		=> 'select',
		'options' 	=> array(
			'left top'			=> 'left top',
			'left center'		=> 'left center',
			'left bottom'		=> 'left bottom',
			'right top'		=> 'right top',
			'right center'		=> 'right center',
			'right bottom'		=> 'right bottom',
			'center top'		=> 'center top',
			'center center'	=> 'center center',
			'center bottom'	=> 'center bottom',
		),
	),
	array(
		'name' 		=> 'html_background_repeat',
		'label' 	=> __( 'Background repeat', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'no-repeat'	=> 'no-repeat',
			'repeat' 		=> 'repeat',
			'repeat-x' 	=> 'repeat-x',
			'repeat-y' 	=> 'repeat-y',
		)
	),
	array(
		'name' 		=> 'html_background_size',
		'label' 	=> __( 'Background size', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'none'		=> 'none',
			'cover' 	=> 'cover',
			'contain' 	=> 'contain',
			'flex' 		=> 'flex',
		)
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_2',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Logo', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name'		=> 'logo_force_form_max_width',
		'label'		=> __( 'Force max-width', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'If checked and the login form width (set below) is not empty, a CSS rule of <code>width</code> will be applied on the logo wrapper element <code>.login h1</code>.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	array(
		'name' 		=> 'logo_background_url',
		'label' 	=> __( 'Image', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> __( 'The default form width is 320px, so an image <strong>should</strong> be the same. You can always modify the form width (below).', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'file',
		'default' 	=> '',
		'size'		=> 'large',
		'sanitize' => 'esc_url',
	),
	array(
		'name' 		=> 'logo_background_size_width',
		'label' 	=> __( 'Image width', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> __( 'Enter your desired image height (All not integers will be removed).', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '',
		'sanitize' => 'int',
	),
	array(
		'name' 		=> 'logo_background_size_height',
		'label' 	=> __( 'Image height', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> __( 'Enter your desired image height (All not integers will be removed).', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '',
		'sanitize' => 'int',
	),
	array(
		'name' 		=> 'logo_background_position',
		'label' 	=> __( 'Background position', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> sprintf( '<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>', __( 'html background position', CUSTOM_LOGIN_DIRNAME ) ),
		'type' 		=> 'select',
		'options' 	=> array(
			'left top'			=> 'left top',
			'left center'		=> 'left center',
			'left bottom'		=> 'left bottom',
			'right top'		=> 'right top',
			'right center'		=> 'right center',
			'right bottom'		=> 'right bottom',
			'center top'		=> 'center top',
			'center center'	=> 'center center',
			'center bottom'	=> 'center bottom',
		),
	),
	array(
		'name' 		=> 'logo_background_repeat',
		'label' 	=> __( 'Background repeat', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'no-repeat'	=> 'no-repeat',
			'repeat' 		=> 'repeat',
			'repeat-x' 	=> 'repeat-x',
			'repeat-y' 	=> 'repeat-y',
		)
	),
	array(
		'name' 		=> 'logo_background_size',
		'label' 	=> __( 'Background size', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'none'		=> 'none',
			'cover' 	=> 'cover',
			'contain' 	=> 'contain',
			'flex' 		=> 'flex',
		)
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_3',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Login Form', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'login_form_width',
		'label' 	=> __( 'Width', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> __( 'Set a different width in pixel value.', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '',
		'sanitize' => 'int',
	),
	array(
		'name' 		=> 'login_form_background_color',
		'label' 	=> __( 'Background color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> ''
	),
	array(
		'name' 		=> 'login_form_background_url',
		'label' 	=> __( 'Background URL', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> __( 'Add a background image to the login form.', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'file',
		'default' 	=> '',
		'size'		=> 'large',
		'sanitize' => 'esc_url',
	),
	array(
		'name' 		=> 'login_form_background_position',
		'label' 	=> __( 'Background position', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> sprintf( '<a href="http://www.w3schools.com/cssref/pr_background-position.asp" target="_blank">%s</a>', __( 'html background position', CUSTOM_LOGIN_DIRNAME ) ),
		'type' 		=> 'select',
		'options' 	=> array(
			'left top'			=> 'left top',
			'left center'		=> 'left center',
			'left bottom'		=> 'left bottom',
			'right top'		=> 'right top',
			'right center'		=> 'right center',
			'right bottom'		=> 'right bottom',
			'center top'		=> 'center top',
			'center center'	=> 'center center',
			'center bottom'	=> 'center bottom',
		),
	),
	array(
		'name' 		=> 'login_form_background_repeat',
		'label' 	=> __( 'Background repeat', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'no-repeat'	=> 'no-repeat',
			'repeat' 		=> 'repeat',
			'repeat-x' 	=> 'repeat-x',
			'repeat-y' 	=> 'repeat-y',
		)
	),
	array(
		'name' 		=> 'login_form_background_size',
		'label' 	=> __( 'Background size', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'select',
		'options' 	=> array(
			'none'		=> 'none',
			'cover' 	=> 'cover',
			'contain' 	=> 'contain',
			'flex' 		=> 'flex',
		)
	),
	array(
		'name' 		=> 'login_form_border_radius',
		'label' 	=> __( 'Border radius', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '',
		'sanitize' => 'int',
	),
	array(
		'name' 		=> 'login_form_border_size',
		'label' 	=> __( 'Border size', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '',
		'sanitize' => 'int',
	),
	array(
		'name' 		=> 'login_form_border_color',
		'label' 	=> __( 'Border color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> ''
	),
	array(
		'name' 		=> 'login_form_box_shadow',
		'label' 	=> __( 'Box shadow', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> sprintf( __( 'Use <a href="%s" target="_blank">box shadow</a> syntax w/ out color. <code>inset h-shadow v-shadow blur spread</code>', CUSTOM_LOGIN_DIRNAME ), 'http://www.w3schools.com/cssref/css3_pr_box-shadow.asp' ),
		'type' 		=> 'text',
		'size'		=> 'medium',
		'default' 	=> '5px 5px 10px'
	),
	array(
		'name' 		=> 'login_form_box_shadow_color',
		'label' 	=> __( 'Box shadow color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> ''
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_4',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Miscellaneous', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'label_color',
		'label' 	=> __( 'Label color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> ''
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_5',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Below Form anchor', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'nav_color',
		'label' 	=> __( 'Nav color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> '',
	),
	array(
		'name' 		=> 'nav_text_shadow_color',
		'label' 	=> __( 'Nav text-shadow color', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> '',
	),
	array(
		'name' 		=> 'nav_hover_color',
		'label' 	=> __( 'Nav color hover', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> '',
	),
	array(
		'name' 		=> 'nav_text_shadow_hover_color',
		'label' 	=> __( 'Nav text-shadow hover', CUSTOM_LOGIN_DIRNAME ),
		'desc' 		=> '',
		'type' 		=> 'colorpicker',
		'default' 	=> '',
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_6',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Custom CSS', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'custom_css',
		'label' 	=> '',
		'desc' 		=> sprintf( '%s %s', __( 'Allowed variables:', CUSTOM_LOGIN_DIRNAME ), '<ul>
			<li>{BSLASH} = "\" (backslash)</li>
			<li><a href="http://wordpress.org/support/topic/quotes-in-custom-css-gets-replaced-with-useless-quote?replies=4">Request others</a></li>
			</ul>' ),
		'type' 		=> 'textarea',
		'sanitize' => 'wp_filter_nohtml_kses',
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_7',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Custom HTML', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'custom_html',
		'label' 	=> '',
		'desc' 		=> '',
		'type' 		=> 'textarea',
		'sanitize' => 'wp_kses_post', //Allow HTML
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_8',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Custom jQuery', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name' 		=> 'custom_jquery',
		'label' 	=> '',
		'desc' 		=> '',
		'type' 		=> 'textarea',
		'sanitize' => 'wp_specialchars_decode',
	),
);

/**
 * General Settings Section
 *
 */
$fields	[CUSTOM_LOGIN_OPTION . '_general'] = array(
	array(
		'name'		=> 'active',
		'label'		=> __( 'Activate', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Allow Custom Login to hook into WordPress.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	array(
		'name'		=> 'capability',
		'label'		=> __( 'Capability', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> sprintf( __( 'Set the minimum user capability to manage these settings. The default capability is <code>%s</code>', CUSTOM_LOGIN_DIRNAME ), 'manage_options' ),
		'type' 		=> 'select',
		'size' 		=> 'large',
		'default' 	=> 'manage_options',
		'options' 	=> custom_login_get_editable_roles()
	),
	
	/** BREAK **/
	array(
		'name' 		=> 'break_1',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Tracking Settings', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name'		=> 'tracking',
		'label'		=> __( 'Allow usage tracking?', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Allow Frosty Media to anonymously track how this plugin is used and help us make the plugin better. Opt-in and receive a 20% discount code for any purchase from the Frosty Media store. Your discount code will be emailed to you.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	
	/** BREAK **/		
	array(
		'name' 		=> 'break_2',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Notices', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name'		=> 'admin_notices',
		'label'		=> __( 'Allow admin notices?', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Allow admin notices everywhere in WordPress. Leave unchecked to disable global notices.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	
	/** BREAK **/		
	array(
		'name' 		=> 'break_3',
		'label'		=> sprintf( '<h4>%s</h4>', __( 'Login functions', CUSTOM_LOGIN_DIRNAME ) ),
		'desc' 		=> '',
		'type' 		=> 'html',
	),
	/** BREAK **/
	
	array(
		'name'		=> 'wp_shake_js',
		'label'		=> __( 'Login shake', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Disable the login form animated "shake" on error.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	array(
		'name'		=> 'remove_login_css',
		'label'		=> __( 'Remove login CSS', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Remove WordPress\' login CSS. Warning: You\'ll have to add aditional syles not set by this plugin.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
	array(
		'name'		=> 'post_password_expires',
		'label'		=> __( 'Login expiration days', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'By default, the cookie expires 10 days from creation. To turn this into a session cookie, set to "0".', CUSTOM_LOGIN_DIRNAME ),
		'type' 		=> 'text',
		'size'		=> 'small',
		'default' 	=> '10',
		'sanitize' => 'int',
	),
	array(
		'name'		=> 'lostpassword_text',
		'label'		=> __( 'Remove lost password text', CUSTOM_LOGIN_DIRNAME ),
		'desc'		=> __( 'Remove the "Lost Password?" text. This does <strong>not<strong> disable the lost password function.', CUSTOM_LOGIN_DIRNAME ),
		'type'		=> 'checkbox'
	),
);