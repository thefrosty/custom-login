<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Tests;

use RuntimeException;

/**
 * Thrown from the `wp_redirect` filter in tests, so code that redirects and then calls `exit` can be tested.
 * @package TheFrosty\CustomLogin\Tests
 */
class RedirectException extends RuntimeException
{

    /**
     * RedirectException constructor.
     * @param string $location
     */
    public function __construct(public readonly string $location)
    {
        parent::__construct(sprintf('Redirected to %s', $location));
    }
}
