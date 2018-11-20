<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\UserProfile;

use Symfony\Component\HttpFoundation\Request;
use TheFrosty\CustomLogin\CustomLogin;

/**
 * Class EmailNotification
 *
 * @package TheFrosty\CustomLogin\UserProfile
 */
class EmailNotificationSetting extends UserProfile
{
    /**
     * EmailNotificationSetting constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->fields = [
            CustomLogin::USER_EMAIL_META_KEY,
        ];
        parent::__construct($request);
    }

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction(parent::USER_PROFILE_HOOK, [$this, 'showExtraUserFields']);
        parent::addHooks();
    }

    /**
     * Show extra user fields for last login IP and time.
     *
     * @param \WP_User|null $user
     */
    protected function showExtraUserFields(\WP_User $user = null)
    {
        \ob_start();
        include $this->getPlugin()->getDirectory() . 'templates/user-profile/email-notification.php';
        echo \ob_get_clean();
    }
}
