<?php declare(strict_types=1);

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('SHORTINIT')) {
    define('SHORTINIT', true);
}

$data ??= '';
if ($data === '') {
    return;
}
?>
/**
* Custom Login by Austin Passy
*
* Plugin URI: https://frosty.media/plugins/custom-login/
* Version: <?php echo TheFrosty\CustomLogin\CustomLogin::VERSION; ?>
* Author URI: https://austin.passy.co/
* Extensions: https://frosty.media/plugin/tag/custom-login-extension/
*/
<script type="text/javascript">
  (function ($) {
    'use strict'
      <?php echo wp_specialchars_decode(stripslashes($data)); ?>
  }(jQuery))
</script>
