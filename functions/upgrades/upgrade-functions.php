<?php
/**
 * Upgrade Functions
 *
 * @package     Custom Login
 * @copyright   Copyright (c) 2013, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Display Upgrade Notices
 *
 * @access      private
 * @since       2.0
 * @return      void
*/
function ap_custom_login_show_upgrade_notices() {
	if ( isset( $_GET['page'] ) && $_GET['page'] == 'custom-login-upgrades' )
		return; // Don't show notices on the upgrades page
	
	$login = CUSTOMLOGIN();
	
	$old_settings = get_option( 'custom_login_settings' );
	
	/* New install */
	if ( empty( $old_settings ) )
		return;
	
	if ( !empty( $old_settings ) && !empty( $old_settings['version'] ) )
		$cl_version = $old_settings['version'];
	else
		$cl_version = $login->version;
	
	//versions less than 2.0
	if ( version_compare( $cl_version, '2.0', '<' ) ) {
		printf(
			'<div class="updated"><p>' . esc_html__( 'Custom Login needs to be upgraded, please click %shere%s to start the upgrade.', $login->domain ) . '</p></div>',
			'<a href="' . esc_url( admin_url( 'options.php?page=custom-login-upgrades' ) ) . '">',
			'</a>'
		);
	}
}
add_action( 'admin_notices', 'ap_custom_login_show_upgrade_notices' );

/**
 * Triggers all upgrade functions
 *
 * This function is usually triggered via ajax
 *
 * @access      private
 * @since       2.0
*/
function ap_custom_login_trigger_upgrades() {
	$login = CUSTOMLOGIN();
	
	$old_settings = get_option( 'custom_login_settings' );
	
	if ( !empty( $old_settings ) && !empty( $old_settings['version'] ) )
		$cl_version = $old_settings['version'];
	else
		$cl_version = $login->version;

	if ( version_compare( $cl_version, '2.0', '<' ) ) {
		ap_custom_login_v2_0_0_upgrades();
	}

	if ( DOING_AJAX ) die( 'complete' ); // Let ajax know we are done
}
add_action( 'wp_ajax_custom_login_trigger_upgrades', 'ap_custom_login_trigger_upgrades' );

/**
 * Upgrade routine for v2.0.0
 *
 * @access      private
 * @since       2.0
 * @return      void
 */
function ap_custom_login_v2_0_0_upgrades() {
	$login 	= CUSTOMLOGIN();
	$ss		= CUSTOM_LOGIN_SCRIPT_STYLES();
	$old_settings = get_option( 'custom_login_settings' );
	$new_settings = get_option( $login->id, array() );
		
	$new_settings['active'] = $login->version;
	$new_settings['active'] = true === $old_settings['custom'] ? 'on' : 'off';
	$new_settings['html_background_color'] = $ss->is_rgba( $old_settings['html_background_color'] ) ? $ss->rgba2hex( $old_settings['html_background_color'] ) : $old_settings['html_background_color'];
    $new_settings['html_background_color_checkbox'] = 'off';
    $new_settings['html_background_color_opacity'] = '';
    $new_settings['html_background_url'] = $old_settings['html_background_url'];
    $new_settings['html_background_position'] = 'left top';
    $new_settings['html_background_repeat'] = $old_settings['html_background_repeat'];
    $new_settings['html_background_size'] = $old_settings['html_background_size'];
	$new_settomgs['hide_wp_logo'] = 'on';
    $new_settings['logo_background_url'] = $old_settings['login_form_logo'];
    $new_settings['logo_background_position'] = 'top center';
    $new_settings['logo_background_repeat'] = '';
    $new_settings['logo_background_size'] = '';
    $new_settings['login_form_background_color'] = $ss->is_rgba( $old_settings['html_background_color'] ) ? $ss->rgba2hex( $old_settings['login_form_background_color'] ) : $old_settings['login_form_background_color'];
    $new_settings['login_form_background_color_checkbox'] = 'off';
    $new_settings['login_form_background_color_opacity'] = '';
    $new_settings['login_form_background_url'] = $old_settings['login_form_background'];
    $new_settings['login_form_background_position'] = '';
    $new_settings['login_form_background_repeat'] = '';
    $new_settings['login_form_background_size'] = $old_settings['login_form_background_size'];
    $new_settings['login_form_border_radius'] = $old_settings['login_form_border_radius'];
    $new_settings['login_form_border_size'] = $old_settings['login_form_border'];
    $new_settings['login_form_border_color'] = $ss->is_rgba( $old_settings['html_background_color'] ) ? $ss->rgba2hex( $old_settings['login_form_border_color'] ) : $old_settings['login_form_border_color'];
    $new_settings['login_form_border_color_checkbox'] = 'off';
    $new_settings['login_form_border_color_opacity'] = '';
    $new_settings['login_form_box_shadow'] = $old_settings['login_form_box_shadow_1'] . 'px ' . $old_settings['login_form_box_shadow_2'] . 'px ' . $old_settings['login_form_box_shadow_3'] . 'px';
    $new_settings['login_form_box_shadow_color'] = $ss->is_rgba( $old_settings['html_background_color'] ) ? $ss->rgba2hex( $old_settings['login_form_box_shadow_4'] ) : $old_settings['login_form_box_shadow_4'];
    $new_settings['login_form_box_shadow_color_checkbox'] = 'off';
    $new_settings['login_form_box_shadow_color_opacity'] = '';
    $new_settings['label_color'] = $ss->is_rgba( $old_settings['html_background_color'] ) ? $ss->rgba2hex( $old_settings['label_color'] ) : $old_settings['label_color'];
    $new_settings['label_color_checkbox'] = 'off';
    $new_settings['label_color_opacity'] = '';
    $new_settings['nav_color'] = '';
    $new_settings['nav_color_checkbox'] = 'off';
    $new_settings['nav_color_opacity'] = '';
    $new_settings['nav_text_shadow_color'] = '';
    $new_settings['nav_text_shadow_color_checkbox'] = 'off';
    $new_settings['nav_text_shadow_color_opacity'] = '';
    $new_settings['nav_hover_color'] = '';
    $new_settings['nav_hover_color_checkbox'] = 'off';
    $new_settings['nav_hover_color_opacity'] = '';
    $new_settings['nav_text_shadow_hover_color'] = '';
    $new_settings['nav_text_shadow_hover_color_checkbox'] = 'off';
    $new_settings['nav_text_shadow_hover_color_opacity'] = '';
    $new_settings['custom_css'] = esc_attr( $old_settings['custom_css'] );
    $new_settings['custom_html'] = wp_specialchars_decode( stripslashes( $old_settings['custom_html'] ), 1, 0, 1 );
    $new_settings['custom_jquery'] = esc_html( $old_settings['custom_jquery'] );
	
	update_option( $login->id, $new_settings );
	delete_option( 'custom_login_settings' );
	return true;
}

/**
 *
option_name: custom_login_settings
option_value:
a:30:{s:7:"version";s:5:"1.1.4";s:6:"custom";b:1;s:8:"gravatar";b:0;s:14:"hide_dashboard";b:0;s:19:"disable_presstrends";b:0;s:12:"hide_upgrade";b:0;s:16:"upgrade_complete";b:0;s:10:"custom_css";s:0:"";s:11:"custom_html";s:0:"";s:13:"custom_jquery";s:0:"";s:21:"html_border_top_color";s:0:"";s:26:"html_border_top_background";s:0:"";s:21:"html_background_color";s:0:"";s:19:"html_background_url";s:0:"";s:22:"html_background_repeat";s:8:"repeat-x";s:20:"html_background_size";s:5:"cover";s:15:"login_form_logo";s:0:"";s:27:"login_form_border_top_color";s:0:"";s:27:"login_form_background_color";s:0:"";s:21:"login_form_background";s:0:"";s:26:"login_form_background_size";s:5:"cover";s:24:"login_form_border_radius";s:2:"11";s:17:"login_form_border";s:1:"1";s:23:"login_form_border_color";s:0:"";s:23:"login_form_box_shadow_1";s:1:"5";s:23:"login_form_box_shadow_2";s:1:"5";s:23:"login_form_box_shadow_3";s:2:"18";s:23:"login_form_box_shadow_4";s:7:"#464646";s:22:"login_form_padding_top";b:1;s:11:"label_color";s:7:"#ffffff";}

a:46:{s:6:"active";s:2:"on";s:21:"html_background_color";s:0:"";s:30:"html_background_color_checkbox";s:3:"off";s:29:"html_background_color_opacity";s:1:"1";s:19:"html_background_url";s:0:"";s:24:"html_background_position";s:8:"left top";s:22:"html_background_repeat";s:9:"no-repeat";s:20:"html_background_size";s:4:"none";s:19:"logo_background_url";s:0:"";s:24:"logo_background_position";s:8:"left top";s:22:"logo_background_repeat";s:9:"no-repeat";s:20:"logo_background_size";s:4:"none";s:27:"login_form_background_color";s:0:"";s:36:"login_form_background_color_checkbox";s:3:"off";s:35:"login_form_background_color_opacity";s:1:"1";s:25:"login_form_background_url";s:0:"";s:30:"login_form_background_position";s:8:"left top";s:28:"login_form_background_repeat";s:9:"no-repeat";s:26:"login_form_background_size";s:4:"none";s:24:"login_form_border_radius";s:0:"";s:22:"login_form_border_size";s:0:"";s:23:"login_form_border_color";s:0:"";s:32:"login_form_border_color_checkbox";s:3:"off";s:31:"login_form_border_color_opacity";s:1:"1";s:21:"login_form_box_shadow";s:12:"5px 5px 10px";s:27:"login_form_box_shadow_color";s:0:"";s:36:"login_form_box_shadow_color_checkbox";s:3:"off";s:35:"login_form_box_shadow_color_opacity";s:1:"1";s:11:"label_color";s:0:"";s:20:"label_color_checkbox";s:3:"off";s:19:"label_color_opacity";s:1:"1";s:9:"nav_color";s:0:"";s:18:"nav_color_checkbox";s:3:"off";s:17:"nav_color_opacity";s:1:"1";s:21:"nav_text_shadow_color";s:0:"";s:30:"nav_text_shadow_color_checkbox";s:3:"off";s:29:"nav_text_shadow_color_opacity";s:1:"1";s:15:"nav_hover_color";s:0:"";s:24:"nav_hover_color_checkbox";s:3:"off";s:23:"nav_hover_color_opacity";s:1:"1";s:27:"nav_text_shadow_hover_color";s:0:"";s:36:"nav_text_shadow_hover_color_checkbox";s:3:"off";s:35:"nav_text_shadow_hover_color_opacity";s:1:"1";s:10:"custom_css";s:0:"";s:11:"custom_html";s:0:"";s:13:"custom_jquery";s:0:"";}
**/