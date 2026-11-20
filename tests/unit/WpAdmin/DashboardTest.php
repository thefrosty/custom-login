<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\WpAdmin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use ReflectionClass;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\OptionValue;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpAdmin\Dashboard;
use TheFrosty\WpUtilities\Utils\View;

/**
 * Class DashboardTest
 * @package TheFrosty\CustomLogin\Tests\WpAdmin
 */
#[CoversClass(Dashboard::class)]
#[UsesClass(Factory::class)]
class DashboardTest extends TestCase
{

    private Dashboard $dashboard;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->dashboard = new Dashboard(Dashboard::getArgs());
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        delete_option(Factory::getSection(Factory::SECTION_GENERAL));
        parent::tearDown();
    }

    /**
     * Test getArgs().
     */
    public function testGetArgs(): void
    {
        $this->assertSame('frosty-media-feed', $this->dashboard->getWidget()->getWidgetId());
        $this->assertStringStartsWith('https://frosty.media/', $this->dashboard->getWidget()->getFeedUrl());
    }

    /**
     * Test addHooks() registers the allowed filter on `load-index.php`.
     */
    public function testAddHooks(): void
    {
        $this->dashboard->addHooks();
        $this->assertSame(1, $this->runMappedHook($this->dashboard, 'load-index.php'));
        $filter = sprintf(Dashboard::HOOK_NAME_ALLOWED_S, 'frosty-media-feed');
        $this->assertSame(1, $this->runMappedHook($this->dashboard, $filter, true));
    }

    /**
     * Test checkAllowDashboard().
     */
    public function testCheckAllowDashboard(): void
    {
        $this->assertTrue($this->invoke($this->dashboard, 'checkAllowDashboard', true));
        update_option(
            Factory::getSection(Factory::SECTION_GENERAL),
            [OptionKey::DASHBOARD_WIDGET => OptionValue::OFF]
        );
        $this->assertFalse($this->invoke($this->dashboard, 'checkAllowDashboard', true));
    }

    /**
     * Test renderWidget() adds the latest extension to the widget.
     */
    public function testRenderWidget(): void
    {
        $this->fakeHttp([
            (object)[
                'link' => 'https://frosty.media/plugins/dashboard-extension/',
                'title' => (object)['rendered' => 'Dashboard Extension'],
                'date' => '2026-01-01T00:00:00',
                'content' => (object)['rendered' => '<p>Content</p>'],
            ],
        ]);
        $template = dirname((new ReflectionClass(View::class))->getFileName(), 3) . '/views/dashboard-widget/rest.php';
        $output = $this->capture(
            fn() => $this->invoke($this->dashboard, 'renderWidget', '<div class="open">', '</div>', $template)
        );
        $this->assertStringContainsString('<div class="open">', $output);
        $this->assertStringContainsString('Custom Login Extensions', $output);
        $this->assertStringContainsString('Dashboard Extension', $output);
    }
}
