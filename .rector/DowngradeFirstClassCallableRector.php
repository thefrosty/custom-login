<?php

declare(strict_types=1);

namespace TheFrosty\CustomLogin\Rector;

use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\ArrayItem;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Downgrade PHP 8.1 first-class callable syntax to `\Closure::fromCallable()` (PHP 7.1+).
 * `Closure::fromCallable()` is called from the same scope, so private/protected methods keep working.
 *  - `strlen(...)` becomes `\Closure::fromCallable('strlen')`
 *  - `$this->method(...)` becomes `\Closure::fromCallable([$this, 'method'])`
 *  - `Foo::method(...)` becomes `\Closure::fromCallable([Foo::class, 'method'])`
 */
final class DowngradeFirstClassCallableRector extends AbstractRector
{

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [FuncCall::class, MethodCall::class, StaticCall::class];
    }

    /**
     * @param Node $node A FuncCall, MethodCall or StaticCall (see getNodeTypes()).
     * @return Node|null
     */
    public function refactor(Node $node): ?Node
    {
        if (
            !($node instanceof FuncCall || $node instanceof MethodCall || $node instanceof StaticCall) ||
            !$node->isFirstClassCallable()
        ) {
            return null;
        }

        $callable = $this->getCallable($node);
        if ($callable === null) {
            return null;
        }

        return new StaticCall(new FullyQualified('Closure'), 'fromCallable', [new Arg($callable)]);
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Downgrade first-class callable syntax to Closure::fromCallable()',
            [
                new CodeSample(
                    'array_map($this->resolve(...), $items);',
                    'array_map(\Closure::fromCallable([$this, \'resolve\']), $items);'
                ),
            ]
        );
    }

    /**
     * Build the callable expression for `Closure::fromCallable()`.
     * @param Node $node A FuncCall, MethodCall or StaticCall.
     * @return Expr|null
     */
    private function getCallable(Node $node): ?Expr
    {
        if ($node instanceof FuncCall) {
            if ($node->name instanceof Expr) {
                return $node->name;
            }
            $name = $this->getName($node) ?? $node->name->toString();

            return new String_(ltrim($name, '\\'));
        }

        $method = $node->name instanceof Identifier ? new String_($node->name->toString()) : $node->name;
        if ($node instanceof MethodCall) {
            return new Array_([new ArrayItem($node->var), new ArrayItem($method)]);
        }

        if ($node->class instanceof Name && $node->class->toLowerString() === 'parent') {
            // `parent::method(...)`, the callable string form keeps the parent scope.
            return $method instanceof String_ ? new String_('parent::' . $method->value) : null;
        }

        $class = $node->class instanceof Name ? new ClassConstFetch($node->class, 'class') : $node->class;

        return new Array_([new ArrayItem($class), new ArrayItem($method)]);
    }
}
