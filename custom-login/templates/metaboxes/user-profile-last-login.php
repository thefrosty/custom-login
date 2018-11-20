<?php

use TheFrosty\CustomLogin\CustomLogin;

$user_login_ip = get_user_meta($user->ID, CustomLogin::LAST_LOGIN_IP_META_KEY, false);
$user_login_time = get_user_meta($user->ID, CustomLogin::LAST_LOGIN_TIME_META_KEY, false);
if (empty($user_login_ip) || empty($user_login_time)) {
    return;
}
?>
<div class="submitbox">
    <div id="last-login-data">
        <div class="misc-pub-section curtime misc-pub-section-last">
            <span id="lastloginip"><?php printf(
                    esc_html__('Last login IP: %1$s', 'wp-login-locker'),
                    '<strong>' . end($user_login_ip) . '</strong>'
                ); ?></span>
            <br>
            <span id="lastlogintime"><?php printf(
                    esc_html__('Last login Date: %1$s', 'wp-login-locker'),
                    '<strong>' . date_i18n(get_option('date_format'), end($user_login_time)) . '</strong>'
                );
                ?></span>
        </div>

        <div class="clear"></div>
    </div>
</div>
