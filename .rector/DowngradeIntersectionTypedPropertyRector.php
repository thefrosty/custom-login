<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\UnionType;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Remove PHP 8.1 intersection (and PHP 8.2 DNF) types from typed properties, which PHP 7.4 can't parse.
 * Rector's downgrade sets handle intersection types on parameters and return types, but not on properties
 * (e.g. symfony/http-foundation's `private \SessionHandlerInterface&\SessionUpdateTimestampHandlerInterface $x`).
 */
final class DowngradeIntersectionTypedPropertyRector extends AbstractRector
{

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Property::class];
    }

    /**
     * @param Node $node A Property (see getNodeTypes()).
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Property || !$this->hasIntersection($node->type)) {
            return null;
        }

        $node->type = null;

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove intersection types from typed properties',
            [
                new CodeSample(
                    'private A&B $handler;',
                    'private $handler;'
                ),
            ]
        );
    }

    /**
     * Whether the type is, or contains (DNF), an intersection type.
     * @param Node|null $type
     * @return bool
     */
    private function hasIntersection(?Node $type): bool
    {
        if ($type instanceof IntersectionType) {
            return true;
        }
        if ($type instanceof UnionType) {
            foreach ($type->types as $subType) {
                if ($subType instanceof IntersectionType) {
                    return true;
                }
            }
        }

        return false;
    }
}
