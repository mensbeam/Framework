<?php
/**
 * @license MIT
 * Copyright 2021, Dustin Wilson, J. King et al.
 * See LICENSE and AUTHORS files for details
 */

declare(strict_types=1);
namespace MensBeam\Framework;


trait MagicProperties {
    public function __get(string $name) {
        $methodName = $this->getMagicPropertyMethodName($name);
        if ($methodName === null) {
            throw new Exception(Exception::NONEXISTENT_PROPERTY, $name);
        }
        return call_user_func([ $this, $methodName ]);
    }

    public function __isset(string $name): bool {
        return ($this->getMagicPropertyMethodName($name) !== null);
    }

    public function __set(string $name, $value) {
        $methodName = $this->getMagicPropertyMethodName($name, false);
        if ($methodName !== null) {
            call_user_func([ $this, $methodName ], $value);
            return;
        }

        if ($this->getMagicPropertyMethodName($name) !== null) {
            throw new Exception(Exception::READONLY_PROPERTY, $name);
        } else {
            throw new Exception(Exception::NONEXISTENT_PROPERTY, $name);
        }
    }

    public function __unset(string $name) {
        $methodName = $this->getMagicPropertyMethodName($name, false);
        if ($methodName === null) {
            throw new Exception(Exception::READONLY_PROPERTY, $name);
        }

        call_user_func([ $this, $methodName ], null);
    }


    // Method_exists is case-insensitive because methods are case-insensitive in
    // PHP. Properties in PHP 8 are sensitive, so let's use reflection to check
    // against the actual name to get a case sensitive match like methods should be!
    private function getMagicPropertyMethodName(string $name, bool $get = true): ?string {
        static $protectedMethodsList = [];

        $methodName = "__" . (($get) ? 'get' : 'set') . "_{$name}";
        if (method_exists($this, $methodName)) {
            if (!isset($protectedMethodsList[$this::class])) {
                $reflector = new \ReflectionClass($this);
                // Magic property methods are protected
                $methodsList = $reflector->getMethods(\ReflectionMethod::IS_PROTECTED);
                $temp = [];
                $valid = false;
                // Only cache the magic methods
                foreach ($methodsList as $m) {
                    if (str_starts_with($m->name, '__get_') || str_starts_with($m->name, '__set_')) {
                        $temp[] = $m->name;

                        if (!$valid && $m->name === $methodName) {
                            $valid = true;
                        }
                    }
                }

                $protectedMethodsList[$this::class] = $temp;
                return ($valid) ? $methodName : null;
            }

            return (in_array($methodName, $protectedMethodsList[$this::class])) ? $methodName : null;
        }

        return null;
    }
}