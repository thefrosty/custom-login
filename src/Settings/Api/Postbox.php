<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Settings\Api;

use TheFrosty\CustomLogin\ServiceProvider;
use TheFrosty\WpUtilities\Utils\Viewable;

/**
 * Trait Postbox
 * @package TheFrosty\CustomLogin\Settings\Api
 */
trait Postbox
{
    use Viewable;

    /**
     * Render the postbox widget.
     * @param string $id ID of the postbox.
     * @param string $title Title of the postbox.
     * @param string $content Content of the postbox.
     */
    protected function postbox(string $id, string $title, string $content): void
    {
        $this->getView(ServiceProvider::WP_UTILITIES_VIEW)->render(
            'postbox',
            ['id' => $id, 'title' => $title, 'content' => $content]
        );
    }
}
