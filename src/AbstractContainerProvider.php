<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use TheFrosty\WpUtilities\Plugin\ContainerAwareTrait;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\PluginAwareInterface;
use TheFrosty\WpUtilities\Plugin\PluginAwareTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class AbstractContainerProvider
 * @package TheFrosty\CustomLogin
 */
abstract class AbstractContainerProvider implements WpHooksInterface, PluginAwareInterface
{
    use ContainerAwareTrait, HooksTrait, PluginAwareTrait;

    /**
     * AbstractContainerProvider constructor.
     * @param Container|null $container Set the container, or use `$this->setContainer($container)`.
     */
    public function __construct(?Container $container = null)
    {
        if ($container) {
            $this->setContainer($container);
        }
    }

    /**
     * Registers hooks for the plugin.
     */
    abstract public function addHooks(): void;
}
