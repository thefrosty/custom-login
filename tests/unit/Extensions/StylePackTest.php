<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Extensions;

use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Extensions\StylePack;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\Sanitize;
use TheFrosty\CustomLogin\Tests\RedirectException;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\WpUtilities\Plugin\Plugin;
use WPDieException;

/**
 * Class StylePackTest
 * @package TheFrosty\CustomLogin\Tests\Extensions
 */
#[CoversTrait(StylePack::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(Sanitize::class)]
class StylePackTest extends TestCase
{

    private object $style_pack;
    private string $dir;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        if (!function_exists('add_settings_error')) {
            require_once ABSPATH . 'wp-admin/includes/template.php';
        }
        $this->dir = trailingslashit(sys_get_temp_dir()) . 'custom-login-style-pack-' . uniqid();
        wp_mkdir_p($this->dir . '/styles');
        file_put_contents(
            $this->dir . '/styles/test_style.php',
            sprintf(
                '<?php return %s;',
                var_export([
                    Factory::getSection(Factory::SECTION_DESIGN) => [OptionKey::LABEL_COLOR => '#abcdef'],
                    'active_plugins_style_pack' => ['evil/evil.php'],
                ], true)
            )
        );
        $this->style_pack = new class ($this->plugin, $this->dir . '/style-pack.php') {
            use StylePack;

            public array $fields = [
                ['name' => 'test_style', 'label' => 'Test Style'],
                ['name' => 'missing_style', 'label' => 'Missing Style'],
            ];

            public function __construct(public Plugin $parent, public string $file)
            {
            }
        };
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        unset($_GET['action'], $_GET['cl_nonce'], $_GET['style_pack_updated'], $_GET['style_pack_label']);
        array_map('unlink', glob($this->dir . '/styles/*.php') ?: []);
        rmdir($this->dir . '/styles');
        rmdir($this->dir);
        delete_option(Factory::getSection(Factory::SECTION_DESIGN));
        delete_option('active_plugins_style_pack');
        $GLOBALS['wp_settings_errors'] = [];
        parent::tearDown();
    }

    /**
     * Test addSettingsError().
     */
    public function testAddSettingsError(): void
    {
        $this->invoke($this->style_pack, 'addSettingsError');
        $this->assertSame([], get_settings_errors('style_pack'));

        $_GET['style_pack_updated'] = '1';
        $_GET['style_pack_label'] = '<b>Test</b>';
        $this->invoke($this->style_pack, 'addSettingsError');
        $errors = get_settings_errors('style_pack');
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('&lt;b&gt;Test&lt;/b&gt;', $errors[0]['message']);
        $this->assertArrayNotHasKey('style_pack_updated', $_GET);
    }

    /**
     * Test maybeImportStylePack() requires a valid nonce and `manage_options`.
     */
    public function testMaybeImportStylePackRequiresNonceAndCapability(): void
    {
        try {
            $this->invoke($this->style_pack, 'maybeImportStylePack');
            $this->fail('Expected wp_die() without a nonce.');
        } catch (WPDieException) {
            $this->assertTrue(true);
        }

        $_GET['action'] = 'custom-login_style_pack_test_style';
        $_GET['cl_nonce'] = wp_create_nonce('style_pack');
        $this->expectException(WPDieException::class);
        $this->invoke($this->style_pack, 'maybeImportStylePack');
    }

    /**
     * Test maybeImportStylePack() imports the plugin's own options from the style file.
     */
    public function testMaybeImportStylePack(): void
    {
        $this->loginAdmin();
        $this->catchRedirects();
        $_GET['action'] = 'custom-login_style_pack_test_style';
        $_GET['cl_nonce'] = wp_create_nonce('style_pack');

        try {
            $this->invoke($this->style_pack, 'maybeImportStylePack');
            $this->fail('Expected a redirect.');
        } catch (RedirectException $exception) {
            $this->assertStringContainsString('style_pack_updated=1', $exception->location);
            $this->assertStringContainsString('style_pack_label=Test', $exception->location);
        }
        $this->assertSame('#abcdef', get_option(Factory::getSection(Factory::SECTION_DESIGN))[OptionKey::LABEL_COLOR]);
        $this->assertFalse(get_option('active_plugins_style_pack'));
    }

    /**
     * Test maybeImportStylePack() redirects back when the style isn't found.
     */
    public function testMaybeImportStylePackMissingStyle(): void
    {
        $this->loginAdmin();
        $this->catchRedirects();
        $_GET['cl_nonce'] = wp_create_nonce('style_pack');

        foreach (['custom-login_style_pack_missing_style', 'custom-login_style_pack_unknown'] as $action) {
            $_GET['action'] = $action;
            try {
                $this->invoke($this->style_pack, 'maybeImportStylePack');
                $this->fail('Expected a redirect.');
            } catch (RedirectException $exception) {
                $this->assertStringContainsString('page=custom-login', $exception->location);
                $this->assertStringNotContainsString('style_pack_updated', $exception->location);
            }
        }
        $this->assertFalse(get_option(Factory::getSection(Factory::SECTION_DESIGN)));
    }

    /**
     * Test buildActionUrl().
     */
    public function testBuildActionUrl(): void
    {
        $this->assertSame('', $this->invoke($this->style_pack, 'buildActionUrl'));
        $url = $this->invoke($this->style_pack, 'buildActionUrl', 'test_style');
        $this->assertStringContainsString('action=custom-login_style_pack_test_style', $url);
        $this->assertStringContainsString('cl_nonce=', $url);
    }
}
