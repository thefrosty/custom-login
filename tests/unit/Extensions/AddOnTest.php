<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Extensions;

use Dwnload\EddSoftwareLicenseManager\Edd\AbstractLicenceManager;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\Extensions\AddOn;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Tests\Settings\SettingsTest;
use TheFrosty\CustomLogin\Tests\TestCase;
use WP_Screen;

/**
 * Class AddOnTest
 * @package TheFrosty\CustomLogin\Tests\Extensions
 */
#[CoversClass(AddOn::class)]
#[UsesClass(Factory::class)]
class AddOnTest extends TestCase
{

    private AddOn $addon;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->addon = self::newAddOn($this->plugin);
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        $GLOBALS['current_screen'] = null;
        delete_option(AbstractLicenceManager::LICENSE_SETTING);
        remove_all_filters('dwnload_edd_slm_licenses');
        remove_all_filters('dwnload_edd_slm_use_local_scripts');
        remove_all_filters('custom_login/plugin_extensions_to_hide');
        parent::tearDown();
    }

    /**
     * Get a concrete AddOn.
     * @param \TheFrosty\WpUtilities\Plugin\Plugin $plugin
     * @return AddOn
     */
    public static function newAddOn(\TheFrosty\WpUtilities\Plugin\Plugin $plugin): AddOn
    {
        return new class (
            $plugin,
            WP_PLUGIN_DIR . '/custom-login-test-addon/custom-login-test-addon.php',
            '1.0.0',
            'ignored',
            'test_addon',
            'Test <b>AddOn</b>'
        ) extends AddOn {
            public bool $initialized = false;

            protected function init(
                SectionManager $section_manager,
                FieldManager $field_manager,
                WpSettingsApi $wp_settings_api
            ): void {
                $this->initialized = true;
            }

            protected function getSettingsFields(): array
            {
                return [['name' => 'test_style', 'label' => 'Test Style']];
            }
        };
    }

    /**
     * Test the constructor.
     */
    public function testConstruct(): void
    {
        $this->assertSame('custom-login', $this->addon->domain);
        $this->assertSame(Factory::PREFIX . 'test_addon', $this->addon->plugin_id);
        $this->assertSame([['name' => 'test_style', 'label' => 'Test Style']], $this->getProperty($this->addon, 'fields'));
    }

    /**
     * Test addHooks() registers the license and extension filters on `init`.
     */
    public function testAddHooks(): void
    {
        require_once ABSPATH . 'wp-admin/includes/class-wp-screen.php';
        require_once ABSPATH . 'wp-admin/includes/screen.php';
        $GLOBALS['current_screen'] = WP_Screen::get('dashboard');

        $this->addon->addHooks();
        $this->assertSame(1, $this->runMappedHook($this->addon, 'init'));

        $licenses = apply_filters('dwnload_edd_slm_licenses', []);
        $this->assertSame('Test &lt;b&gt;AddOn&lt;/b&gt;', $licenses[Factory::PREFIX . 'test_addon']);
        $this->assertTrue(apply_filters('dwnload_edd_slm_use_local_scripts', false));
        $this->assertSame(
            ['custom-login-test-addon/custom-login-test-addon.php'],
            apply_filters('custom_login/plugin_extensions_to_hide', [])
        );
        $api = SettingsTest::getSettingsApi();
        $this->assertSame(
            1,
            $this->runMappedHook($this->addon, WpSettingsApi::HOOK_INIT, new SectionManager($api), new FieldManager(), $api)
        );
        $this->assertTrue($this->addon->initialized);
    }

    /**
     * Test getLicense().
     */
    public function testGetLicense(): void
    {
        $this->assertSame('', $this->addon->getLicense());
        update_option(AbstractLicenceManager::LICENSE_SETTING, [
            Factory::PREFIX . 'test_addon' => ['license' => 'abc123'],
        ]);
        $this->assertSame('abc123', $this->addon->getLicense());
    }

    /**
     * Test pluginActionLinks().
     */
    public function testPluginActionLinks(): void
    {
        $this->assertSame(['x'], $this->invoke($this->addon, 'pluginActionLinks', ['x'], 'other/other.php'));
        $links = $this->invoke(
            $this->addon,
            'pluginActionLinks',
            ['x'],
            'custom-login-test-addon/custom-login-test-addon.php'
        );
        $this->assertCount(2, $links);
        $this->assertStringContainsString('#' . Factory::PREFIX . 'test_addon', $links[0]);
    }
}
