<?php

use TheFrosty\CustomLogin\CustomLogin;

?>
    <table class="form-table">

        <tr class="user-login-notifications-wrap">
            <th scope="row"><?php esc_html_e('Login Notifications', 'wp-login-locker'); ?></th>
            <td>
                <label for="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>">
                    <input name="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>" type="checkbox"
                           id="<?php echo CustomLogin::USER_EMAIL_META_KEY; ?>"
                           value="true"
                        <?php checked(true, get_user_meta($user->ID, CustomLogin::USER_EMAIL_META_KEY, true)); ?> />
                    <?php esc_html_e('Disable login notifications', 'wp-login-locker'); ?>
                </label>
            </td>
        </tr>
    </table>
<?php
