<?php
	
error_reporting(0);

/**
 * Extract the options from the database
 * print_r( get_option( 'custom_login_settings' ) );
 *
 * @return	Array (
				[version]
				[custom]
				[gravatar]
				[hide_dashboard]
				[disable_presstrends]
				[hide_upgrade]
				[upgrade_complete]
				[custom_css]
				[custom_html]
				[html_border_top_color]				//Deprecated
				[html_border_top_background]		//Deprecated
				[html_background_color]
				[html_background_url]
				[html_background_repeat]
				[login_form_logo]
				[login_form_border_top_color]
				[login_form_background_color]
				[login_form_background]
				[login_form_border_radius]
				[login_form_border]
				[login_form_border_color]
				[login_form_box_shadow_1]
				[login_form_box_shadow_2]
				[login_form_box_shadow_3]
				[login_form_box_shadow_4]
				[login_form_padding_top]
				[label_color]
				[html_background_size]
 */
extract( get_option( 'custom_login_settings' ) );

?><style type="text/css"><?php

echo "
/**
 * Custom Login lite by Austin Passy
 *
 * Plugin URI	: http://austinpassy.com/wordpress-plugins/custom-login
 * Version		: $version
 * Author URI	: http://austinpassy.com
 * Pro Version	: http://extendd.com/plugin/custom-login-pro
 *
 */";

/* Custom user input */
if ( $custom_css ) echo wp_specialchars_decode( stripslashes( $custom_css ), 1, 0, 1 ) . "\n\n"; ?>


html {
	background: <?php
	
	if ( !empty( $html_background_color ) ) echo trailingsemicolonit( $html_background_color );
	
	if ( !empty( $html_background_url ) ) echo trailingsemicolonit( "url('{$html_background_url}') left top {$html_background_repeat}" );
	
	$background_size = ( 'flex' != $html_background_size ) ? $html_background_size : '100% auto';
	
    custom_login_prefix_it( 'background-size', $background_size ); ?>
	
}

<?php if ( !empty( $html_background_url ) ) { ?>
body.login {
	background: transparent !important;
}
<?php } ?>

#login form {
	<?php
	if ( !empty( $login_form_background_color ) )	echo trailingsemicolonit( "background-color: {$login_form_background_color}" );
	if ( !empty( $login_form_background ) ) 		echo trailingsemicolonit( "background-image: transparent url('{$login_form_background}') center top no-repeat" );
	
	if ( true == $login_form_padding_top ) 			echo trailingsemicolonit( 'padding-top: 20px' ); else echo trailingsemicolonit( 'padding-top: 100px' ); ?>
	
}

#login form {
    border: <?php echo rtrim( $login_form_border, 'px' ); ?>px solid <?php echo $login_form_border_color; ?>;
	<?php
	if ( absint( $login_form_border_radius ) ) {
		$login_form_border_radius = rtrim( $login_form_border_radius, 'px' ) . 'px';
		custom_login_prefix_it( 'border-radius', $login_form_border_radius );
	}
	
	$login_form_box_shadow_1 = rtrim( $login_form_box_shadow_1, 'px' ) . 'px';
	$login_form_box_shadow_2 = rtrim( $login_form_box_shadow_2, 'px' ) . 'px';
	$login_form_box_shadow_3 = rtrim( $login_form_box_shadow_3, 'px' ) . 'px';
	
	$box_shadow = $login_form_box_shadow_1 . ' ' . $login_form_box_shadow_2 . ' ' . $login_form_box_shadow_3 . ' ' . $login_form_box_shadow_4;
	
	custom_login_prefix_it( 'box-shadow', $box_shadow ); ?>
	
}

<?php if ( empty( $login_form_logo ) ) { ?>
#login h1 {
	display: none;
}
<?php }

if ( !empty( $login_form_logo ) ) { ?>
.login h1 a {
	background: transparent url('<?php echo $login_form_logo; ?>') no-repeat scroll center top;
}
<?php } ?>

label {
	color: <?php echo $label_color; ?> !important;
}
</style>