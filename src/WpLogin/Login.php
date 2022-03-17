<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpLogin;

use Dwnload\WpSettingsApi\Api\Options;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\WpUtilities\Api\TransientsTrait;
use TheFrosty\WpUtilities\Plugin\AbstractContainerProvider;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Utils\Viewable;
use function add_filter;
use function get_bloginfo;
use function home_url;
use function is_multisite;
use function remove_action;
use function wp_deregister_style;
use function wp_enqueue_script;
use function wp_kses_post;
use function wp_script_is;

/**
 * Class Login
 * @package TheFrosty\CustomLogin\WpLogin
 */
class Login extends AbstractContainerProvider
{

    use HooksTrait, TransientsTrait, Viewable;

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        if (
            Options::getOption(
                OptionKey::ACTIVE,
                Factory::SECTION_GENERAL,
                OptionValue::ON
            ) === OptionValue::OFF
        ) {
            return;
        }

        $this->addAction('init', [$this, 'maybeRemoveLoginStyle']);
        $this->addAction('login_enqueue_scripts', [$this, 'loginEnqueueScripts']);
        $this->addAction('login_head', [$this, 'loginHead']);
        $this->addAction('login_footer', [$this, 'loginFooterHtml'], 8);
        $this->addAction('login_footer', [$this, 'loginFooterJquery'], 19);
        $this->addFilter('login_headerurl', [$this, 'loginHeaderUrl']);
        $this->addFilter('login_headertext', [$this, 'loginHeaderTitle']);
        $this->addFilter('gettext', [$this, 'removeLostPasswordText'], 20, 2);
    }

    /**
     * Finds the global page for the wp-login.php and removes default stylesheets, so we can add our own.
     */
    protected function maybeRemoveLoginStyle(): void
    {
        if (
            $GLOBALS['pagenow'] === 'wp-login.php' &&
            Options::getOption(
                OptionKey::REMOVE_LOGIN_CSS,
                Factory::SECTION_GENERAL,
                OptionValue::OFF
            ) === OptionValue::ON
        ) {
            add_filter('wp_admin_css', '__return_false');
            wp_deregister_style('login');
        }
    }

    /**
     * Enqueue additional scripts.
     * @since 2.0
     */
    protected function loginEnqueueScripts(): void
    {
        if (
            Options::getOption(
                OptionKey::ANIMATE_CSS,
                Factory::SECTION_DESIGN,
                OptionValue::OFF
            ) === OptionValue::ON
        ) {
            // Enqueue the Animate.CSS
            wp_enqueue_style(
                'animate.css',
                $this->getPlugin()->getPath('node_modules/animate.css/animate.css'),
                ['login'],
                '4.1.1',
                'screen'
            );
        }

        if (
            !empty(Options::getOption(OptionKey::CUSTOM_JQUERY, Factory::SECTION_DESIGN)) &&
            !wp_script_is('jquery')
        ) {
            wp_enqueue_script('jquery');
        }
    }

    /**
     * Actions hooked into login_head
     */
    protected function loginHead(): void
    {
        if (
            Options::getOption(
                OptionKey::WP_SHAKE_JS,
                Factory::SECTION_GENERAL,
                OptionValue::OFF
            ) === OptionValue::ON
        ) {
            remove_action('login_footer', 'wp_shake_js', 12);
        }

        $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->render(
            'wp-login/style',
            Options::getOptions(Factory::SECTION_DESIGN)
        );
    }

    /**
     * If there is custom HTML set in the settings echo it to the 'login_footer' hook in wp-login.php.
     */
    protected function loginFooterHtml(): void
    {
        $data = Options::getOption(OptionKey::CUSTOM_HTML, Factory::SECTION_DESIGN);
        if (!empty($data)) {
            echo wp_kses_post($data) . PHP_EOL;
        }
    }

    /**
     * Database access to the scripts and styles.
     * @since 2.1
     */
    protected function loginFooterJquery(): void
    {
        $data = Options::getOption(OptionKey::CUSTOM_JQUERY, Factory::SECTION_DESIGN);

        if (empty($data)) {
            return;
        }

        $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->render(
            'wp-login/script', [
                OptionKey::CUSTOM_JQUERY => $data,
            ]
        );
    }

    /**
     * Replace the default link to your URL
     * @param string $url
     * @return string
     */
    protected function loginHeaderUrl(string $url): string
    {
        if (is_multisite()) {
            return $url;
        }

        return home_url();
    }

    /**
     * Replace the default title to your description
     * @param string $title
     * @return string
     */
    protected function loginHeaderTitle(string $title): string
    {
        if (is_multisite()) {
            return $title;
        }

        return get_bloginfo('description');
    }

    /**
     * Remove the "Lost your password?" text.
     */
    protected function removeLostPasswordText($translated_text, $untranslated_text): string
    {
        if (
            $GLOBALS['pagenow'] === 'wp-login.php' &&
            Options::getOption(OptionKey::LOSTPASSWORD_TEXT, Factory::SECTION_GENERAL) !== OptionValue::OFF &&
            $untranslated_text === 'Lost your password?'
        ) {
            $translated_text = ''; // Unset translation to empty string
        }

        return $translated_text;
    }
}