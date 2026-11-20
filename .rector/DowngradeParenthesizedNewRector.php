<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticPropertyFetch;
use PhpParser\Node\Expr\Variable;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Remove the parentheses PHP 8.0 allows around a `new` class expression: `new ($class)($args)`.
 * The syntax tree is the same as `new $class($args)`, so no core downgrade rule changes it and Rector's
 * format-preserving printer keeps the parentheses, a parse error on PHP 7.4
 * (e.g. blakvghost/php-validator's `Validator::resolveRule()`).
 * Only variable-like class expressions are rewritten, since those are valid unparenthesized on PHP 7.4.
 */
final class DowngradeParenthesizedNewRector extends AbstractRector
{

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [New_::class];
    }

    /**
     * @param Node $node A New_ (see getNodeTypes()).
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (
            !$node instanceof New_ ||
            !(
                $node->class instanceof Variable ||
                $node->class instanceof PropertyFetch ||
                $node->class instanceof ArrayDimFetch ||
                $node->class instanceof StaticPropertyFetch
            )
        ) {
            return null;
        }

        // `new $class` has the class expression exactly 4 characters (`new `) after the start, anything else
        // means parentheses (or unusual spacing, where reprinting is harmless). Nodes created by this rule have
        // no positions, so they're never rewritten twice.
        $start = $node->getStartFilePos();
        $classStart = $node->class->getStartFilePos();
        if ($start < 0 || $classStart < 0 || $classStart - $start === 4) {
            return null;
        }

        // A new node has no original tokens, so it's printed fresh (without the parentheses).
        return new New_($node->class, $node->args);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove parentheses around a variable class name in a new expression',
            [
                new CodeSample(
                    'return new ($ruleClass)($parameters);',
                    'return new $ruleClass($parameters);'
                ),
            ]
        );
    }
}
