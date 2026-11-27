<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Trait_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function in_array;

/**
 * Downgrade PHP 8.2 constants in traits (e.g. wp-utilities 4's `Api\Hash::KEY_CONSTANT`) by inlining
 * **private** trait constants where the trait uses them (`self::NAME` / `static::NAME`) and removing them.
 * Public and protected trait constants can be read through the using classes, so they are left untouched
 * (and still fail the PHP 7.4 lint instead of silently breaking those reads).
 */
final class DowngradeTraitConstantRector extends AbstractRector
{

    /**
     * Class references that point at the trait's own constants.
     */
    private const array SELF_REFERENCES = ['self', 'static'];

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Trait_::class];
    }

    /**
     * @param Node $node A Trait_ (see getNodeTypes()).
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Trait_) {
            return null;
        }

        /** @var array<string, Expr> $values */
        $values = [];
        foreach ($node->stmts as $key => $statement) {
            if (!$statement instanceof ClassConst || !$statement->isPrivate()) {
                continue;
            }
            foreach ($statement->consts as $const) {
                $values[$const->name->toString()] = $const->value;
            }
            unset($node->stmts[$key]);
        }

        if ($values === []) {
            return null;
        }

        $node->stmts = array_values($node->stmts);
        $this->traverseNodesWithCallable($node->stmts, static function (Node $fetch) use ($values): ?Expr {
            if (
                !$fetch instanceof ClassConstFetch ||
                !$fetch->class instanceof Name ||
                !in_array($fetch->class->toLowerString(), self::SELF_REFERENCES, true) ||
                !$fetch->name instanceof Identifier ||
                !isset($values[$fetch->name->toString()])
            ) {
                return null;
            }

            return clone $values[$fetch->name->toString()];
        });

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Inline private trait constants and remove them',
            [
                new CodeSample(
                    'trait Hash { private const KEY = "x"; public function key() { return self::KEY; } }',
                    'trait Hash { public function key() { return "x"; } }'
                ),
            ]
        );
    }
}
