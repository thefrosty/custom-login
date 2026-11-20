<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Downgrade enum case property fetches, to pair with Rector's `DowngradeEnumToConstantListClassRector`.
 *
 * That rule turns `enum Type: string { case TEXT = 'text'; }` into `class Type { public const TEXT = 'text'; }`
 * but leaves usages like `Type::TEXT->value` untouched, which is a parse error on PHP < 8.0
 * (and wrong on 8.x once `Type` is a class). Class constants can't hold objects, so a
 * `Foo::BAR->value` / `Foo::BAR->name` fetch only ever makes sense on an enum case:
 *  - `Foo::BAR->value` becomes `Foo::BAR` (the backed value the constant now holds).
 *  - `Foo::BAR->name` becomes `'BAR'`.
 */
final class DowngradeEnumCaseFetchRector extends AbstractRector
{

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [PropertyFetch::class];
    }

    /**
     * @param Node $node A PropertyFetch (see getNodeTypes()).
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (
            !$node instanceof PropertyFetch ||
            !$node->var instanceof ClassConstFetch ||
            !$node->var->class instanceof Name ||
            !$node->var->name instanceof Identifier ||
            !$node->name instanceof Identifier
        ) {
            return null;
        }

        return match ($node->name->toString()) {
            'value' => $node->var,
            'name' => new String_($node->var->name->toString()),
            default => null,
        };
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Downgrade enum case ->value / ->name fetches to the constant list class',
            [
                new CodeSample(
                    'esc_attr(Type::TEXT->value);',
                    'esc_attr(Type::TEXT);'
                ),
            ]
        );
    }
}
