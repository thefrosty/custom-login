<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\UserProfile;

use TheFrosty\CustomLogin\CustomLogin;

/**
 * Class LastLogin
 *
 * @package TheFrosty\CustomLogin\UserProfile
 */
class LastLogin extends UserProfile
{
    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction(parent::USER_PROFILE_HOOK, [$this, 'showExtraUserFields']);
        parent::addHooks();
    }

    /**
     * Searches the user meta and returns either "No data" (if this is the first login), or if there has only
     * been one login, returns the current date most likely. Otherwise it will return that last login date saved.
     *
     * @param int $user_id
     * @return string
     */
    public function getLastLoginIp(int $user_id): string
    {
        $user_login_ip = $this->getUserMeta($user_id, CustomLogin::LAST_LOGIN_IP_META_KEY);
        $count = \count($user_login_ip);
        if ($count === 0) {
            return \__('No data', 'wp-login-locker');
        } elseif ($count === 1) {
            return \end($user_login_ip);
        }

        return $user_login_ip[($count - 1)] ?? \end($user_login_ip);
    }

    /**
     * Returns the active session IP.
     *
     * @param int $user_id
     * @return string
     */
    public function getCurrentLoginIp(int $user_id): string
    {
        $user_login_ip = $this->getUserMeta($user_id, CustomLogin::LAST_LOGIN_IP_META_KEY);
        if (empty($user_login_ip)) {
            return \__('No data', 'wp-login-locker');
        }
        return \end($user_login_ip);
    }

    /**
     * Searches the user meta and returns either "No data" (if this is the first login), or if there has only
     * been one login, returns the current date most likely. Otherwise it will return that last login date saved.
     *
     * @param int $user_id
     * @return string
     */
    public function getLastLogin(int $user_id): string
    {
        $user_login_time = $this->getUserMeta($user_id, CustomLogin::LAST_LOGIN_TIME_META_KEY);
        $count = \count($user_login_time);
        if ($count === 0) {
            return \__('No data', 'wp-login-locker');
        } elseif ($count === 1) {
            return \date_i18n($this->getDateTimeFormat(), \end($user_login_time));
        }
        return \date_i18n(
            $this->getDateTimeFormat(),
            $user_login_time[($count - 1)] ?? \end($user_login_time)
        );
    }

    /**
     * Returns the active session login date.
     *
     * @param int $user_id
     * @return string
     */
    public function getCurrentLogin(int $user_id): string
    {
        $user_login_time = $this->getUserMeta($user_id, CustomLogin::LAST_LOGIN_TIME_META_KEY);
        if (empty($user_login_time)) {
            $user_login_time[] = time();
        }
        return \date_i18n($this->getDateTimeFormat(), \end($user_login_time));
    }

    /**
     * Show extra user fields for last login IP and time.
     *
     * @param \WP_User|null $user
     */
    protected function showExtraUserFields(\WP_User $user = null)
    {
        \ob_start();
        include $this->getPlugin()->getDirectory() . 'templates/user-profile/last-login.php';
        echo \ob_get_clean();
    }

    /**
     * Return the DateTime string format based on the current WordPress settings.
     * @return string
     */
    private function getDateTimeFormat(): string
    {
        return \get_option('date_format') . '\\ ' . \get_option('time_format');
    }
}
