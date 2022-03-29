<?php declare(strict_types=1);

namespace TheFrosty\Tests\CustomLogin;

use PHPUnit\Framework\MockObject\MockObject;
use ReflectionObject;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\PluginFactory;
use function get_class;

/**
 * Class TestCase
 * @package TheFrosty\Tests\CustomLogin
 */
class TestCase extends \WP_UnitTestCase
{

    public const METHOD_ADD_FILTER = 'addFilter';

    /** @var \TheFrosty\WpUtilities\Plugin\Container $container */
    protected $container;

    /** @var Plugin $plugin */
    protected Plugin $plugin;

    /** @var ReflectionObject $reflection */
    protected ReflectionObject $reflection;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Set the filename to the root of the plugin (not the test plugin (so we have asset access without mocks)).
        $filename = \dirname(__DIR__, 2) . '/custom-login.php';
        $this->plugin = PluginFactory::create('custom-login', $filename);
        $this->container = $this->plugin->getContainer();
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        unset($this->container, $this->plugin, $this->reflection);
        parent::tearDown();
    }

    /**
     * Gets an instance of the \ReflectionObject.
     * @param object $argument
     * @return ReflectionObject
     */
    protected function getReflection(object $argument): ReflectionObject
    {
        static $reflector;

        if (!isset($reflector[get_class($argument)]) ||
            !($reflector[get_class($argument)] instanceof ReflectionObject)
        ) {
            $reflector[get_class($argument)] = new ReflectionObject($argument);
        }

        return $reflector[get_class($argument)];
    }

    /**
     * Get a Mock Provider.
     * @param string $className
     * @return MockObject
     */
    protected function getMockProvider(string $className): MockObject
    {
        return $this->getMockBuilder($className)
            ->onlyMethods([self::METHOD_ADD_FILTER])
            ->getMock();
    }
}
