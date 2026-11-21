<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use TheFrosty\CustomLogin\Api\Cron;
use TheFrosty\CustomLogin\Tests\TestCase;

/**
 * Class CronTest
 * @package TheFrosty\CustomLogin\Tests\Api
 */
#[CoversClass(Cron::class)]
class CronTest extends TestCase
{

    private Cron $cron;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->cron = new Cron();
        $this->cron->setPlugin($this->plugin);
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->cron->addHooks();
        $this->assertCount(2, $this->getProperty($this->cron, 'filter_map'));
    }

    /**
     * Test scheduleEvents().
     */
    public function testScheduleEvents(): void
    {
        wp_clear_scheduled_hook(Cron::HOOK_DAILY);
        wp_clear_scheduled_hook(Cron::HOOK_WEEKLY);
        $this->invoke($this->cron, 'scheduleEvents');
        $this->assertIsInt(wp_next_scheduled(Cron::HOOK_DAILY));
        $this->assertIsInt(wp_next_scheduled(Cron::HOOK_WEEKLY));
        // Already scheduled, so nothing changes.
        $daily = wp_next_scheduled(Cron::HOOK_DAILY);
        $this->invoke($this->cron, 'scheduleEvents');
        $this->assertSame($daily, wp_next_scheduled(Cron::HOOK_DAILY));
        wp_clear_scheduled_hook(Cron::HOOK_DAILY);
        wp_clear_scheduled_hook(Cron::HOOK_WEEKLY);
    }

    /**
     * Test maybeAddSchedule().
     */
    public function testMaybeAddSchedule(): void
    {
        $schedules = $this->invoke($this->cron, 'maybeAddSchedule', []);
        $this->assertSame(604800, $schedules['weekly']['interval']);

        $existing = ['weekly' => ['interval' => 1, 'display' => 'Existing']];
        $this->assertSame($existing, $this->invoke($this->cron, 'maybeAddSchedule', $existing));
    }
}
