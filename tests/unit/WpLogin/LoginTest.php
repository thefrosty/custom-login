<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\WpLogin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\CustomLogin\Settings\Sanitize;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpLogin\Login;

/**
 * Class LoginTest
 * @package TheFrosty\CustomLogin\Tests\WpLogin
 */
#[CoversClass(Login::class)]
#[UsesClass(Factory::class)]
#[UsesClass(Sanitize::class)]
#[UsesClass(ServiceProvider::class)]
class LoginTest extends TestCase
{

    private Login $login;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->login = $this->newLogin();
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        global $pagenow;
        $pagenow = 'index.php';
        delete_option(Factory::getSection(Factory::SECTION_DESIGN));
        delete_option(Factory::getSection(Factory::SECTION_GENERAL));
        remove_all_filters('custom_login/login_footer_html');
        wp_dequeue_style('animate.css');
        wp_deregister_style('animate.css');
        parent::tearDown();
    }

    /**
     * Test addHooks() bails when the plugin is deactivated in the settings.
     */
    public function testAddHooks(): void
    {
        $this->setGeneral([OptionKey::ACTIVE => OptionValue::OFF]);
        $this->login->addHooks();
        $this->assertSame([], $this->getProperty($this->login, 'filter_map'));

        $this->setGeneral([OptionKey::ACTIVE => OptionValue::ON]);
        $login = $this->newLogin();
        $login->addHooks();
        $this->assertCount(9, $this->getProperty($login, 'filter_map'));
    }

    /**
     * Test maybeRemoveLoginStyle().
     */
    public function testMaybeRemoveLoginStyle(): void
    {
        global $pagenow;
        $this->setGeneral([OptionKey::REMOVE_LOGIN_CSS => OptionValue::ON]);
        $pagenow = 'index.php';
        $this->invoke($this->login, 'maybeRemoveLoginStyle');
        $this->assertFalse(has_filter('wp_admin_css', '__return_false'));

        $pagenow = 'wp-login.php';
        $this->invoke($this->login, 'maybeRemoveLoginStyle');
        $this->assertSame(10, has_filter('wp_admin_css', '__return_false'));
        $this->assertFalse(wp_style_is('login', 'registered'));
        remove_filter('wp_admin_css', '__return_false');
        wp_default_styles(wp_styles());
    }

    /**
     * Test loginEnqueueScripts().
     */
    public function testLoginEnqueueScripts(): void
    {
        $this->invoke($this->login, 'loginEnqueueScripts');
        $this->assertFalse(wp_style_is('animate.css'));

        wp_dequeue_script('jquery');
        $this->setDesign([OptionKey::ANIMATE_CSS => OptionValue::ON, OptionKey::CUSTOM_JQUERY => 'console.log(1)']);
        $this->invoke($this->login, 'loginEnqueueScripts');
        $this->assertTrue(wp_style_is('animate.css'));
        $this->assertTrue(wp_script_is('jquery'));
        wp_dequeue_script('jquery');
    }

    /**
     * Test loginHead() prints the stylesheet and can remove the shake JS.
     */
    public function testLoginHead(): void
    {
        add_action('login_footer', 'wp_shake_js', 12);
        $this->setGeneral([OptionKey::WP_SHAKE_JS => OptionValue::ON]);
        $this->setDesign([
            OptionKey::CUSTOM_CSS => '&lt;/style&gt;&lt;script&gt;alert(1)&lt;/script&gt;',
            OptionKey::LABEL_COLOR => '#ff0000;}',
            OptionKey::LOGIN_FORM_WIDTH => '400',
        ]);

        $output = $this->capture(fn() => $this->invoke($this->login, 'loginHead'));
        $this->assertFalse(has_action('login_footer', 'wp_shake_js'));
        $this->assertStringStartsWith('<style>', $output);
        $this->assertSame(1, substr_count(strtolower($output), '</style>'));
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('color: #ff0000;', $output);
        $this->assertStringContainsString('width: 400px;', $output);
    }

    /**
     * Test the login form box shadow, including `none` to remove it entirely.
     */
    public function testLoginHeadBoxShadow(): void
    {
        $this->setDesign([
            OptionKey::LOGIN_FORM_BOX_SHADOW => '5px 5px 10px',
            OptionKey::LOGIN_FORM_BOX_SHADOW_COLOR => '',
        ]);
        $output = $this->capture(fn() => $this->invoke($this->login, 'loginHead'));
        $this->assertStringContainsString('box-shadow: 5px 5px 10px #121212;', $output);

        $this->setDesign([
            OptionKey::LOGIN_FORM_BOX_SHADOW => ' None ',
            OptionKey::LOGIN_FORM_BOX_SHADOW_COLOR => '#ff0000',
        ]);
        $output = $this->capture(fn() => $this->invoke($this->login, 'loginHead'));
        $this->assertStringContainsString('-webkit-box-shadow: none;', $output);
        $this->assertStringContainsString("\tbox-shadow: none;", $output);
        $this->assertStringNotContainsString('#ff0000', $output);
    }

    /**
     * Test loginFooter() only renders the srcset markup when enabled.
     */
    public function testLoginFooter(): void
    {
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->login, 'loginFooter')));

        // Enabled, but the URL isn't an attachment, so the view bails.
        $this->setDesign([
            OptionKey::HTML_USE_IMG_SRCSET => OptionValue::ON,
            OptionKey::HTML_BACKGROUND_URL => 'https://example.com/not-an-attachment.jpg',
        ]);
        $this->assertSame('', trim($this->capture(fn() => $this->invoke($this->login, 'loginFooter'))));
    }

    /**
     * Test loginFooterHtml().
     */
    public function testLoginFooterHtml(): void
    {
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->login, 'loginFooterHtml')));

        $this->setDesign([OptionKey::CUSTOM_HTML => '<p id="footer">Hi</p><script>alert(1)</script>']);
        add_filter('custom_login/login_footer_html', static fn(string $html): string => $html . '<b>Filtered</b>');
        $output = $this->capture(fn() => $this->invoke($this->login, 'loginFooterHtml'));
        $this->assertStringContainsString('<p id="footer">Hi</p>', $output);
        $this->assertStringContainsString('<b>Filtered</b>', $output);
        $this->assertStringNotContainsString('<script>', $output);
    }

    /**
     * Test loginFooterJquery().
     */
    public function testLoginFooterJquery(): void
    {
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->login, 'loginFooterJquery')));

        $this->setDesign([OptionKey::CUSTOM_JQUERY => 'console.log("custom");']);
        $output = $this->capture(fn() => $this->invoke($this->login, 'loginFooterJquery'));
        $this->assertStringContainsString('<script type="text/javascript">', $output);
        $this->assertStringContainsString('console.log("custom");', $output);
    }

    /**
     * Test loginHeaderUrl() and loginHeaderTitle().
     */
    public function testLoginHeader(): void
    {
        $this->assertSame(home_url(), $this->invoke($this->login, 'loginHeaderUrl', 'https://wordpress.org/'));
        $this->assertSame(get_bloginfo('description'), $this->invoke($this->login, 'loginHeaderTitle', 'WordPress'));
    }

    /**
     * Test removeLostPasswordText().
     */
    public function testRemoveLostPasswordText(): void
    {
        global $pagenow;
        $text = 'Lost your password?';
        $pagenow = 'index.php';
        $this->assertSame($text, $this->invoke($this->login, 'removeLostPasswordText', $text, $text));

        $pagenow = 'wp-login.php';
        $this->assertSame('', $this->invoke($this->login, 'removeLostPasswordText', $text, $text));
        $this->assertSame('Log In', $this->invoke($this->login, 'removeLostPasswordText', 'Log In', 'Log In'));

        $this->setGeneral([OptionKey::LOSTPASSWORD_TEXT => OptionValue::OFF]);
        $this->assertSame($text, $this->invoke($this->login, 'removeLostPasswordText', $text, $text));
    }

    /**
     * Get a new Login instance (View data is kept per instance).
     * @return Login
     */
    private function newLogin(): Login
    {
        $login = new Login($this->container);
        $login->setPlugin($this->plugin);

        return $login;
    }

    /**
     * Set the general section options.
     * @param array $options
     */
    private function setGeneral(array $options): void
    {
        update_option(Factory::getSection(Factory::SECTION_GENERAL), $options);
    }

    /**
     * Set the design section options.
     * @param array $options
     */
    private function setDesign(array $options): void
    {
        update_option(Factory::getSection(Factory::SECTION_DESIGN), $options);
    }
}
