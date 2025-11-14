<?php

declare(strict_types=1);

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (empty($html_background_url)) {
    return;
}

$id = attachment_url_to_postid(esc_url($html_background_url));
if ($id === 0 || !($srcset = wp_get_attachment_image_srcset($id))) { // phpcs:ignore
    return;
}
$style = <<<CSS
<style>
#custom-login__bq_wrapper picture {
    position:fixed;
    left:0;
    top:0;
    z-index:-999;
    width:100vw;
    height:100vh;
}
</style>
CSS;

printf(
    '%3$s<div id="custom-login__bq_wrapper"><picture><source srcset="%1$s"><img alt="%2$s" src="%4$s"></picture></div>',
    esc_attr($srcset),
    esc_attr(get_post_meta($id, '_wp_attachment_image_alt', true)),
    "\n" . $style . "\n",
    esc_url(wp_get_attachment_image_src($id, 'medium')[0])
);

echo PHP_EOL;
