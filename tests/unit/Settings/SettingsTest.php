<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Settings;

use Dwnload\WpSettingsApi\Api\Style;
use Dwnload\WpSettingsApi\SettingsApiFactory;
use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use Dwnload\WpSettingsApi\WpSettingsApi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\Settings;
use TheFrosty\CustomLogin\Tests\TestCase;

/**
 * Class SettingsTest
 * @package TheFrosty\CustomLogin\Tests\Settings
 */
#[CoversClass(Settings::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(ServiceProvider::class)]
class SettingsTest extends TestCase
{

    private Settings $settings;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->settings = new Settings($this->container);
        $this->settings->setPlugin($this->plugin);
    }

    /**
     * Get a settings API instance.
     * @param string|null $slug
     * @return WpSettingsApi
     */
    public static function getSettingsApi(?string $slug = null): WpSettingsApi
    {
        if ($slug === null) {
            return new WpSettingsApi(
                Factory::getPluginSettings(\TheFrosty\WpUtilities\Plugin\PluginFactory::create('custom-login'))
            );
        }

        return new WpSettingsApi(SettingsApiFactory::create([
            'domain' => $slug,
            'file' => __FILE__,
            'menu-slug' => $slug,
            'menu-title' => $slug,
            'page-title' => $slug,
            'prefix' => $slug,
            'version' => '1.0.0',
        ]));
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->settings->addHooks();
        $this->assertCount(5, $this->getProperty($this->settings, 'filter_map'));
    }

    /**
     * Test init() registers the sections and fields for our menu slug only.
     */
    public function testInit(): void
    {
        $api = self::getSettingsApi('another-plugin');
        $this->invoke($this->settings, 'init', new SectionManager($api), new FieldManager(), $api);
        $this->assertSame([], SectionManager::getSection('another-plugin'));

        $api = self::getSettingsApi();
        $this->invoke($this->settings, 'init', new SectionManager($api), new FieldManager(), $api);
        $ids = array_map(static fn($section): string => $section->getId(), SectionManager::getSection('custom-login'));
        $this->assertContains(Factory::getSection(Factory::SECTION_DESIGN), $ids);
        $this->assertContains(Factory::getSection(Factory::SECTION_GENERAL), $ids);
        $this->assertNotEmpty(FieldManager::getFields()[Factory::getSection(Factory::SECTION_DESIGN)]);
    }

    /**
     * Test adminScripts().
     */
    public function testAdminScripts(): void
    {
        $this->assertSame(['x'], $this->invoke($this->settings, 'adminScripts', ['x']));
        $this->assertTrue(wp_script_is('code-editor', 'enqueued'));
    }

    /**
     * Test adminStyles().
     */
    public function testAdminStyles(): void
    {
        $styles = $this->invoke($this->settings, 'adminStyles', []);
        $this->assertCount(1, $styles);
        $this->assertInstanceOf(Style::class, $styles[0]);
    }

    /**
     * Test the sidebars only render on our settings page.
     */
    public function testSidebars(): void
    {
        $this->fakeHttp([
            (object)[
                'link' => 'https://frosty.media/plugins/example/',
                'title' => (object)['rendered' => 'Example Extension'],
                'date' => '2026-01-01T00:00:00',
                'content' => (object)['rendered' => '<p>Content</p>'],
            ],
        ]);
        $other = self::getSettingsApi('another-plugin');
        $api = self::getSettingsApi();

        $this->assertSame('', $this->capture(fn() => $this->invoke($this->settings, 'sidebarAboutTheAuthor', $other)));
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->settings, 'sidebarExtensions', $other)));

        $about = $this->capture(fn() => $this->invoke($this->settings, 'sidebarAboutTheAuthor', $api));
        $this->assertStringContainsString('frosty-media-author', $about);
        $this->assertStringContainsString('https://x.com/TheFrosty', $about);

        $extensions = $this->capture(fn() => $this->invoke($this->settings, 'sidebarExtensions', $api));
        $this->assertStringContainsString('custom-login-extensions', $extensions);
        $this->assertStringContainsString('Example Extension', $extensions);
    }
}
