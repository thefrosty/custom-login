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
use TheFrosty\CustomLogin\Tests\RedirectException;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpAdmin\Tracking;

/**
 * Class TrackingTest
 * @package TheFrosty\CustomLogin\Tests\WpAdmin
 */
#[CoversClass(Tracking::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(ServiceProvider::class)]
class TrackingTest extends TestCase
{

    private const string HOOK_SEND_CHECKIN = Factory::PREFIX . 'send_check_in';

    private Tracking $tracking;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->tracking = new Tracking($this->container);
        $this->tracking->setPlugin($this->plugin);
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        global $pagenow;
        $pagenow = 'index.php';
        unset($_GET['_wpnonce'], $_GET['page']);
        wp_clear_scheduled_hook(self::HOOK_SEND_CHECKIN, [['on_activation' => 'yes'], true]);
        wp_clear_scheduled_hook(
            self::HOOK_SEND_CHECKIN,
            [['on_activation' => 'settings', 'mailchimp_sub' => 'yes'], true]
        );
        wp_clear_scheduled_hook(
            self::HOOK_SEND_CHECKIN,
            [['on_activation' => 'admin notice', 'mailchimp_sub' => 'yes'], true]
        );
        delete_option(Factory::getSection(Factory::SECTION_GENERAL));
        delete_option(Tracking::OPTION_HIDE_TRACKING_NOTICE);
        delete_option(Tracking::OPTION_TRACKING_LAST_SEND);
        parent::tearDown();
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->tracking->addHooks();
        // sendCheckIn() is hooked twice, but WordPress' callback ID for a method doesn't include the hook.
        $this->assertCount(5, $this->getProperty($this->tracking, 'filter_map'));
    }

    /**
     * Test activate() schedules a forced check-in.
     */
    public function testActivate(): void
    {
        $this->tracking->activate();
        $this->assertIsInt(wp_next_scheduled(self::HOOK_SEND_CHECKIN, [['on_activation' => 'yes'], true]));
        // Scheduling twice keeps the single event.
        $this->tracking->activate();
        $this->assertIsInt(wp_next_scheduled(self::HOOK_SEND_CHECKIN, [['on_activation' => 'yes'], true]));
    }

    /**
     * Test sendCheckIn() respects the opt-in and the weekly limit.
     */
    public function testSendCheckIn(): void
    {
        $requests = [];
        add_filter('pre_http_request', static function ($pre, array $args, string $url) use (&$requests): array {
            $requests[] = ['url' => $url, 'body' => $args['body']];

            return ['headers' => [], 'body' => '{}', 'response' => ['code' => 200], 'cookies' => [], 'filename' => null];
        }, 10, 3);

        // Not opted in.
        $this->invoke($this->tracking, 'sendCheckIn');
        // Opted in, but no previous send is treated as "just sent".
        $this->setGeneral([OptionKey::TRACKING => OptionValue::ON]);
        $this->invoke($this->tracking, 'sendCheckIn');
        $this->assertCount(0, $requests);

        // Last sent over a week ago.
        update_option(Tracking::OPTION_TRACKING_LAST_SEND, strtotime('-2 weeks'));
        $this->invoke($this->tracking, 'sendCheckIn', ['extra' => 'yes']);
        $this->assertCount(1, $requests);
        $this->assertStringContainsString('edd_action=cl_checkin', $requests[0]['url']);
        $this->assertSame(CustomLogin::VERSION, $requests[0]['body']['cl_version']);
        $this->assertSame('yes', $requests[0]['body']['extra']);
        $this->assertArrayHasKey('email', $requests[0]['body']);
        $this->assertGreaterThan(strtotime('-1 minute'), (int)get_option(Tracking::OPTION_TRACKING_LAST_SEND));

        // Forced, but sent recently.
        $this->setGeneral([OptionKey::TRACKING => OptionValue::OFF]);
        $this->invoke($this->tracking, 'sendCheckIn', [], true);
        $this->assertCount(1, $requests);
    }

    /**
     * Test afterSanitizeOptions() schedules a check-in when tracking is turned on.
     */
    public function testAfterSanitizeOptions(): void
    {
        $args = [['on_activation' => 'settings', 'mailchimp_sub' => 'yes'], true];
        $off = [OptionKey::TRACKING => OptionValue::OFF];
        $this->assertSame($off, $this->invoke($this->tracking, 'afterSanitizeOptions', $off));
        $this->assertFalse(wp_next_scheduled(self::HOOK_SEND_CHECKIN, $args));

        $on = [OptionKey::TRACKING => OptionValue::ON];
        $this->assertSame($on, $this->invoke($this->tracking, 'afterSanitizeOptions', $on));
        $this->assertIsInt(wp_next_scheduled(self::HOOK_SEND_CHECKIN, $args));
    }

    /**
     * Test checkForOptIn() and checkForOptOut() require a valid nonce and `manage_options`.
     */
    public function testOptInAndOut(): void
    {
        $this->catchRedirects();
        $section = Factory::getSection(Factory::SECTION_GENERAL);

        // No nonce, then a valid nonce without the capability.
        $this->invoke($this->tracking, 'checkForOptIn');
        $_GET['_wpnonce'] = wp_create_nonce('cl_checkin');
        $this->invoke($this->tracking, 'checkForOptIn');
        $this->invoke($this->tracking, 'checkForOptOut');
        $this->assertFalse(get_option($section));

        $this->loginAdmin();
        $_GET['_wpnonce'] = wp_create_nonce('cl_checkin');
        try {
            $this->invoke($this->tracking, 'checkForOptIn');
            $this->fail('Expected a redirect.');
        } catch (RedirectException) {
            $this->assertSame(OptionValue::ON, get_option($section)[OptionKey::TRACKING]);
            $this->assertSame('1', get_option(Tracking::OPTION_HIDE_TRACKING_NOTICE));
        }

        try {
            $this->invoke($this->tracking, 'checkForOptOut');
            $this->fail('Expected a redirect.');
        } catch (RedirectException) {
            $this->assertSame(OptionValue::OFF, get_option($section)[OptionKey::TRACKING]);
        }
    }

    /**
     * Test adminNotice() only shows on our settings page until dismissed.
     */
    public function testAdminNotice(): void
    {
        global $pagenow;
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->tracking, 'adminNotice')));

        $pagenow = 'options-general.php';
        $_GET['page'] = 'custom-login';
        $output = $this->capture(fn() => $this->invoke($this->tracking, 'adminNotice'));
        $this->assertStringContainsString('cl_opt_into_tracking', $output);
        $this->assertStringContainsString('cl_opt_out_of_tracking', $output);

        update_option(Tracking::OPTION_HIDE_TRACKING_NOTICE, '1');
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->tracking, 'adminNotice')));
    }

    /**
     * Set the general section options.
     * @param array $options
     */
    private function setGeneral(array $options): void
    {
        update_option(Factory::getSection(Factory::SECTION_GENERAL), $options);
    }
}
