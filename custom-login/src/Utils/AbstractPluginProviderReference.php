<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Utils;

use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class AbstractPluginProviderReference
 *
 * @package TheFrosty\CustomLogin\Api
 */
abstract class AbstractPluginProviderReference implements WpHooksInterface
{
    use HooksTrait, ReflectionTrait;

    /**
     * Plugin object passed by reference, since
     * this isn't initiated until a later hook by calling initiate().
     * @var Plugin $plugin
     */
    private $plugin;

    /**
     * AbstractPluginProvider constructor.
     * @param Plugin $plugin
     */
    public function __construct(Plugin &$plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Registers hooks for the plugin.
     */
    abstract public function addHooks();

    /**
     * Return the Plugin object passed by reference.
     * @return Plugin
     */
    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }

    /**
     * @param string $class_name
     * @return null|WpHooksInterface
     */
    public function getWpHookObject(string $class_name)
    {
        $instance = null;
        $wp_hooks = $this->getPlugin()->getInit()->getWpHooks();
        \array_walk(
            $wp_hooks,
            function (WpHooksInterface $object, int $key) use (&$wp_hooks, &$instance, $class_name) {
                if (($object instanceof WpHooksInterface) && \get_class($object) === $class_name) {
                    $instance = $wp_hooks[$key];
                }
            }
        );

        return $instance;
    }

    /**
     * Helper to get the PSR4 class name.
     * @param string $class_name
     * @return string
     */
    protected function getClassName(string $class_name): string
    {
        return \str_replace(['_', '-'], '', \ucwords($class_name, '_-'));
    }
}
