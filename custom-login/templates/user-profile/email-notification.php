<?php

use TheFrosty\CustomLogin\CustomLogin;

?>
<h4><?php esc_html_e('Your new login notification setting', 'wp-login-locker'); ?></h4>
<table class="form-table">
    <tr>
        <th scope="row"><?php esc_html_e('Login Notifications', 'wp-login-locker'); ?></th>
        <td>
            <label for="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>">
                <input name="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>" type="checkbox"
                       id="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>"
                       value="true"
                    <?php checked('true', get_user_meta($user->ID, CustomLogin::USER_EMAIL_META_KEY, true)); ?> />
                <?php esc_html_e('Disable login notifications', 'wp-login-locker'); ?>
            </label>
        </td>
    </tr>
</table>
