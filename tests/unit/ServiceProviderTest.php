<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\WpUtilities\Plugin\Container;
use TheFrosty\WpUtilities\Utils\View;

/**
 * Class ServiceProviderTest
 * @package TheFrosty\CustomLogin\Tests
 */
#[CoversClass(ServiceProvider::class)]
class ServiceProviderTest extends TestCase
{

    /**
     * Test register().
     */
    public function testRegister(): void
    {
        $container = new Container();
        (new ServiceProvider())->register($container);
        $this->assertTrue($container->has(ServiceProvider::WP_UTILITIES_VIEW));
        $view = $container->get(ServiceProvider::WP_UTILITIES_VIEW);
        $this->assertInstanceOf(View::class, $view);
        $this->assertNotNull($view->get('wp-login/style'));
    }
}
