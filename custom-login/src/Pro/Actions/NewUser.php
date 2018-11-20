<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Actions;

use TheFrosty\CustomLogin\AbstractHookRequestProvider;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Utils\GeoUtilTrait;

/**
 * Class NewUser
 * @package TheFrosty\CustomLogin\Actions
 */
class NewUser extends AbstractHookRequestProvider
{
    use GeoUtilTrait;

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction('user_register', [$this, 'userRegisterAction']);
    }

    /**
     * On user registration, add their first unique meta of their IP address and login time.
     *
     * @param int $user_id The new users ID.
     */
    protected function userRegisterAction(int $user_id)
    {
        \add_user_meta($user_id, CustomLogin::LAST_LOGIN_IP_META_KEY, $this->getIP(), false);
        \add_user_meta($user_id, CustomLogin::LAST_LOGIN_TIME_META_KEY, \time(), false);
    }
}
