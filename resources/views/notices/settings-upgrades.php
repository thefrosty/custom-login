<?php declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="notice notice-warning is-dismissible">
    <p>
        <span>%1$s</span>
        <span><a href="<?php echo esc_url($url ?? ''); ?>">%2$s</a></span>
    </p>
</div>
