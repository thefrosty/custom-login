<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests\Settings\Api;

use PHPUnit\Framework\Attributes\CoversClass;
use TheFrosty\CustomLogin\CustomLogin;
use TheFrosty\CustomLogin\Settings\Api\Factory;
use TheFrosty\CustomLogin\Tests\TestCase;

/**
 * Class FactoryTest
 * @package TheFrosty\CustomLogin\Tests\Settings\Api
 */
#[CoversClass(Factory::class)]
class FactoryTest extends TestCase
{

    /**
     * Test getPluginSettings().
     */
    public function testGetPluginSettings(): void
    {
        $settings = Factory::getPluginSettings($this->plugin);
        $this->assertSame('custom-login', $settings->getMenuSlug());
        $this->assertSame(Factory::PREFIX, $settings->getPrefix());
        $this->assertSame(CustomLogin::VERSION, $settings->getVersion());
    }

    /**
     * Test getSection().
     */
    public function testGetSection(): void
    {
        $this->assertSame('custom_login_design', Factory::getSection(Factory::SECTION_DESIGN));
    }
}
