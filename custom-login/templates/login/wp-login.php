<?php
function_exists('login_header') || exit;
login_header(__('Log In'), '', '');
?>
<p class="message"><?php
    echo apply_filters(\TheFrosty\CustomLogin\CustomLogin::HOOK_PREFIX . 'wp-login/message',
        esc_html__('Login without a key has been disabled.', 'custom-login'));
    ?>
</p>
</div>
<div class="clear"></div>
</body>
</html>