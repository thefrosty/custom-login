<?php declare(strict_types=1);

namespace TheFrosty\CustomLogin\Utils;

/**
 * Trait ReflectionTrait
 * @package TheFrosty\CustomLogin\Utils
 */
trait ReflectionTrait
{
    /**
     * Gets an instance of the \ReflectionObject.
     * @param object $argument
     * @return \ReflectionObject
     * phpcs:disable Inpsyde.CodeQuality.ArgumentTypeDeclaration.NoArgumentType
     */
    protected function getReflection($argument): \ReflectionObject
    {
        // phpcs:enable
        static $reflector;

        if (!($reflector instanceof \ReflectionObject)) {
            $reflector = new \ReflectionObject($argument);
        }

        return $reflector;
    }
}
