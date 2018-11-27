<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings;

use Dwnload\WpSettingsApi\App;
use TheFrosty\CustomLogin\Settings\Api\AbstractWpSettingsApi;

/**
 * Class SidebarHooks
 *
 * @package TheFrosty\CustomLogin\Settings
 */
class SidebarHooks extends AbstractWpSettingsApi
{
    private const SIDEBAR_ABOUT_AUTHOR_ID = 'custom-login-about-author';

    /**
     * Add class hooks.
     */
    public function addHooks()
    {
        $this->setSections();
        $this->addAction(App::ACTION_PREFIX . 'sticky_admin_notice', [$this, 'stickyAdminNoticeSocialLinks']);
        $this->addAction(App::ACTION_PREFIX . 'settings_sidebars', [$this, 'aboutTheAuthor'],
            $this->getPriority()
        );
    }

    /**
     * Social links sticky admin menu html.
     */
    protected function stickyAdminNoticeSocialLinks()
    {
        echo $this->getContent('sticky-admin-notice/social-links.php');
    }

    /**
     * About the author sidebar widget.
     */
    protected function aboutTheAuthor()
    {
        $this->postbox(
            self::SIDEBAR_ABOUT_AUTHOR_ID,
            \esc_html__('Custom Login', 'custom-login'),
            $this->getContent('sidebar/about-the-author.php')
        );
    }
}
