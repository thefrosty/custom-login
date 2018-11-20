<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\UserProfile;

use TheFrosty\CustomLogin\AbstractHookRequestProvider;
use TheFrosty\CustomLogin\CustomLogin;

/**
 * Class UserProfile
 *
 * @package TheFrosty\CustomLogin\UserProfile
 */
abstract class UserProfile extends AbstractHookRequestProvider
{
    const USER_PROFILE_HOOK = CustomLogin::HOOK_PREFIX . 'user_profile/extra_fields';

    /**
     * User meta fields to save.
     *
     * @var array $fields
     */
    protected $fields = [];

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->addAction('show_user_profile', [$this, 'doUserProfileAction'], 19);
        $this->addAction('edit_user_profile', [$this, 'doUserProfileAction'], 19);
        $this->addAction('personal_options_update', [$this, 'saveExtraProfileFields']);
        $this->addAction('edit_user_profile_update', [$this, 'saveExtraProfileFields']);
    }

    /**
     * @param \WP_User|null $user
     * @return void
     */
    protected function doUserProfileAction(\WP_User $user = null)
    {
        if (!\did_action(self::USER_PROFILE_HOOK)) {
            \printf('<h2>%s</h2>', \esc_html__('Login Locker Settings', 'wp-login-locker'));
            \do_action(self::USER_PROFILE_HOOK, $user);
        }
    }

    /**
     * If the inherited class set's fields, save them.
     *
     * @param int $user_id The current users ID.
     */
    protected function saveExtraProfileFields($user_id)
    {
        if (empty($this->fields) || !current_user_can('edit_user', $user_id)) {
            return;
        }

        foreach ($this->fields as $field) {
            if ($this->getRequest()->request->has($field)) {
                \update_user_meta($user_id, $field, $this->getRequest()->request->get($field));
            } else {
                \delete_user_meta($user_id, $field);
            }
        }
    }

    /**
     * Helper to get the user meta as an array.
     *
     * @param int $user_id
     * @param string $key
     * @return array
     */
    protected function getUserMeta(int $user_id, string $key): array
    {
        return \get_user_meta($user_id, $key, false);
    }
}
