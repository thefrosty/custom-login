<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Api;

/**
 * Trait Activator
 * @package TheFrosty\CustomLogin\Api
 */
trait Activator
{

    abstract public function activate(): void;
}
