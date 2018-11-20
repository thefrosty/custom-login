<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin;

use Symfony\Component\HttpFoundation\Request;
use TheFrosty\WpUtilities\Plugin\AbstractHookProvider;
use TheFrosty\WpUtilities\Plugin\HttpFoundationRequestInterface;
use TheFrosty\WpUtilities\Plugin\HttpFoundationRequestTrait;

/**
 * Class AbstractCustomLogin
 * @package TheFrosty\CustomLogin
 */
abstract class AbstractHookRequestProvider extends AbstractHookProvider implements HttpFoundationRequestInterface
{
    use HttpFoundationRequestTrait;

    /**
     * AbstractCustomLogin constructor.
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->setRequest($request);
    }
}
