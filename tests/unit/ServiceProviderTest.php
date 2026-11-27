<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use DI\Container as DiContainer;
use PHPUnit\Framework\Attributes\CoversClass;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\WpUtilities\Plugin\DefaultServiceProvider;
use TheFrosty\WpUtilities\Plugin\PluginFactory;
use TheFrosty\WpUtilities\Utils\View;

/**
 * Class ServiceProviderTest
 * @package TheFrosty\CustomLogin\Tests
 */
#[CoversClass(ServiceProvider::class)]
class ServiceProviderTest extends TestCase
{

    /**
     * The view entry keeps its v5 ID.
     */
    public function testViewId(): void
    {
        $this->assertSame(DefaultServiceProvider::VIEW, ServiceProvider::WP_UTILITIES_VIEW);
    }

    /**
     * The plugin uses PHP-DI, and the shared View loads Custom Login's templates.
     */
    public function testAddServiceProvider(): void
    {
        $plugin = PluginFactory::create('custom-login-service-provider-test', dirname(__DIR__, 2) . '/custom-login.php');
        $this->assertInstanceOf(DiContainer::class, $plugin->getContainer());

        $plugin->addServiceProvider(new ServiceProvider());
        $this->assertTrue($plugin->getContainer()->has(ServiceProvider::WP_UTILITIES_VIEW));
        $view = $plugin->getContainer()->get(ServiceProvider::WP_UTILITIES_VIEW);
        $this->assertInstanceOf(View::class, $view);
        $this->assertNotNull($view->get('wp-login/style'));
        $this->assertSame($view, $plugin->getContainer()->get(ServiceProvider::WP_UTILITIES_VIEW), 'shared entry');
    }
}
