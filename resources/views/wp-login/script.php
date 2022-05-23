<?php declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SHORTINIT')) {
    define('SHORTINIT', true);
}

$custom_jquery ??= '';
if (!is_string($custom_jquery) || $custom_jquery === '') {
    return;
}
?>
<script type="text/javascript">
    <?php echo wp_specialchars_decode(stripslashes($custom_jquery)); ?>
</script>
