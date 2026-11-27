<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\WpAdmin;

use PHPUnit\Framework\Attributes\CoversClass;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpAdmin\DisableAutoUpdates;

/**
 * Class DisableAutoUpdatesTest
 * @package TheFrosty\CustomLogin\Tests\WpAdmin
 */
#[CoversClass(DisableAutoUpdates::class)]
class DisableAutoUpdatesTest extends TestCase
{

    private const string OTHER_PLUGIN = 'hello.php';

    private DisableAutoUpdates $disable_auto_updates;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->disable_auto_updates = new DisableAutoUpdates();
        $this->disable_auto_updates->setPlugin($this->plugin);
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->disable_auto_updates->addHooks();
        $this->assertCount(2, $this->getProperty($this->disable_auto_updates, 'filter_map'));
        $this->assertSame(
            1,
            $this->runMappedHook($this->disable_auto_updates, 'auto_update_plugin', true, $this->item())
        );
    }

    /**
     * Custom Login is never auto-updated; other plugins keep their value.
     */
    public function testDisableAutoUpdate(): void
    {
        foreach ([true, false, null] as $update) {
            $this->assertFalse(
                $this->invoke($this->disable_auto_updates, 'disableAutoUpdate', $update, $this->item())
            );
            $other = $this->item(self::OTHER_PLUGIN);
            $this->assertSame(
                $update,
                $this->invoke($this->disable_auto_updates, 'disableAutoUpdate', $update, $other)
            );
        }
        $this->assertTrue($this->invoke($this->disable_auto_updates, 'disableAutoUpdate', true, null));
        $this->assertSame(1, $this->invoke($this->disable_auto_updates, 'disableAutoUpdate', 1, (object)[]));
    }

    /**
     * The Plugins screen shows a note instead of the auto-updates toggle for Custom Login only.
     */
    public function testAutoUpdateSettingHtml(): void
    {
        $toggle = '<a href="#">Enable auto-updates</a>';
        $html = $this->invoke(
            $this->disable_auto_updates,
            'autoUpdateSettingHtml',
            $toggle,
            $this->plugin->getBasename()
        );
        $this->assertStringNotContainsString('Enable auto-updates', $html);
        $this->assertStringContainsString('Composer', $html);
        $this->assertSame(
            $toggle,
            $this->invoke($this->disable_auto_updates, 'autoUpdateSettingHtml', $toggle, self::OTHER_PLUGIN)
        );
    }

    /**
     * An update offer, as WP_Automatic_Updater passes it to `auto_update_plugin`.
     * @param string|null $plugin
     * @return object
     */
    private function item(?string $plugin = null): object
    {
        return (object)['plugin' => $plugin ?? $this->plugin->getBasename()];
    }
}
