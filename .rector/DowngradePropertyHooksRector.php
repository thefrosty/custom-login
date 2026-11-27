<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\PropertyHook;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Property;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use function count;
use function in_array;
use function is_array;
use function strtolower;

/**
 * Downgrade PHP 8.4 property hooks that only announce a deprecation before assigning the value, e.g.
 * symfony/http-foundation 8.1's `public InputBag $query { set { trigger_deprecation(...); $this->query = $value; } }`.
 * Removing such a hook keeps the property's behaviour (a plain typed property) and only drops the notice.
 * Any other hook (a `get` hook, or a `set` hook doing more than that) is left untouched, so it still fails the
 * PHP 7.4 lint instead of silently changing behaviour.
 */
final class DowngradePropertyHooksRector extends AbstractRector
{

    /**
     * Functions a `set` hook may call (only to report a deprecation) and still be removed.
     */
    private const array NOTICE_FUNCTIONS = ['trigger_deprecation', 'trigger_error'];

    /**
     * The implicit parameter of a `set` hook.
     */
    private const string DEFAULT_VALUE_PARAMETER = 'value';

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
        if (!$node instanceof Property || $node->hooks === [] || count($node->props) !== 1) {
            return null;
        }

        $property = $node->props[0]->name->toString();
        foreach ($node->hooks as $hook) {
            if (!$this->isNoticeAndAssignSetHook($hook, $property)) {
                return null;
            }
        }

        $node->hooks = [];

        return $node;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Remove property hooks that only trigger a deprecation before assigning the value',
            [
                new CodeSample(
                    'public Bag $bag { set { trigger_deprecation("x", "1.0", "Deprecated."); $this->bag = $value; } }',
                    'public Bag $bag;'
                ),
            ]
        );
    }

    /**
     * Whether the hook is `set { <notice calls>; $this->property = $value; }`.
     * @param PropertyHook $hook
     * @param string $property
     * @return bool
     */
    private function isNoticeAndAssignSetHook(PropertyHook $hook, string $property): bool
    {
        if (strtolower($hook->name->toString()) !== 'set' || !is_array($hook->body) || $hook->body === []) {
            return false;
        }

        $parameter = $hook->params[0]->var->name ?? self::DEFAULT_VALUE_PARAMETER;
        $statements = $hook->body;
        $last = array_pop($statements);
        foreach ($statements as $statement) {
            if (
                !$statement instanceof Expression ||
                !$statement->expr instanceof FuncCall ||
                !$statement->expr->name instanceof Name ||
                !in_array(strtolower($statement->expr->name->toString()), self::NOTICE_FUNCTIONS, true)
            ) {
                return false;
            }
        }

        return $last instanceof Expression &&
            $last->expr instanceof Assign &&
            $last->expr->var instanceof PropertyFetch &&
            $last->expr->var->var instanceof Variable &&
            $last->expr->var->var->name === 'this' &&
            $last->expr->var->name instanceof Identifier &&
            $last->expr->var->name->toString() === $property &&
            $last->expr->expr instanceof Variable &&
            $last->expr->expr->name === $parameter;
    }
}
