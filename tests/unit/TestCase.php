<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Psr\Container\ContainerInterface;
use ReflectionMethod;
use ReflectionObject;
use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\PluginFactory;
use WPDieException;
use function add_filter;
use function dirname;
use function get_class;
use function remove_all_filters;
use function wp_insert_user;
use function wp_json_encode;
use function wp_set_current_user;

/**
 * Class TestCase
 * @package TheFrosty\Tests\CustomLogin
 */
class TestCase extends PhpUnitTestCase
{

    public const string METHOD_ADD_ACTION = 'addAction';
    public const string METHOD_ADD_FILTER = 'addFilter';

    protected ContainerInterface $container;
    protected Plugin $plugin;
    protected ReflectionObject $reflection;

    /**
     * Setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        // Set the filename to the root of the plugin (not the test plugin (so we have asset access without mocks)).
        $filename = dirname(__DIR__, 2) . '/custom-login.php';
        $this->plugin = PluginFactory::create('custom-login', $filename);
        $this->container = $this->plugin->getContainer();
        $_SERVER['REQUEST_URI'] ??= '/wp-admin/options-general.php';
        if (!$this->container->has(ServiceProvider::WP_UTILITIES_VIEW)) {
            $this->plugin->addServiceProvider(new ServiceProvider());
        }
    }

    /**
     * Tear down.
     */
    public function tearDown(): void
    {
        remove_all_filters('pre_http_request');
        remove_all_filters('wp_redirect');
        wp_set_current_user(0);
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

    /**
     * Call a (protected or private) method.
     * @param object $object
     * @param string $method
     * @param mixed ...$args
     * @return mixed
     */
    protected function invoke(object $object, string $method, mixed ...$args): mixed
    {
        return (new ReflectionMethod($object, $method))->invoke($object, ...$args);
    }

    /**
     * Get a (protected or private) property value.
     * @param object $object
     * @param string $property
     * @return mixed
     */
    protected function getProperty(object $object, string $property): mixed
    {
        return (new ReflectionObject($object))->getProperty($property)->getValue($object);
    }

    /**
     * Run every closure a HooksTrait object attached to WordPress (for hooks we can't fire globally, like `init`).
     * @param object $object
     * @param mixed ...$args
     */
    protected function runMappedHooks(object $object, mixed ...$args): void
    {
        foreach ($this->getProperty($object, 'filter_map') as $closure) {
            $closure(...$args);
        }
    }

    /**
     * Run only the closures a HooksTrait object attached to a specific hook (without firing it globally).
     * @param object $object
     * @param string $hook
     * @param mixed ...$args
     * @return int The number of callbacks run.
     */
    protected function runMappedHook(object $object, string $hook, mixed ...$args): int
    {
        $count = 0;
        foreach ($this->getProperty($object, 'filter_map') as $closure) {
            if (has_filter($hook, $closure) !== false) {
                $closure(...$args);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Create an administrator and make them the current user.
     * @return int
     */
    protected function loginAdmin(): int
    {
        static $user_id;
        if (!$user_id) {
            $user_id = wp_insert_user([
                'user_login' => 'custom_login_admin',
                'user_pass' => wp_generate_password(),
                'user_email' => 'custom-login-admin@example.com',
                'role' => 'administrator',
            ]);
        }
        wp_set_current_user($user_id);

        return $user_id;
    }

    /**
     * Short-circuit HTTP requests with a JSON response.
     * @param mixed $body
     * @param int $code
     */
    protected function fakeHttp(mixed $body, int $code = 200): void
    {
        add_filter('pre_http_request', static fn(): array => [
            'headers' => [],
            'body' => wp_json_encode($body),
            'response' => ['code' => $code, 'message' => 'OK'],
            'cookies' => [],
            'filename' => null,
        ]);
    }

    /**
     * Throw a RedirectException instead of redirecting (code under test calls `exit` after redirecting).
     */
    protected function catchRedirects(): void
    {
        add_filter('wp_redirect', static function (string $location): never {
            throw new RedirectException($location);
        });
    }

    /**
     * Make `wp_die()` throw during AJAX requests too (the bootstrap only replaces the default handler).
     */
    protected function throwOnAjaxDie(): void
    {
        add_filter('wp_doing_ajax', '__return_true');
        add_filter('wp_die_ajax_handler', static fn(): callable => static function (): never {
            throw new WPDieException();
        });
    }

    /**
     * Restore AJAX handling.
     */
    protected function resetAjax(): void
    {
        remove_all_filters('wp_doing_ajax');
        remove_all_filters('wp_die_ajax_handler');
    }

    /**
     * Capture output of a callback.
     * @param callable $callback
     * @return string
     */
    protected function capture(callable $callback): string
    {
        ob_start();
        try {
            $callback();
        } finally {
            $output = (string)ob_get_clean();
        }

        return $output;
    }
}
