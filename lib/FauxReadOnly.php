<?php
/**
 * @license MIT
 * Copyright 2021, Dustin Wilson, J. King et al.
 * See LICENSE and AUTHORS files for details
 */

declare(strict_types=1);
namespace MensBeam\Framework;


trait FauxReadOnly {
    public function __get(string $name) {
        $prop = "_$name";
        if (!property_exists($this, $prop)) {
            $trace = debug_backtrace();
            set_error_handler(function($errno, $errstr) use($trace) {
                echo "PHP Notice:  $errstr in {$trace[0]['file']} on line {$trace[0]['line']}" . PHP_EOL;
            });
            trigger_error("Cannot get undefined property $name", \E_USER_NOTICE);
            restore_error_handler();
            return null;
        }

        return $this->$prop;
    }
}