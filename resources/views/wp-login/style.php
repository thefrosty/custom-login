<?php

declare(strict_types=1);

use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\OptionValue;

use function TheFrosty\CustomLogin\openCssRule;
use function TheFrosty\CustomLogin\prefixIt;
use function TheFrosty\CustomLogin\trailingSemicolonIt;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// phpcs:ignore
$login_h1 = version_compare($GLOBALS['wp_version'], '6.7', '>=') ? '.login .wp-login-logo' : '.login h1';
$version ??= CustomLogin::VERSION;
$close_rule = "}\n";

$css = '<style>';
$css .= "
/**
 * Custom Login by Austin Passy
 *
 * Plugin URI  : https://frosty.media/plugins/custom-login/
 * Version     : $version
 * Author URI  : https://austin.passy.co/
 * Extensions  : https://frosty.media/plugin/tag/custom-login-extension/
 */\n\n";

$custom_css ??= '';
if (is_string($custom_css) && $custom_css !== '') {
    $css .= "/** START Custom CSS */\n";
    $css .= str_replace(['{BSLASH}'], ['\\'], wp_specialchars_decode(stripslashes($custom_css)));
    $css .= "\n/** END Custom CSS */\n";
    $css .= "\n\n";
}

/**
 * Open html
 * @rule html
 */
if (!empty($html_background_color) || !empty($html_background_url)) {
    $css .= openCssRule('html');
    if (!empty($html_background_color)) {
        $css .= trailingSemicolonIt('background-color', $html_background_color);
    }

    if (!empty($html_background_url) && (empty($html_use_img_srcset) || $html_use_img_srcset === OptionValue::OFF)) {
        $css .= trailingSemicolonIt('background-image', "url('$html_background_url')");
        $html_background_position ??= '';
        $html_background_repeat ??= '';
        $css .= trailingSemicolonIt('background-position', $html_background_position);
        $css .= trailingSemicolonIt('background-repeat', $html_background_repeat);

        if (!empty($html_background_size) && $html_background_size !== 'none') {
            $css .= prefixIt('background-size', $html_background_size);
        }
    }
    $css .= $close_rule; // CLOSE html.
}

/**
 * Open body.login
 * @rule body.login
 */
if (!empty($html_background_color) || !empty($html_background_url)) {
    $css .= openCssRule('body.login');
    $css .= trailingSemicolonIt('background', 'transparent');
    $css .= $close_rule;  // CLOSE body.login.
}

/**
 * Open login
 * @rule #login
 */
if (!empty($login_form_width)) {
    $css .= openCssRule('#login');
    $css .= trailingSemicolonIt('width', sprintf('%1$s%2$s', $login_form_width, $login_form_width_unit ?? 'px'));
    $css .= $close_rule; // CLOSE #login.
}

/**
 * Open login form
 * @rule #login form
 */
$css .= openCssRule('#login form');
if (!empty($login_form_background_color)) {
    $css .= trailingSemicolonIt('background-color', $login_form_background_color);
}

if (!empty($login_form_background_url)) {
    $css .= trailingSemicolonIt('background-image', "url('$login_form_background_url')");
    $login_form_background_position ??= '';
    $login_form_background_repeat ??= '';
    $css .= trailingSemicolonIt('background-position', $login_form_background_position);
    $css .= trailingSemicolonIt('background-repeat', $login_form_background_repeat);

    if (!empty($login_form_background_size) && $login_form_background_size !== 'none') {
        $login_form_background_size = $login_form_background_size !== 'flex' ?
            $login_form_background_size : '100% auto';
        $css .= prefixIt('background-size', $login_form_background_size);
    }
}

if (!empty($login_form_border_size) && !empty($login_form_border_color)) {
    $login_form_border_size = rtrim($login_form_border_size, 'px');
    $css .= trailingSemicolonIt(
        'border',
        sprintf('%1$spx solid %2$s', $login_form_border_size, $login_form_border_color)
    );
}

if (!empty($login_form_border_radius)) {
    $login_form_border_radius = rtrim($login_form_border_radius, 'px') . 'px';
    $css .= prefixIt('border-radius', $login_form_border_radius);
}

if (!empty($login_form_box_shadow)) {
    if (empty($login_form_box_shadow_color)) {
        $login_form_box_shadow_color = '#121212';
    }
    $box_shadow = $login_form_box_shadow . ' ' . $login_form_box_shadow_color;

    $css .= prefixIt('box-shadow', trim($box_shadow));
}
$css .= $close_rule; // CLOSE #login form.

/**
 * Open login h1
 * @rule #login h1
 */
if ((!empty($hide_wp_logo) && $hide_wp_logo === OptionValue::ON) && empty($logo_background_url)) {
    $css .= openCssRule('#login h1');
    $css .= trailingSemicolonIt('display', 'none');
    $css .= $close_rule; // CLOSE #login h1.
}

/**
 * Open login h1
 * @rule .login h1 / .login .wp-login-logo
 */
if (
    (!empty($logo_force_form_max_width) && $logo_force_form_max_width === OptionValue::ON) &&
    !empty($login_form_width)
) {
    $css .= openCssRule($login_h1);
    $css .= trailingSemicolonIt(
        'width',
        sprintf('%1$s%2$s', $login_form_width, $login_form_width_unit ?? 'px')
    );
    $css .= $close_rule; // CLOSE .login h1 / .login .wp-login-logo.
}

/**
 * Open login h1 a
 * @rule .login h1 a / .login .wp-login-logo a
 */
if (!empty($logo_background_url)) {
    $css .= openCssRule("$login_h1 a");
    if (!empty($logo_background_size_width)) {
        $css .= trailingSemicolonIt('width', sprintf('%1$spx !important', $logo_background_size_width));
    }
    if (!empty($logo_background_size_height)) {
        $css .= trailingSemicolonIt('height', sprintf('%1$spx !important', $logo_background_size_height));
    }
    $css .= trailingSemicolonIt('background-image', "url('$logo_background_url')");
    $logo_background_position ??= '';
    $logo_background_repeat ??= '';
    $css .= trailingSemicolonIt('background-position', $logo_background_position);
    $css .= trailingSemicolonIt('background-repeat', $logo_background_repeat);

    if (!empty($logo_background_size) && $logo_background_size !== 'none') {
        $css .= prefixIt('background-size', $logo_background_size);
    } else {
        $css .= prefixIt('background-size', 'inherit');
    }
    $css .= $close_rule; // CLOSE .login h1 a / .login .wp-login-logo a.
}

/**
 * Open form label
 * @rule .login label | #loginform label, #lostpasswordform label
 */
if (!empty($label_color)) {
    $css .= openCssRule('.login label');
    $css .= trailingSemicolonIt('color', $label_color);
    $css .= $close_rule; // CLOSE .login label.
}

/**
 * Open below form links
 * @rule .login #nav a, .login #backtoblog a
 */
if (!empty($nav_color)) {
    $css .= openCssRule('.login #nav a, .login #backtoblog a');
    $css .= trailingSemicolonIt('color', sprintf('%1$s !important', $nav_color));
    $nav_text_shadow_color ??= '';
    $css .= trailingSemicolonIt('text-shadow', sprintf('0 1px 0 %1$s', $nav_text_shadow_color));
    $css .= $close_rule; // CLOSE .login #nav a, .login #backtoblog a.
}

/**
 * Open below form links :hover
 * @rule .login #nav a:hover, .login #backtoblog a:hover
 */
if (!empty($nav_hover_color)) {
    $css .= openCssRule('.login #nav a:hover, .login #backtoblog a:hover');
    $css .= trailingSemicolonIt('color', sprintf('%1$s !important', $nav_hover_color));
    $nav_text_shadow_hover_color ??= '';
    $css .= trailingSemicolonIt('text-shadow', sprintf('0 1px 0 %1$s', $nav_text_shadow_hover_color));
    $css .= $close_rule; // CLOSE .login #nav a:hover, .login #backtoblog a:hover.
}
$css .= "</style>\n";

/*
 * Out of the frying pan, and into the fire!
 */
echo $css;
