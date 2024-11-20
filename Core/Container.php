<?php

namespace Core;

use Exception;
use ReflectionClass;

/**
 * Holds global instances of classes that can be accessed from anywhere
 * @see bootstrap.php
 */
class Container {
    private array $bindings = [];
    private array $initialized = [];

    public function bind($key, $resolver): void {
        $this->bindings[$key] = $resolver;
    }

    /**
     * @template T
     * @param T $key
     * @return T
     * @throws Exception
     */
    public function resolve($key) {
        if (!array_key_exists($key, $this->bindings)) {
            throw new Exception("No matching binding found for $key");
        }

        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }

    /**
     * @template T
     * @param T $class
     * @return T
     * @throws Exception
     */
    public function newClass($class) {
        $reflectionClass = new ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();

        $parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $type = $parameter->getType();
            if ($type && !$type->isBuiltin()) {
                $dependencyClass = $type->getName();
                $dependencies[] = $this->resolve($dependencyClass);
            } else {
                throw new Exception("Cannot resolve dependency {$parameter->getName()} in class {$class}");
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}