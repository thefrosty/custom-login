<?php

declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (empty($html_background_url)) {
    return;
}

$id = attachment_url_to_postid(esc_url($html_background_url));
if ($id === 0 || !($srcset = wp_get_attachment_image_srcset($id))) {
    return;
}
$style = <<<CSS
<style>
picture#custom-login__picture {
    position:fixed;
    left:0;
    top:0;
    z-index:-999;
    object-fit:cover;
    width:100vw;
    animation: unblur 1200ms;
}
@keyframes unblur {
  0% {
    filter: blur(200px)
  }
  70% {
    filter: blur(20px)
  }
  40% {
    filter: blur(40px)
  }
  100% {
    filter: blur(0)
  }
}
</style>
CSS;

printf(
    '%3$s<picture id="custom-login__picture"><img srcset="%1$s" alt="%2$ss" src=""></picture>',
    esc_attr($srcset),
    esc_attr(get_post_meta($id, '_wp_attachment_image_alt', true)),
    $style
);
