<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\WpAdmin;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\CustomLogin\Tests\TestCase;
use TheFrosty\CustomLogin\WpAdmin\SettingsUpgrades;
use WPDieException;

/**
 * Class SettingsUpgradesTest
 * @package TheFrosty\CustomLogin\Tests\WpAdmin
 */
#[CoversClass(SettingsUpgrades::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(ServiceProvider::class)]
class SettingsUpgradesTest extends TestCase
{

    private SettingsUpgrades $upgrades;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $this->upgrades = new SettingsUpgrades($this->container);
        $this->upgrades->setPlugin($this->plugin);
        $this->upgrades->addHooks();
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        global $submenu;
        unset($submenu['options-general.php'], $_REQUEST['_ajax_nonce']);
        delete_option(SettingsUpgrades::OPTION_VERSION);
        $this->resetAjax();
        parent::tearDown();
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->assertCount(3, $this->getProperty($this->upgrades, 'filter_map'));
        $this->assertSame('custom-login/upgrades', $this->getProperty($this->upgrades, 'menu_slug'));
    }

    /**
     * Test adminNotice() only shows for old database versions.
     */
    public function testAdminNotice(): void
    {
        $this->assertSame('', $this->capture(fn() => $this->invoke($this->upgrades, 'adminNotice')));

        update_option(SettingsUpgrades::OPTION_VERSION, '3.2.15');
        $output = $this->capture(fn() => $this->invoke($this->upgrades, 'adminNotice'));
        $this->assertStringContainsString('notice-warning', $output);
        $this->assertStringContainsString('page=custom-login/upgrades', $output);
    }

    /**
     * Test adminMenu() registers a hidden page that renders the upgrade script.
     */
    public function testAdminMenu(): void
    {
        global $submenu;
        $this->loginAdmin();
        $this->invoke($this->upgrades, 'adminMenu');
        $this->assertNotContains('custom-login/upgrades', array_column($submenu['options-general.php'] ?? [], 2));

        $hook = get_plugin_page_hookname('custom-login/upgrades', 'options-general.php');
        $output = $this->capture(static fn() => do_action($hook));
        $this->assertStringContainsString(SettingsUpgrades::AJAX_ACTION, $output);
    }

    /**
     * Test triggerUpgrades() checks the nonce and capability, then updates the version.
     */
    public function testTriggerUpgrades(): void
    {
        $this->throwOnAjaxDie();

        // Invalid nonce.
        $_REQUEST['_ajax_nonce'] = 'invalid';
        $this->assertDies();

        // Valid nonce, no capability.
        $_REQUEST['_ajax_nonce'] = wp_create_nonce(SettingsUpgrades::ACTION_NONCE);
        $this->assertStringContainsString('"success":false', $this->assertDies());
        $this->assertFalse(get_option(SettingsUpgrades::OPTION_VERSION));

        // Valid nonce and capability.
        $this->loginAdmin();
        $_REQUEST['_ajax_nonce'] = wp_create_nonce(SettingsUpgrades::ACTION_NONCE);
        $this->assertStringContainsString('"success":true', $this->assertDies());
        $this->assertSame(CustomLogin::VERSION, get_option(SettingsUpgrades::OPTION_VERSION));
    }

    /**
     * Run triggerUpgrades() and assert it ends with wp_die().
     * @return string The output.
     */
    private function assertDies(): string
    {
        $died = false;
        $output = $this->capture(function () use (&$died): void {
            try {
                $this->invoke($this->upgrades, 'triggerUpgrades');
            } catch (WPDieException) {
                $died = true;
            }
        });
        $this->assertTrue($died);

        return $output;
    }
}
