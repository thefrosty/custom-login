<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Settings\OptionKey;
use TheFrosty\CustomLogin\Settings\Sanitize;

/**
 * Class SanitizeTest
 * @package TheFrosty\CustomLogin\Tests
 */
class SanitizeTest extends TestCase
{

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        remove_all_filters('user_has_cap');
        delete_option(Factory::getSection(Factory::SECTION_DESIGN));
        wp_set_current_user(0);
        parent::tearDown();
    }

    /**
     * A `</style>` sequence (from custom CSS or decoded entities) can never close the style element.
     */
    public function testCssForStyleElementCannotCloseStyle(): void
    {
        $css = Sanitize::cssForStyleElement('body{color:red}</style><script>alert(1)</script>');
        $this->assertStringNotContainsString('<', $css);
        $this->assertStringNotContainsStringIgnoringCase('</style', $css);
    }

    /**
     * CSS values cannot end a declaration, a rule, or the style element.
     */
    public function testCssValue(): void
    {
        $value = Sanitize::cssValue('#fff;}</style><script>');
        foreach ([';', '}', '{', '<', '>', '/', "'", '"'] as $char) {
            $this->assertStringNotContainsString($char, $value);
        }
        $this->assertSame('rgba(0, 0, 0, .5)', Sanitize::cssValue('rgba(0, 0, 0, .5)'));
        $this->assertSame('0 0 5px', Sanitize::cssValue('0 0 5px'));
        $this->assertSame('', Sanitize::cssValue(['array']));
    }

    /**
     * URLs cannot break out of `url('...')`.
     */
    public function testCssUrl(): void
    {
        $url = Sanitize::cssUrl("https://example.com/a.png');}body{display:none");
        $this->assertStringNotContainsString("'", $url);
        $this->assertStringNotContainsString(')', $url);
        $this->assertSame('https://example.com/a.png', Sanitize::cssUrl('https://example.com/a.png'));
        $this->assertSame('', Sanitize::cssUrl('javascript:alert(1)'));
    }

    /**
     * Free-form fields are left alone, everything else is sanitized for CSS output.
     */
    public function testDesignOptionsForCss(): void
    {
        $options = Sanitize::designOptionsForCss([
            OptionKey::CUSTOM_CSS => 'a > b { color: red; }',
            OptionKey::LABEL_COLOR => 'red;}',
            OptionKey::LOGO_BACKGROUND_URL => "https://example.com/logo.png')",
        ]);
        $this->assertSame('a > b { color: red; }', $options[OptionKey::CUSTOM_CSS]);
        $this->assertSame('red', $options[OptionKey::LABEL_COLOR]);
        $this->assertSame('https://example.com/logo.png%27%29', $options[OptionKey::LOGO_BACKGROUND_URL]);
    }

    /**
     * Imports may only write the plugin's own options.
     */
    public function testImportPayloadOnlyAllowsPluginOptions(): void
    {
        $design = Factory::getSection(Factory::SECTION_DESIGN);
        $general = Factory::getSection(Factory::SECTION_GENERAL);
        $import = Sanitize::importPayload([
            $design => [OptionKey::LABEL_COLOR => '#000'],
            $general => [OptionKey::ACTIVE => 'on'],
            'active_plugins' => ['evil/evil.php'],
            'default_role' => 'administrator',
            'sections' => [],
            'fields' => [],
            Factory::PREFIX . 'not_a_section' => [],
        ]);
        $this->assertSame([$design, $general], array_keys($import));
    }

    /**
     * Users without `unfiltered_html` can't change the custom javascript, directly or through an import.
     */
    public function testCustomJavascriptRequiresUnfilteredHtml(): void
    {
        $design = Factory::getSection(Factory::SECTION_DESIGN);
        update_option($design, [OptionKey::CUSTOM_JQUERY => 'console.log("existing")']);
        wp_set_current_user(0);

        $this->assertSame('console.log("existing")', Sanitize::customJavascript('alert(document.cookie)'));
        $import = Sanitize::importPayload([$design => [OptionKey::CUSTOM_JQUERY => 'alert(document.cookie)']]);
        $this->assertSame('console.log("existing")', $import[$design][OptionKey::CUSTOM_JQUERY]);

        add_filter('user_has_cap', static function (array $allcaps): array {
            $allcaps['unfiltered_html'] = true;
            return $allcaps;
        });
        $this->assertSame('console.log("new")', Sanitize::customJavascript('console.log("new")'));
    }
}
