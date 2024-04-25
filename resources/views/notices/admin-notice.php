<?php declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="notice notice-info">
    <p>
        <span>%1$s</span>
        <span class="alignright">
        <a href="<?php echo esc_url($opt_in_url ?? ''); ?>">%2$s</a>
            |
        <a href="<?php echo esc_url($opt_out_url ?? ''); ?>">%3$s</a>
        </span>
    </p>
</div>
