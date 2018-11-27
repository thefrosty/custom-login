<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings\Api;

use Dwnload\WpSettingsApi\App;
use Dwnload\WpSettingsApi\Settings\SectionManager;
use TheFrosty\WpUtilities\Plugin\HooksTrait;
use TheFrosty\WpUtilities\Plugin\PluginAwareInterface;
use TheFrosty\WpUtilities\Plugin\PluginAwareTrait;
use TheFrosty\WpUtilities\Plugin\WpHooksInterface;

/**
 * Class AbstractWpSettingsApi
 *
 * @package TheFrosty\CustomLogin\Settings\Api
 */
abstract class AbstractWpSettingsApi implements WpHooksInterface, PluginAwareInterface
{
    use HooksTrait, PluginAwareTrait;

    /**
     * App object.
     * @var App $app
     */
    private $app;

    /**
     * Sections array.
     * @var array $sections
     */
    private static $sections = [];

    /**
     * AbstractWpSettingsApi constructor.
     * @param App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * Get the section array for our counters.
     * @throws \Exception
     */
    protected function setSections()
    {
        if (!\did_action(App::ACTION_PREFIX . 'settings_page_loaded')) {
            throw new \Exception(
                \sprintf(
                    'Calling this method should be done on action hook `%s`.',
                    App::ACTION_PREFIX . 'settings_page_loaded'
                )
            );
        }
        self::$sections = self::$sections ?: SectionManager::getSections($this->app->getMenuSlug());
    }

    /**
     * Create a postbox widget.
     *
     * @param string $id ID of the postbox.
     * @param string $title Title of the postbox.
     * @param string $content Content of the postbox.
     */
    protected function postbox($id, $title, $content): void
    {
        echo '<li class="metabox-holder" id="' . \esc_attr($id) . '">';
        echo '<div class="postbox">';
        echo '<h3>' . \esc_html($title) . '</h3>';
        echo '<div class="inside">' . \wp_kses_post($content) . '</div>';
        echo '</div>';
        echo '</li>';
    }

    /**
     * Return the content from a settings template.
     * @param string $template
     * @return string
     */
    protected function getContent(string $template): string
    {
        ob_start();
        include $this->getPlugin()->getDirectory() . 'templates/settings/' . $template;
        return ob_get_clean();
    }

    /**
     * Return a auto-incrementing integer count.
     * @return int
     */
    protected function getPriority(): int
    {
        static $count;
        $sections = \count(self::$sections);
        $count += ++$sections * 2 + 2;
        return \absint($count);
    }
}
