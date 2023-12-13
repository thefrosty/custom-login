<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Api;

use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use function array_key_exists;
use function current_time;
use function wp_next_scheduled;
use function wp_schedule_event;

/**
 * Class Cron
 * @package TheFrosty\CustomLogin\Api
 */
class Cron extends AbstractHookProvider
{

    public const HOOK_DAILY = 'custom_login_daily_scheduled_events';
    public const HOOK_WEEKLY = 'custom_login_weekly_scheduled_events';

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        $this->addAction('wp', [$this, 'scheduleEvents']);
        $this->addFilter('cron_schedules', [$this, 'maybeAddSchedule']);
    }

    /**
     * Schedules Custom Login cron events.
     * @since 1.6
     */
    protected function scheduleEvents(): void
    {
        $this->dailyEvents();
        $this->weeklyEvents();
    }

    /**
     * Registers a new cron schedule "weekly" if it doesn't exist.
     * @param array $schedules
     * @return array
     * @since 1.6
     */
    protected function maybeAddSchedule(array $schedules = []): array
    {
        if (!array_key_exists('weekly', $schedules)) {
            $schedules['weekly'] = [
                'interval' => 604800,
                'display' => esc_html__('Once Weekly', 'custom-login'),
            ];
        }

        return $schedules;
    }

    /**
     * Schedule daily events.
     * @since 1.6
     */
    private function dailyEvents(): void
    {
        if (!wp_next_scheduled(self::HOOK_DAILY)) {
            wp_schedule_event(current_time('timestamp'), 'daily', self::HOOK_DAILY);
        }
    }

    /**
     * Schedule weekly events.
     * @since 1.6
     */
    private function weeklyEvents(): void
    {
        if (!wp_next_scheduled(self::HOOK_WEEKLY)) {
            wp_schedule_event(current_time('timestamp'), 'weekly', self::HOOK_WEEKLY);
        }
    }
}
