<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\CustomLogin\WpAdmin\Extensions;

/**
 * Class CustomLoginTest
 * @package TheFrosty\Tests\CustomLogin
 */
#[CoversClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(Extensions::class)]
class CustomLoginTest extends TestCase
{

    private CustomLogin $custom_login;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->custom_login = new CustomLogin();
        $this->custom_login->setPlugin($this->plugin);
        $this->reflection = $this->getReflection($this->custom_login);
    }

    /**
     * Teardown.
     */
    public function tearDown(): void
    {
        remove_all_filters('custom_login/plugin_extensions_to_hide');
        remove_all_filters(CustomLogin::OPTION . '_disable_meta_generator');
        putenv('CUSTOM_LOGIN_API_URL');
        unset($this->custom_login);
        parent::tearDown();
    }

    /**
     * Test CustomLogin.
     */
    public function testCustomLogin(): void
    {
        $this->assertInstanceOf(CustomLogin::class, $this->custom_login);
        $this->assertCount(3, $this->reflection->getConstants());
    }

    /**
     * Test getApiUrl().
     */
    public function testGetApiUrl(): void
    {
        $this->assertSame('https://frosty.media/checkout/', CustomLogin::getApiUrl('checkout/'));
        putenv('CUSTOM_LOGIN_API_URL=https://example.test/');
        $this->assertSame('https://example.test/api', CustomLogin::getApiUrl('api'));
    }

    /**
     * Test getSettings().
     */
    public function testGetSettings(): void
    {
        $settings = CustomLogin::getSettings();
        $this->assertArrayHasKey('sections', $settings);
        $this->assertArrayHasKey('fields', $settings);
        $this->assertArrayHasKey(Factory::getSection(Factory::SECTION_DESIGN), $settings['fields']);
    }

    /**
     * Test activate().
     */
    public function testActivate(): void
    {
        $section = Factory::getSection(Factory::SECTION_GENERAL);
        update_option($section, [OptionKey::ACTIVE => OptionValue::OFF]);
        $this->custom_login->activate();
        $this->assertSame(OptionValue::ON, get_option($section)[OptionKey::ACTIVE]);
        delete_option($section);
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $actions = did_action(Factory::PREFIX . 'actions');
        $this->custom_login->addHooks();
        $this->assertSame($actions + 1, did_action(Factory::PREFIX . 'actions'));
        $this->assertNotEmpty($this->getProperty($this->custom_login, 'filter_map'));
    }

    /**
     * Test pluginActionLinks().
     */
    public function testPluginActionLinks(): void
    {
        $basename = $this->plugin->getBasename();
        $this->assertSame(['x'], $this->invoke($this->custom_login, 'pluginActionLinks', ['x'], 'other/other.php'));

        $actions = $this->invoke($this->custom_login, 'pluginActionLinks', ['deactivate'], $basename);
        $this->assertCount(2, $actions);
        $this->assertStringContainsString('options-general.php?page=custom-login', $actions[0]);

        add_filter('custom_login/plugin_extensions_to_hide', static fn(): array => ['ext/ext.php']);
        $actions = $this->invoke($this->custom_login, 'pluginActionLinks', ['deactivate'], $basename);
        $this->assertCount(3, $actions);
        $this->assertStringContainsString('plugin_status=all', $actions[2]);
    }

    /**
     * Test pluginRowMeta().
     */
    public function testPluginRowMeta(): void
    {
        $this->assertSame(['x'], $this->invoke($this->custom_login, 'pluginRowMeta', ['x'], 'other/other.php'));
        $meta = $this->invoke($this->custom_login, 'pluginRowMeta', ['x'], $this->plugin->getBasename());
        $this->assertCount(3, $meta);
        $this->assertStringContainsString('custom-login/extensions', $meta[1]);
    }

    /**
     * Test i18n().
     */
    public function testI18n(): void
    {
        $this->invoke($this->custom_login, 'i18n');
        $this->assertTrue(true); // Loading a missing translation file is a no-op.
    }

    /**
     * Test metaGenerator().
     */
    public function testMetaGenerator(): void
    {
        $output = $this->capture(fn() => $this->invoke($this->custom_login, 'metaGenerator'));
        $this->assertStringContainsString('content="Custom Login ' . CustomLogin::VERSION . '"', $output);

        add_filter(CustomLogin::OPTION . '_disable_meta_generator', '__return_true');
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->custom_login, 'metaGenerator')));
    }
}
