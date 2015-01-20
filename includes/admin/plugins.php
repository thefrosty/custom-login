<?php
/**
 * @package     CustomLogin
 * @subpackage  Admin/Plugins
 * @author      Austin Passy <http://austin.passy.co>
 * @copyright   Copyright (c) 2014-2015, Austin Passy
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @since 3.0
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 * @return array $links
 */
function custom_login_plugin_action_links( $links, $file ) {
	$settings_link = '<a href="' . sprintf( admin_url( 'options-general.php?page=%s' ), CUSTOM_LOGIN_DIRNAME ) . '">' . esc_html__( 'Settings', CUSTOM_LOGIN_DIRNAME ) . '</a>';
	if ( $file == CUSTOM_LOGIN_BASENAME )
		array_unshift( $links, $settings_link );

	return $links;
}
add_filter( 'plugin_action_links', 'custom_login_plugin_action_links', 10, 2 );


/**
 * Plugin row meta links
 *
 * @since 3.0
 * @param array $input already defined meta links
 * @param string $file plugin file path and name being processed
 * @return array $input
 */
function custom_login_plugin_row_meta( $input, $file ) {
	if ( $file != CUSTOM_LOGIN_BASENAME )
		return $input;

	$links = array(
		'<a href="' . sprintf( admin_url( 'options-general.php?page=%s/extensions' ), CUSTOM_LOGIN_DIRNAME ) . '">' . esc_html__( 'Extension Installer', CUSTOM_LOGIN_DIRNAME ) . '</a>',
		'<a href="https://frosty.media/plugin/tag/custom-login-extension/" target="_blank">' . esc_html__( 'Add Ons', CUSTOM_LOGIN_DIRNAME ) . '</a>',
	);

	$input = array_merge( $input, $links );

	return $input;
}
add_filter( 'plugin_row_meta', 'custom_login_plugin_row_meta', 10, 2 );