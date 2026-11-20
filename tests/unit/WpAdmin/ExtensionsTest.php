<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\WpAdmin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\CustomLogin\Tests\Settings\SettingsTest;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpAdmin\Extensions;

/**
 * Class ExtensionsTest
 * @package TheFrosty\CustomLogin\Tests\WpAdmin
 */
#[CoversClass(Extensions::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(ServiceProvider::class)]
class ExtensionsTest extends TestCase
{

    private Extensions $extensions;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $this->extensions = new Extensions($this->container);
        $this->extensions->setPlugin($this->plugin);
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        global $pagenow, $submenu;
        $pagenow = 'index.php';
        unset($_GET['plugin_status'], $submenu['options-general.php']);
        remove_all_filters('custom_login/plugin_extensions_to_hide');
        delete_option(Factory::getSection(Factory::SECTION_GENERAL));
        parent::tearDown();
    }

    /**
     * Test addHooks() and getExtensions().
     */
    public function testAddHooksAndGetExtensions(): void
    {
        $this->extensions->addHooks();
        $this->assertCount(3, $this->getProperty($this->extensions, 'filter_map'));

        $this->assertSame([], Extensions::getExtensions());
        add_filter('custom_login/plugin_extensions_to_hide', static fn(): array => ['a/a.php', '']);
        $this->assertSame(['a/a.php'], Extensions::getExtensions());
    }

    /**
     * Test adminMenu() registers, renders and (optionally) hides the extensions page.
     */
    public function testAdminMenu(): void
    {
        global $submenu;
        $this->loginAdmin();
        $slug = 'custom-login/extensions';
        $hook = get_plugin_page_hookname($slug, 'options-general.php');

        $this->invoke($this->extensions, 'adminMenu');
        $this->assertContains($slug, array_column($submenu['options-general.php'] ?? [], 2));

        $output = $this->capture(static fn() => do_action($hook));
        $this->assertStringContainsString('Available Custom Login Extensions', $output);
        $this->assertStringContainsString('Custom Login Stealth Login', $output);

        $this->assertSame(1, $this->runMappedHook($this->extensions, 'load-' . $hook));
        $this->assertSame(1, $this->runMappedHook($this->extensions, 'admin_enqueue_scripts'));
        $this->assertTrue(wp_style_is('custom-login'));
        wp_dequeue_style('custom-login');

        // Hidden from the menu when the setting is off.
        unset($submenu['options-general.php']);
        update_option(Factory::getSection(Factory::SECTION_GENERAL), [OptionKey::EXTENSIONS_MENU => OptionValue::OFF]);
        $this->invoke($this->extensions, 'adminMenu');
        $this->assertNotContains($slug, array_column($submenu['options-general.php'] ?? [], 2));
    }

    /**
     * Test sidebarExtensions() only renders on our settings page.
     */
    public function testSidebarExtensions(): void
    {
        $other = SettingsTest::getSettingsApi('another-extensions-plugin');
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->extensions, 'sidebarExtensions', $other)));

        $output = $this->capture(
            fn() => $this->invoke($this->extensions, 'sidebarExtensions', SettingsTest::getSettingsApi())
        );
        $this->assertStringContainsString('custom-login-extensions-installer', $output);
    }

    /**
     * Test maybeHideExtensions().
     */
    public function testMaybeHideExtensions(): void
    {
        global $pagenow;
        $plugins = ['a/a.php' => ['Name' => 'A'], 'b/b.php' => ['Name' => 'B']];
        add_filter('custom_login/plugin_extensions_to_hide', static fn(): array => ['a/a.php']);

        $pagenow = 'index.php';
        $this->assertSame($plugins, $this->invoke($this->extensions, 'maybeHideExtensions', $plugins));

        $pagenow = 'plugins.php';
        $this->assertSame(['b/b.php'], array_keys($this->invoke($this->extensions, 'maybeHideExtensions', $plugins)));

        $_GET['plugin_status'] = 'all';
        $this->assertSame($plugins, $this->invoke($this->extensions, 'maybeHideExtensions', $plugins));
    }
}
