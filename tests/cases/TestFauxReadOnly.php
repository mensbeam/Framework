<?php
/**
 * @license MIT
 * Copyright 2021, Dustin Wilson, J. King et al.
 * See LICENSE and AUTHORS files for details
 */

declare(strict_types=1);
namespace MensBeam\HTML\DOM\TestCase;

use MensBeam\Framework\{
    Exception,
    FauxReadOnly
};


/** @covers \MensBeam\Framework\FauxReadOnly */
class TestFauxReadOnly extends \PHPUnit\Framework\TestCase {
    public function testFailures(): void {
        // This is stupid, but I don't know of any other way to grab this as it already
        // uses its own error handler.
        ob_start();

        $ook = new class {
            use FauxReadOnly;
            protected ?string $_ook = 'ook';
        };
        $ook->ack;

        $this->assertTrue(strpos(ob_get_clean(), 'PHP Notice:  Cannot get undefined property ack') !== false);
    }


    public function testGet(): void {
        $ook = new class {
            use FauxReadOnly;
            protected ?string $_ook = 'ook';
        };
        $this->assertSame('ook', $ook->ook);
    }
}