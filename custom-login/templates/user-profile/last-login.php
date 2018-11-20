<?php
/** @var \TheFrosty\CustomLogin\UserProfile\LastLogin $this */

$last_login_ip = $this->getLastLoginIp($user->ID);
$current_login_ip = $this->getCurrentLoginIp($user->ID);
$last_login_time = $this->getLastLogin($user->ID);
$current_login_time = $this->getCurrentLogin($user->ID);
?>
    <h4><?php esc_html_e('Your recent login data', 'wp-login-locker'); ?></h4>
    <table class="form-table">
        <?php if ($last_login_ip !== $current_login_ip) { ?>
            <tr>
                <th scope="row"><?php esc_html_e('Previous IP', 'wp-login-locker'); ?></th>
                <td>
                    <?php echo esc_html($last_login_ip); ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <th scope="row"><?php esc_html_e('Current IP', 'wp-login-locker'); ?></th>
            <td>
                <?php echo esc_html($current_login_ip); ?>
            </td>
        </tr>
        <?php if ($last_login_time !== $current_login_time) { ?>
            <tr>
                <th scope="row"><?php esc_html_e('Previous Login', 'wp-login-locker'); ?></th>
                <td>
                    <?php echo esc_html($last_login_time); ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <th scope="row"><?php esc_html_e('Current Login', 'wp-login-locker'); ?></th>
            <td>
                <?php echo esc_html($current_login_time); ?>
            </td>
        </tr>
    </table>
<?php unset($last_login_ip, $current_login_ip, $last_login_time, $current_login_time);
