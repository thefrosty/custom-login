<?php declare (strict_types=1);

namespace TheFrosty\CustomLogin;

use Psr\Container\ContainerInterface;
use TheFrosty\WpUtilities\Plugin\Container;
use TheFrosty\WpUtilities\Plugin\Plugin;
use TheFrosty\WpUtilities\Plugin\PluginFactory;

const TAG_LOAD = 'custom_login_load';
const TAG_LOADED = 'custom_login_loaded';

$plugin = PluginFactory::create(SLUG);
/** @var Container $container */
$container = $plugin->getContainer();
$container->register(new ServiceProvider($plugin));

\add_action('plugins_loaded', function () use ($container, $plugin) {
    /**
     * Start loading Custom Login.
     * @since 4.0.0
     * @param Plugin $plugin plugin instance.
     * @param ContainerInterface $container Dependency container.
     */
    \do_action(TAG_LOAD, $plugin, $container);

    $plugin->add(new CustomLogin($plugin))->initialize();

    /**
     * Finished loading Custom Login.
     * @since 4.0.0
     * @param Plugin $plugin plugin instance.
     * @param ContainerInterface $container Dependency container.
     */
    \do_action(TAG_LOADED, $plugin, $container);
}, 5);

unset($container, $plugin);
