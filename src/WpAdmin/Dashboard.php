<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\WpAdmin;

use TheFrosty\WpUtilities\Api\WpRemote;
use TheFrosty\WpUtilities\WpAdmin\Dashboard\Widget;
use TheFrosty\WpUtilities\WpAdmin\DashboardWidget;
use function esc_html__;
use function printf;
use const WEEK_IN_SECONDS;

/**
 * Class Dashboard
 * @package TheFrosty\CustomLogin\WpAdmin
 */
class Dashboard extends DashboardWidget
{

    use WpRemote;

    /**
     * Build args for the dashboard widget.
     * @return string[]
     */
    public static function getArgs(): array
    {
        return [
            Widget::FEED_URL => 'https://frosty.media/wp-json/wp/v2/posts?per_page=3',
            Widget::TYPE => Widget::TYPE_REST,
            Widget::WIDGET_ID => 'frosty-media-feed',
            Widget::WIDGET_NAME => 'Frosty Media Feed',
        ];
    }

    /**
     * Add class hooks.
     */
    public function addHooks(): void
    {
        parent::addHooks();
        $this->addAction(DashboardWidget::HOOK_NAME_RENDER, [$this, 'renderWidget'], 10, 3);
    }

    /**
     * Render additional content to the widget.
     * @param string $div_open
     * @param string $div_close
     * @param string $template
     */
    protected function renderWidget(string $div_open, string $div_close, string $template): void
    {
        echo '<hr>';
        echo $div_open;
        printf('<h4>%s</h4>', esc_html__('Custom Login Extensions', 'custom-login'));
        $posts = $this->retrieveBodyCached(
            'https://frosty.media/wp-json/wp/v2/extensions?per_page=1&plugin_tag=29',
            WEEK_IN_SECONDS
        );
        $renderContent = false;
        include $template;
        echo $div_close;
    }
}
