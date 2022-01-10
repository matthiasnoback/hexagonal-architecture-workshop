<?php

declare(strict_types=1);

namespace Utils\PHPStan;

use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Psr\Container\ContainerInterface;

final class ContainerGetReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function __construct()
    {
    }

    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        if (isset($methodCall->args[0])) {
            /** @var Arg $argument */
            $argument = $methodCall->args[0];

            if ($argument->value instanceof ClassConstFetch) {
                /** @var ClassConstFetch $constantFetch */
                $constantFetch = $argument->value;

                $class = $constantFetch->class;

                if ($class instanceof FullyQualified) {
                    return new ObjectType($class->toString());
                }
            }
        }

        /** @var Arg[] $arguments */
        $arguments = $methodCall->args;

        // By default, return the return type of ContainerInterface::get()
        return ParametersAcceptorSelector::selectFromArgs(
            $scope,
            $arguments,
            $methodReflection->getVariants()
        )->getReturnType();
    }
}
