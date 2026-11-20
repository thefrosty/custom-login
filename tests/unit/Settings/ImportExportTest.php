<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Settings;

use Dwnload\WpSettingsApi\Settings\FieldManager;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\ImportExport;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\Sanitize;
use TheFrosty\CustomLogin\Tests\RedirectException;
use TheFrosty\CustomLogin\Tests\TestCase;

/**
 * Class ImportExportTest
 * @package TheFrosty\CustomLogin\Tests\Settings
 */
#[CoversClass(ImportExport::class)]
#[UsesClass(CustomLogin::class)]
#[UsesClass(Factory::class)]
#[UsesClass(Sanitize::class)]
class ImportExportTest extends TestCase
{

    private ImportExport $import_export;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->import_export = new ImportExport($this->container);
        $this->import_export->setPlugin($this->plugin);
        if (!function_exists('add_settings_error')) {
            require_once ABSPATH . 'wp-admin/includes/template.php';
        }
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        unset($_GET['action'], $_GET[ImportExport::NONCE]);
        delete_option(Factory::getSection(Factory::SECTION_DESIGN));
        delete_option('active_plugins_test');
        $GLOBALS['wp_settings_errors'] = [];
        parent::tearDown();
    }

    /**
     * Test addHooks().
     */
    public function testAddHooks(): void
    {
        $this->import_export->addHooks();
        $this->assertCount(3, $this->getProperty($this->import_export, 'filter_map'));
    }

    /**
     * Test init() registers the import/export section for our menu slug only.
     */
    public function testInit(): void
    {
        $api = SettingsTest::getSettingsApi('another-import-plugin');
        $this->invoke($this->import_export, 'init', new SectionManager($api), new FieldManager(), $api);
        $this->assertSame([], SectionManager::getSection('another-import-plugin'));

        $api = SettingsTest::getSettingsApi();
        $this->invoke($this->import_export, 'init', new SectionManager($api), new FieldManager(), $api);
        $section = Factory::getSection(Factory::SECTION_IMPORT_EXPORT);
        $fields = FieldManager::getFields()[$section];
        $this->assertNotEmpty($fields);

        // The export field defaults to the encoded settings and sanitizes back to them.
        $export = array_values(array_filter($fields, static fn($f): bool => $f->getName() === OptionKey::SETTINGS_EXPORT));
        $decoded = json_decode(base64_decode($export[0]->getDefault()), true);
        $this->assertArrayHasKey(Factory::getSection(Factory::SECTION_DESIGN), $decoded);
        $this->assertSame($export[0]->getDefault(), call_user_func($export[0]->getSanitizeCallback()));
    }

    /**
     * Import only writes the plugin's own options, and only for users who can manage options.
     */
    public function testMaybeImportSettings(): void
    {
        $design = Factory::getSection(Factory::SECTION_DESIGN);
        $payload = base64_encode(json_encode([
            $design => [OptionKey::LABEL_COLOR => '#123456'],
            'active_plugins_test' => ['evil/evil.php'],
        ]));

        // Logged out: nothing happens.
        $this->invoke($this->import_export, 'maybeImportSettings', [OptionKey::SETTINGS_IMPORT => $payload]);
        $this->assertFalse(get_option($design));

        $this->loginAdmin();
        // Not base64, or not a string: nothing happens.
        $this->invoke($this->import_export, 'maybeImportSettings', [OptionKey::SETTINGS_IMPORT => 'not base64!']);
        $this->invoke($this->import_export, 'maybeImportSettings', [OptionKey::SETTINGS_IMPORT => ['array']]);
        $this->invoke($this->import_export, 'maybeImportSettings', []);
        $this->assertFalse(get_option($design));

        $this->invoke($this->import_export, 'maybeImportSettings', [OptionKey::SETTINGS_IMPORT => $payload]);
        $this->assertSame('#123456', get_option($design)[OptionKey::LABEL_COLOR]);
        $this->assertFalse(get_option('active_plugins_test'));
        $this->assertNotEmpty(get_settings_errors($design));
    }

    /**
     * The export download requires a valid nonce and `manage_options`.
     */
    public function testDownloadSettingsExportRequiresNonceAndCapability(): void
    {
        $this->catchRedirects();

        try {
            $this->invoke($this->import_export, 'downloadSettingsExport');
            $this->fail('Expected a redirect without a nonce.');
        } catch (RedirectException $exception) {
            $this->assertStringNotContainsString(ImportExport::NONCE, $exception->location);
        }

        $_GET['action'] = ImportExport::ACTION_DOWNLOAD_EXPORT;
        $_GET[ImportExport::NONCE] = wp_create_nonce('export');
        $this->expectException(RedirectException::class);
        $this->invoke($this->import_export, 'downloadSettingsExport'); // Logged out, so no capability.
    }
}
