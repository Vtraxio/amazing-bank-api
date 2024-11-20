<?php

namespace Core;

use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;

/**
 * Static utility methods for reflection
 */
class ReflectorUtils {
    /**
     * Gets the reflection function for a callable, either a closure or a class method
     * @throws ReflectionException
     */
    static function getReflectionFunction(mixed $callable): ReflectionFunctionAbstract {
        if ($callable instanceof Closure) {
            return new ReflectionFunction($callable);
        } elseif (is_array($callable) && count($callable) === 2) {
            [$class, $method] = $callable;
            return new ReflectionMethod($class, $method);
        } else {
            throw new InvalidArgumentException("Unsupported callable type");
        }
    }
}
