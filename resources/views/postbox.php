<?php declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (!isset($id) || !isset($title) || !isset($content)) {
    wp_die('Invalid arguments passed to postbox');
}

?>
<div class="metabox-holder" id="<?php echo esc_attr($id); ?>">
    <div class="postbox">
        <h3><?php echo wp_kses_post($title); ?></h3>
        <div class="inside"><?php echo wp_kses_post($content); ?></div>
    </div>
</div>
